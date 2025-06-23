<?php

namespace App\Http\Controllers\trafico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TraficoService;
use App\Http\Controllers\LoginController;

class TraficoController extends Controller
{
    protected $_trafico;

    public function __construct(TraficoService $_trafico)
    {
        $this->_trafico = $_trafico;
    }

    public function index(Request $request)
    {
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        $consulta = $this->_trafico->consulta($request,$user);
        return response()->json($consulta,200);
    }
}
