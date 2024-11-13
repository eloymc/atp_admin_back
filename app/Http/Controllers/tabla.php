<?php

namespace App\Http\Controllers;

use App\Models\IngresoModel;
use App\Models\DetalleIngresoModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class tabla extends Controller
{
    public function ConsultaTabla(Request $req){
        $arr_subConsultas = array();
        if(isset($req->subConsultas)){
            $arr_subConsultas = $req->subConsultas;
        }
        $arr_subConsultas[] = 'Banco';
        $query = IngresoModel::activos()->with($arr_subConsultas);
        if(isset($req->id_ingreso) && $req->id_ingreso > 0){
            $query->where('ingresos.id_ingreso',$req->id_ingreso);
        }else{
            $query->whereBetween('ingresos.fecha_movimiento', ['2024-01-01 00:00:00', date("Y-m-d H:i:s")]);
        }
        if(isset($req->pendientesDesglosar)){
            $query->where('ingresos.importe','!=',DB::raw('ingresos.importe_aplicado'));
        }
        $ingresos = $query->paginate(5);
        return response()->json($ingresos,200);
    }

    public function ConsultaTabla2(Request $req){
        $query = DetalleIngresoModel::with(['ingreso']);
        $resultado = $query->get();
        return response()->json($resultado,200);
    }
}
