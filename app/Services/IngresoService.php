<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IngresoModel;
use App\Models\DetalleIngresoModel;
use App\Services\AnticipoService;
use Exception;

class IngresoService
{
    public function consulta(Request $request, $user, $id_ingreso = null)
    {
        $query = IngresoModel::{$request->status ?? 'activos'}();
        $arr_subConsultas = array();
        if(isset($request->subConsultas)){
            $arr_subConsultas = $request->subConsultas;
        }
        //$arr_subConsultas[] = 'DetalleIngresos';
        //$arr_subConsultas[] = 'DetalleIngresosAnticipos';
        //$arr_subConsultas[] = 'DetalleIngresosCheques';
        //$arr_subConsultas[] = 'DetalleIngresosDepositosVarios';
        $arr_subConsultas[] = 'Banco'                 ;
        $arr_subConsultas[] = 'Cliente'               ;
        $arr_subConsultas[] = 'Beneficiario'          ;
        $arr_subConsultas[] = 'TipoMovimientoBancario';
        $arr_subConsultas[] = 'Anticipo'              ;
        $arr_subConsultas[] = 'Cheque'                ;
        $arr_subConsultas['Anticipo.Detalle'] = function ($query) {
            $query->select('id_detalle_anticipo', 'id_anticipo', 'referencia', 'folio_fiscal','numero_cuenta','impuestos','fletes','pagos_terceros','hon_comp'); 
        };
        $arr_subConsultas['Cheque.Detalle'] = function ($query) {
            $query->select('id_desgloce', 'id_cheque', 'folio_fiscal','numero_cuenta','importe');
        };
        $query->with($arr_subConsultas);
        if((isset($request->id_ingreso) && $request->id_ingreso > 0) || $id_ingreso){
            if(!$id_ingreso){
                $id_ingreso = $request->id_ingreso;
            }
            $query->where('ingresos.id_ingreso',$id_ingreso);
            return $query->first();
            //return response()->json($query->first(),200);
        }else{
            if(isset($request->fecha_inicio) && isset($request->fecha_fin)){
                $query->rangoFecha($request->fecha_inicio, $request->fecha_fin);
            }else{
                if(isset($request->mes) && isset($request->anio)){
                    $query->dameMes($request->anio,$request->mes);
                    $query->orderBy('fecha_movimiento', 'desc');
                }else{
                    if(isset($request->anio)){
                        $query->dameAnio($request->anio);
                    }else{
                        $query->dameAnio(date("Y"));
                        $query->orderBy('fecha_movimiento', 'desc');
                    }
                }
            }
            if(isset($request->cl)){
                $query->where('deudor', $request->cl);
                $query->where('tipo_deudor', 'C');
            }
            if(isset($request->beneficiario)){
                $query->where('deudor', $request->beneficiario);
                $query->where('tipo_deudor', 'D');
            }
            if(isset($request->banco)){
                $query->where('id_banco', $request->banco);
            }
            $query->orderBy('fecha_movimiento', 'asc');
        }
        
        if(isset($request->paginate)){
            $ingresos = $query->paginate(15);
        }else{
            $ingresos = $query->get();
        }
        return $ingresos;
        //return response()->json($ingresos,200);
    }

    public function guardarIngresoConDetalles(Request $request, $user)
    {
        return DB::transaction(function () use ($request,$user) {
            $ingreso = $this->InsertIngreso($request,$user);
            
            if($request->anticipo > 0){
                $id_concepto = $_ENV['ID_ANTICIPO'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->anticipo);
                $_anticipo = new AnticipoService();
                $anticipo = $_anticipo->guardarAnticipo($request,$user,$ingreso);
            }
            if($request->pago_cuentas > 0){
                $id_concepto = $_ENV['ID_PAGO_CUENTAS'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->pago_cuentas);
            }
            if($request->varios > 0){
                $id_concepto = $_ENV['ID_DEPOSITO_VARIOS'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->varios);
            }
            if($request->garantias > 0){
                $id_concepto = $_ENV['ID_GARANTIAS'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->garantias);
            }
            return $ingreso; // opcionalmente puedes retornar algo
        });
    }

