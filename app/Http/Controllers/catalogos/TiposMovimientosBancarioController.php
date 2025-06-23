<?php

namespace App\Http\Controllers\catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TiposMovimientoBancarioModel;
use DB;

class TiposMovimientosBancarioController extends Controller
{
    public function get(Request $req){
        if(isset($req->id)){
            $mov = TiposMovimientoBancarioModel::where('id_tipo_mov_banc',$req->id);
            $resultado = $mov->first();
            return response()->json($resultado,200);
        }
        $mov = TiposMovimientoBancarioModel::select('id_tipo_mov_banc','descripcion_mov');
        if(isset($req->q) && $req->q != ""){
            $mov->where('descripcion_mov','ilike','%'.$req->q.'%');
        }
        $mov->orderBy('descripcion_mov');
        $resultado = $mov->get();
        return response()->json($resultado,200);
    }
}
