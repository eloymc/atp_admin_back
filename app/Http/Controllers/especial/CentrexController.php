<?php

namespace App\Http\Controllers\especial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\SaaioPedimeModel;
use \App\Models\ClienteModel;
use \App\Models\TraficoModel;
use DB;

class CentrexController extends Controller
{
    public $ids_clientes_ventas = array(
        2267, // L OREAL SLP, S.A. DE C.V.
        2248, // FRABEL, S.A. DE C.V.
        229,  // COSBEL, S.A. DE C.V.
    );
    public $oficinas = array(
        'aereo' => "Aereo",
        'altamira' => "Altamira",
        'laredo' => "Laredo",
        'lazaro' => "Lazaro",
        'jalisco' => "Jalisco",
        'guadalajara' => "Guadalajara",
        'pacifico' => "Pacifico",
        'matamoros' => "Matamoros",
        'tijuana' => "Tijuana",
        'toluca' => "Toluca",
        'queretaro2' => "Queretaro",
        'veracruz' => "Veracruz",
        'vallejo' => "México"
    );
    public $registros = array();
    public $rango_fecha_consulta = array();
    public $conteos = array("referencias"=>array(),"guias"=>array(), "guias_frabel"=>array(), "guias_contenedores"=>array());
    public $reglas = array(
        "contenedores"=>array(
            array( "de"=>0,"a"=>0,"importe"=>2000 ),
            array( "de"=>1,"a"=>4,"importe"=>5500 ),
            array( "de"=>5,"a"=>8,"importe"=>9600 ),
            array( "de"=>9,"a"=>12,"importe"=>13200 ),
            array( "de"=>13,"a"=>20,"importe"=>15000 ),
            array( "de"=>21,"a"=>1000000,"importe"=>20000 ),
        )
    );

    public function get(Request $req){
        if($req->month && $req->year){
            $this->rango_fecha_consulta[0] = $req->year."-".$req->month."-01";
            $this->rango_fecha_consulta[1] = $req->year."-".$req->month."-".date('t',strtotime($this->rango_fecha_consulta[0]));
        }else{
            $this->rango_fecha_consulta = array(date("Y-m-d"),date("Y-m-d"));
        }
        $this->consultas();
        $this->agregarImportes();
        $this->registros['conteos'] = $this->conteos;

        return response()->json($this->registros,200);
    }