    public function desglosar(Request $request, $user){
        return DB::transaction(function () use ($request,$user) {
            $ingreso = $this->TraerIngreso($request->id_ingreso);
            if($request->anticipo > 0){
                $id_concepto = $_ENV['ID_ANTICIPO'];
                $detalle_ingreso = $this->SumarIngresoDetalleConcepto($request->id_ingreso,$id_concepto, $request->anticipo);
                if(!$detalle_ingreso){
                    $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->anticipo);
                    $_anticipo = new AnticipoService();
                    $anticipo = $_anticipo->guardarAnticipo($request,$user,$ingreso);
                }else{
                    $_anticipo = new AnticipoService();
                    $_anticipo->agregarMasImporte($request->id_ingreso,$request->anticipo);
                }
            }
            if($request->pago_cuentas > 0){
                $id_concepto = $_ENV['ID_PAGO_CUENTAS'];
                $detalle_ingreso = $this->SumarIngresoDetalleConcepto($request->id_ingreso,$id_concepto, $request->pago_cuentas);
                if(!$detalle_ingreso){
                    $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->pago_cuentas);
                }
            }
            if($request->varios > 0){
                $id_concepto = $_ENV['ID_DEPOSITO_VARIOS'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->varios);
            }
            if($request->garantias > 0){
                $id_concepto = $_ENV['ID_GARANTIAS'];
                $detalle_ingreso = $this->InsertIngresoDetalle($request,$user,$ingreso,$id_concepto,$request->garantias);
            }
            return $ingreso; // opcionalmente puedes retornar algo
        });
    }

    protected function InsertIngreso(Request $request, $user){
        $ingreso = new IngresoModel();
        $ingreso->id_banco         = $request->banco                                                             ;
        $ingreso->deudor           = ($request->tipoDeudor == 'cliente') ? $request->cliente : $request->diverso ;
        $ingreso->fecha_movimiento = $request->fecha_movimiento                                                  ;
        $ingreso->fecha_cargo      = $request->fecha_aplicacion                                                  ;
        $ingreso->id_tipo_mov_banc = $request->tipo_mov_bancario                                                 ;
        $ingreso->no_mov           = $request->no_operacion                                                      ;
        $ingreso->observaciones    = $request->observaciones                                                     ;
        $ingreso->tipo_deudor      = ($request->tipoDeudor == 'cliente') ? 'C' : 'D'                             ;
        $ingreso->importe          = $request->importe                                                           ;
        $ingreso->status           = 1                                                                           ;
        $ingreso->importe_aplicado = 0                                                                           ;
        $ingreso->usuario_registro = $user->cveusuario                                                           ;
        $ingreso->fecharegistro    = date('Y-m-d H:i:s')                                                         ;
        $ingreso->ip_registro      = $request->getClientIp()                                                     ;
        $ingreso->status_deposito  = "F"                                                                         ;

        if (!$ingreso->save()) {
            throw new Exception('Error al guardar ingreso');
        }
        return $ingreso;
    }

    protected function InsertIngresoDetalle(Request $request, $user, $ingreso, $id_concepto, $importe_aplicado){

        $detalle_ingreso = new DetalleIngresoModel();
        $detalle_ingreso->id_ingreso          = $ingreso->id_ingreso    ;
        $detalle_ingreso->deudor              = $ingreso->deudor        ;
        $detalle_ingreso->id_concepto_ingreso = $id_concepto            ;
        $detalle_ingreso->importe             = $importe_aplicado      ;
        $detalle_ingreso->status              = 1                       ;
        $detalle_ingreso->usuario_registro    = $user->cveusuario       ;
        $detalle_ingreso->fecharegistro       = date('Y-m-d H:i:s')     ;
        $detalle_ingreso->ip_registro         = $request->getClientIp() ;
        $detalle_ingreso->importe_aplicado    = 0                       ;
        $detalle_ingreso->tipo_cambio         = 0                       ;
        if (!$detalle_ingreso->save()) {
            throw new Exception('Error al guardar ingreso detalle');
        }
        $this->ImporteAplicado($ingreso->id_ingreso,$importe_aplicado);
        return $detalle_ingreso;
    }

    protected function ImporteAplicado($id_ingreso, $importe){
        IngresoModel::where('id_ingreso',$id_ingreso)->update([
            'importe_aplicado' => DB::raw('importe_aplicado + '.$importe)
        ]);
    }

    protected function TraerIngreso($id_ingreso){
        $ingreso = IngresoModel::where('id_ingreso',$id_ingreso)->first();
        return $ingreso;
    }

    protected function TraerIngresoDetalle($id_ingreso){
        $detalle_ingreso = DetalleIngresoModel::where('id_ingreso',$id_ingreso)->where('status','>',0)->get();
        return $detalle_ingreso;
    }

    protected function TraerIngresoDetalleConcepto($id_ingreso,$id_concepto){
        $detalle_ingreso = DetalleIngresoModel::where('id_ingreso',$id_ingreso)->where('id_concepto_ingreso',$id_concepto)->where('status','>',0)->first();
        return $detalle_ingreso;
    }

    protected function SumarIngresoDetalleConcepto($id_ingreso, $id_concepto, $importe){
        $detalle_ingreso = DetalleIngresoModel::where('id_ingreso',$id_ingreso)->where('id_concepto_ingreso',$id_concepto)->where('status','>',0)->first();
        if(!$detalle_ingreso){
            return false;
        }else{
            $detalle_ingreso->importe = $detalle_ingreso->importe + $importe;
            if(!$detalle_ingreso->save()){
                throw new Exception('Error al sumar importe en Detalle del Ingreso');
            }
            $this->ImporteAplicado($id_ingreso,$importe);
        }
        return $detalle_ingreso;
    }


}