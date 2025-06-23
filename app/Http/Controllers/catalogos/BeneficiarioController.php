<?php

namespace App\Http\Controllers\catalogos;

use App\Http\Controllers\Controller;
use App\Models\BeneficiarioModel;
use Illuminate\Http\Request;
use DB;

class BeneficiarioController extends Controller
{
    public function get(Request $req){
        if(isset($req->id)){
            $cliente = BeneficiarioModel::where('id_beneficiario',$req->id);
            $resultado = $cliente->first();
            return response()->json($resultado,200);
        }
        $cliente = BeneficiarioModel::select('id_beneficiario','nombre');
        if(isset($req->q) && $req->q != ""){
            $cliente->where('nombre','ilike','%'.$req->q.'%');
        }
        $cliente->orderBy('nombre');
        $resultado = $cliente->get();
        return response()->json($resultado,200);
    }
}
