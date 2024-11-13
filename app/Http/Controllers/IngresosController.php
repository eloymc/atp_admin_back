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
        if(isset($request->paginate)){
            $ingresos = $query->paginate(15);
        }else{
            $ingresos = $query->get();
        }
        return response()->json($ingresos,200);
    }

}
