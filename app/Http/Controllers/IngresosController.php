<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IngresoModel;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ingresos\AnticiposController;
use App\Http\Controllers\ingresos\PagoCuentasController;
use App\Http\Controllers\ingresos\DepositoVariosController;
use App\Http\Controllers\ingresos\GarantiasController;
use Illuminate\Support\Facades\DB;
use App\Services\IngresoService;
use App\Services\AnticipoService;

class IngresosController extends Controller
{
    protected $_ingreso;
    protected $_anticipo;
    public $ingreso;

    public function __construct(IngresoService $_ingreso, AnticipoService $_anticipo)
    {
        $this->_ingreso = $_ingreso;
        $this->_anticipo = $_anticipo;
    }

    public function index(Request $request)
    {
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        $consulta = $this->_ingreso->consulta($request,$user);
        return response()->json($consulta,200);
    }

    public function show(Request $request,$id)
    {
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        $consulta = $this->_ingreso->consulta($request,$user,$id);
        return response()->json($consulta,200);
    }

    public function store(Request $request){
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        $validated = $request->validate([
            'banco' => 'required|numeric',
            'importe' => 'required|numeric',
            'fecha_movimiento' => 'required|string',
            'fecha_aplicacion' => 'required|string',
            'tipo_mov_bancario' => 'required|numeric',
            'no_operacion' => 'required|string'
        ]);
        
        try {
            $ingreso = $this->_ingreso->guardarIngresoConDetalles($request,$user);
            return response()->json([
                'status' => 'ok',
                'ingreso' => $ingreso->id_ingreso
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request){
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        if($request->tipo == 'desglosar'){
            try {
                $ingreso = $this->_ingreso->desglosar($request,$user);
                
                return response()->json([
                    'status' => 'ok',
                    'ingreso' => $ingreso->id_ingreso
                ],200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        if($request->tipo == 'desglosarAnticipo'){
            
            try {
                $anticipo = $this->_anticipo->guardarDetalleAnticipo($request,$user);
                return response()->json([
                    'status' => 'ok',
                    'ingreso' => $anticipo->id_anticipo
                ],200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    public function registrarAntipo(Request $req){
        if($req->anticipo > 0){
            AnticiposController::registrar($req, $this->ingreso->id_ingreso);
        }
    }

    public function registrarPagoCuentas(Request $req){
        if($importe > 0){
            
        }
    }

    public function registrarDepositoVarios(Request $req){
        if($importe > 0){
            
        }
    }

    public function registrarGarantias(Request $req){
        if($importe > 0){
            
        }
    }

}
