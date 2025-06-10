<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IngresoModel;
use Illuminate\Support\Facades\DB;

class IngresosController extends Controller
{
    public function get(Request $request)
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
        $arr_subConsultas[] = 'Banco';
        $arr_subConsultas[] = 'Cliente';
        $arr_subConsultas[] = 'Beneficiario';
        $arr_subConsultas[] = 'TipoMovimientoBancario';
        $arr_subConsultas[] = 'Anticipo';
        $arr_subConsultas[] = 'Cheque';
        $arr_subConsultas['Anticipo.Detalle'] = function ($query) {
            $query->select('id_detalle_anticipo', 'id_anticipo', 'referencia', 'folio_fiscal','numero_cuenta','impuestos','fletes','pagos_terceros','hon_comp'); 
        };
        $arr_subConsultas['Cheque.Detalle'] = function ($query) {
            $query->select('id_desgloce', 'id_cheque', 'folio_fiscal','numero_cuenta','importe');
        };
        $query->with($arr_subConsultas);
        if(isset($request->id_ingreso) && $request->id_ingreso > 0){
            $query->where('ingresos.id_ingreso',$request->id_ingreso);
            return response()->json($query->first(),200);
        }else{
            if(isset($request->mes) && isset($request->anio)){
                $query->dameMes($request->anio,$request->mes);
            }else{
                if(isset($request->anio)){
                    $query->dameAnio($request->anio);
                }else{
                    $query->dameAnio(date("Y"));
                }
            }
        }
        $query->ordenFechaDesc();
        if(isset($request->paginate)){
            $ingresos = $query->paginate(15);
        }else{
            $ingresos = $query->get();
        }
        return response()->json($ingresos,200);
    }

}
