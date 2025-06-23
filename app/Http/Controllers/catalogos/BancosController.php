<?php

namespace App\Http\Controllers\catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BancoModel;

class BancosController extends Controller
{
    public function get(Request $req){
        $bancos = BancoModel::activos();
        $bancos->select('id_banco','abreviatura','nombre_banco','cuenta_bancaria');
        if($req->id){
            $bancos = BancoModel::where('id_banco',$req->id);
            return response()->json($bancos->first(),200);
        }
        $bancos->where('status',1);
        return response()->json($bancos->get(),200);
    }
}
