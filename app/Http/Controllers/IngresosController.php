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
        $arr_subConsultas[] = 'Banco';
        $arr_subConsultas[] = 'Cliente';
        $arr_subConsultas[] = 'Beneficiario';
        $arr_subConsultas[] = 'TipoMovimientoBancario';
        $arr_subConsultas[] = 'DetalleIngresos';
        $arr_subConsultas[] = 'DetalleIngresosAnticipos.Anticipo';
        //$arr_subConsultas[] = 'Anticipos';
        $arr_subConsultas[] = 'DetalleIngresosCheques';
        $arr_subConsultas[] = 'DetalleIngresosDepositosVarios';
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
