<?php

namespace App\Http\Controllers\catalogos;

use App\Http\Controllers\Controller;
use App\Models\ClienteModel;
use Illuminate\Http\Request;
use DB;

class ClienteController extends Controller
{
    public function get(Request $req){
        if(isset($req->id)){
            $cliente = ClienteModel::where('id',$req->id);
            $resultado = $cliente->first();
            return response()->json($resultado,200);
        }
        $cliente = ClienteModel::select('id','nombre_cliente');
        if(isset($req->q)){
            $cliente->where('nombre_cliente','ilike','%'.$req->q.'%');
        }
        $cliente->orderBy('nombre_cliente');
        $resultado = $cliente->get();
        return response()->json($resultado,200);
    }
}
