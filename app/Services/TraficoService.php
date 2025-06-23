<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TraficoModel;
use App\Models\DetalleIngresoModel;
use App\Services\AnticipoService;
use Exception;

class TraficoService
{
    public function consulta(Request $request, $user){
        if($request->tipo == 'cl_cat'){
            $referencias = $this->SoloRefernciasCliente($request->id_cliente);
            return $referencias;
        }
    }

    protected function SoloRefernciasCliente($id_cliente){
        $trafico = TraficoModel::where('cliente','=',$id_cliente);
        $trafico->where('status_trafico','!=',-100);
        $trafico->orderBy('fecha_actualizacion_tupla','DESC');
        $trafico->select('referencia',DB::raw("1 as tipo_cuenta"), DB::raw("0 as importe"));
        return $trafico->get();
    }
}