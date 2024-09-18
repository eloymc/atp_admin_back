<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cuenta;

class CuentasController extends Controller
{
    public function get(Request $request)
    {
        $cuentas = cuenta::activo()->with(['detalleAnticipos'])->whereHas('detalleAnticipos')->whereYear('fecha_cuenta', 2024);
        return response()->json($cuentas->get(),200);
    }

}
