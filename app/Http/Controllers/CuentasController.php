<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cuenta;
use App\Models\saaio_pedime;

class CuentasController extends Controller
{
    public function get(Request $request)
    {
        $cuentas = saaio_pedime::concluidos()
            ->whereDoesntHave('cuentasReferencias')
            ->whereDoesntHave('cuentasPedimentos');
        //$cuentas = cuenta::activo()->with(['detalleAnticipos'])->whereHas('detalleAnticipos')->whereYear('fecha_cuenta', 2024); cuentasPedimentos
        return response()->json($cuentas->paginate(15),200);
    }

}
