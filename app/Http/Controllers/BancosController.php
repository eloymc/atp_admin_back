<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BancoModel;

class BancosController extends Controller
{
    public function get(Request $request){
        $bancos = BancoModel::activos();
        if(isset($request->tipo)){
            if($request->tipo == 'catalogo'){
                $bancos->catalogo();
            }
        }
        return response()->json($bancos->get(),200);
    }
}
