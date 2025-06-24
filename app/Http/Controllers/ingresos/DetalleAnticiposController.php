<?php

namespace App\Http\Controllers\ingresos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AnticipoService;
use App\Http\Controllers\LoginController;

class DetalleAnticiposController extends Controller
{
    protected $_anticipo;

    public function __construct(AnticipoService $_anticipo)
    {
        $this->_anticipo = $_anticipo;
    }
    
    public function destroy(Request $request, $id_detalle_anticipo){
        $login = new LoginController();
        $user = $login->Usuario($request,false);
        try {
            $this->_anticipo->quitarDetalleAnticipo($request, $id_detalle_anticipo, $user);
            return response()->json([
                'status' => 'ok'
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