    public function consultas(){
        foreach($this->oficinas as $schema => $oficina){
            $ids_clientes = (new ClienteModel)->setSchema($schema)
                ->whereIn('id_cliente_ventas',$this->ids_clientes_ventas)
                ->pluck('id');

            $referencias = (new SaaioPedimeModel)->setSchema($schema)
                ->conFechaPago($this->rango_fecha_consulta)
                //->conFechaPagoMes($month, $year)
                ->exportaciones()
                ->whereIn('cve_impo',$ids_clientes)
                ->quitarRectificaciones()
                ->pluck('num_refe');
            
            $trafico = (new TraficoModel)->setSchema($schema)
                ->whereIn('trafico.referencia',$referencias)->distinct()
                ->leftJoin($schema.'.cliente','cliente.id','trafico.cliente')
                ->leftJoin('central.division',function($query){
                    $query->on('trafico.division','division.id_division');
                    $query->on('cliente.id_cliente_ventas','division.id_cliente_ventas');
                })
                ->leftJoin($schema.'.guia',function($query){
                    $query->on('guia.referencia','trafico.referencia');
                    $query->on('guia.tipo',DB::raw("'M'"));
                })
                ->leftJoin($schema.'.contenedor',function($query){
                    $query->on('contenedor.referencia','trafico.referencia');
                })
                ->leftJoin($schema.'.saaio_pedime',function($query){
                    $query->on('saaio_pedime.num_refe','trafico.referencia');
                })
                ->select('trafico.referencia','cliente.nombre_cliente','division.nombre_division','guia.guia','contenedor.numero_contenedor','saaio_pedime.num_pedi')
                ->where(DB::raw("substr(division.nombre_division,1,3)"),'IDC')
                ->orderBy('guia.guia')
                ->orderBy('trafico.referencia')
                ->get();
            //if($schema == "lazaro") dd($trafico);
            $nueva_referencia = $nueva_guia = "";
            $no_nueva_referencia = $no_nueva_guia = 0;
            foreach($trafico as $registro){

                if($nueva_referencia != $registro->referencia){
                    $no_nueva_referencia = 1;
                    $nueva_referencia = $registro->referencia;
                }else{
                    $no_nueva_referencia ++;
                }
                $registro->no_referencia = $no_nueva_referencia;

                if($nueva_guia != $registro->guia){
                    $no_nueva_guia = 1;
                    $nueva_guia = $registro->guia;
                }else{
                    $no_nueva_guia ++;
                }
                $registro->no_guia = $no_nueva_guia;

                if(!isset($this->conteos['referencias'][$registro->referencia])){
                    $this->conteos['referencias'][$registro->referencia] = 1;
                }else{
                    $this->conteos['referencias'][$registro->referencia]++ ;
                }
                if($registro->guia){
                    $this->conteos['guias_frabel'][$registro->guia] = 0;
                    if(!isset($this->conteos['guias'][$registro->guia])){
                        $this->conteos['guias'][$registro->guia] = 1;
                    }else{
                        $this->conteos['guias'][$registro->guia]++ ;
                    }
                    if(!isset($this->registros[$schema])) $this->registros[$schema] = array() ;
                    if($registro->nombre_cliente == 'FRABEL, S.A. DE C.V.'){
                        if(!isset($this->conteos['guias_frabel'][$registro->guia])){
                            $this->conteos['guias_frabel'][$registro->guia] = 1;
                        }else{
                            $this->conteos['guias_frabel'][$registro->guia]++ ;
                        }
                    }
                }else{
                    $this->conteos['guias'][$registro->guia] = 1;
                    $registro->no_guia = 1;
                }
                if(!is_null($registro->guia)){
                    if(!isset($this->conteos['guias_contenedores'][$registro->guia])){
                        $this->conteos['guias_contenedores'][$registro->guia] = array();
                    }
                    if(!isset($this->conteos['guias_contenedores'][$registro->guia][$registro->numero_contenedor])){
                        $this->conteos['guias_contenedores'][$registro->guia][$registro->numero_contenedor] = 0;
                    }
                    $this->conteos['guias_contenedores'][$registro->guia][$registro->numero_contenedor] = 1;
                }
                
                $this->registros[$schema][] = $registro;
            }

        }
    }

    public function agregarImportes(){
        foreach($this->registros as $oficina=>$regs){
            foreach($regs as $k_reg => $reg){
                if($reg->no_guia == 1){
                    foreach($this->reglas['contenedores'] as $regla){
                        $restar = ($reg->guia != '' && $this->conteos['guias_frabel'][$reg->guia]) ? $this->conteos['guias_frabel'][$reg->guia] : 0;
                        $cuentas_guias_son = $this->conteos['guias'][$reg->guia] - $restar;
                        if(!is_null($reg->guia)){
                            $cuentas_contenedores_son = count($this->conteos['guias_contenedores'][$reg->guia]);
                        }else{
                            $cuentas_contenedores_son = 1;
                        }
                        if($cuentas_contenedores_son >= $regla['de'] && $cuentas_contenedores_son <= $regla['a']){
                            $this->registros[$oficina][$k_reg]['numero_contenedores'] = $cuentas_contenedores_son;
                            $this->registros[$oficina][$k_reg]['rango_tarifa'] = $regla['de']." a ".$regla['a'];
                            $this->registros[$oficina][$k_reg]['monto_aplicable'] = $regla['importe'];
                            $importe_dividido = $regla['importe'] / $cuentas_guias_son;
                        }
                    }
                }
                $this->registros[$oficina][$k_reg]['honorarios'] = "";
                if($reg->nombre_cliente != "FRABEL, S.A. DE C.V."){
                    $this->registros[$oficina][$k_reg]['honorarios'] = $importe_dividido;
                }
            }
        }
    }
}
