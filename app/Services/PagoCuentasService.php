<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IngresoModel;
use App\Models\ChequeModel;
use App\Models\DesgloceChequeModel;
use Exception;

class PagoCuentasService
{
    public function guardarPagoCuentas(Request $request, $user, $ingreso = null)
    {
        return DB::transaction(function () use ($request,$user,$ingreso) {
            if(!$ingreso){
                $ingreso = IngresoModel::where('id_ingreso',$request->id_ingreso)->first();
            }
            $anticipo = $this->InsertAnticipo($request, $user, $ingreso);
            return $anticipo;
        });
    }

    public function guardarDetalleAnticipo(Request $request, $user)
    {
        return DB::transaction(function () use ($request,$user) {
            $anticipo = AnticipoModel::where('id_anticipo',$request->id_anticipo)->first();
            $detalle_anticipo = $this->InsertDetalleAnticipo($request,$user, $anticipo);
            return $detalle_anticipo;
        });
    }

    protected function InsertAnticipo(Request $request, $user, $ingreso){
        $anticipo = new AnticipoModel();
        $anticipo->id_banco         = $ingreso->id_banco         ;
        $anticipo->cliente          = $ingreso->deudor           ;
        $anticipo->fecha_movimiento = $ingreso->fecha_movimiento ;
        $anticipo->fecha_cargo      = $ingreso->fecha_cargo      ;
        $anticipo->id_tipo_mov_banc = $ingreso->id_tipo_mov_banc ;
        $anticipo->no_mov           = $ingreso->no_mov           ;
        $anticipo->importe          = $request->anticipo         ;
        $anticipo->importe_aplicado = 0                          ;
        $anticipo->status           = 1                          ;
        $anticipo->usuario_registro = $user->cveusuario          ;
        $anticipo->fecharegistro    = date('Y-m-d H:i:s')        ;
        $anticipo->ip_registro      = $request->getClientIp()    ;
        $anticipo->observaciones    = $ingreso->observaciones    ;
        $anticipo->tipo             = 1                          ;
        $anticipo->id_ingreso       = $ingreso->id_ingreso       ;
        if (!$anticipo->save()) {
            throw new Exception('Error al guardar ingreso');
        }
        $ingreso->importe_aplicado = $ingreso->importe_aplicado + $request->anticipo;
        if (!$ingreso->save()) {
            throw new Exception('Error al guardar ingreso');
        }
        return $anticipo;
    }

    protected function InsertDetalleAnticipo(Request $request, $user, $anticipo){
        $detalle_anticipo = new DetalleAnticipoModel();
        $detalle_anticipo->id_anticipo      = $anticipo->id_anticipo    ;
        $detalle_anticipo->referencia       = $request->referencia      ;
        $detalle_anticipo->cliente          = $anticipo->cliente        ;
        $detalle_anticipo->fecha_aplicacion = $request->fecha_aplicacion;
        $detalle_anticipo->impuestos        = $request->importe         ;
        $detalle_anticipo->fletes           = 0                         ;
        $detalle_anticipo->pagos_terceros   = 0                         ;
        $detalle_anticipo->hon_comp         = 0                         ;
        $detalle_anticipo->observaciones    = $request->observaciones   ;
        $detalle_anticipo->status           = 1                         ;
        $detalle_anticipo->usuario_registro = $user->cveusuario         ;
        $detalle_anticipo->fecharegistro    = date('Y-m-d H:i:s')       ;
        $detalle_anticipo->ip_registro      = $request->getClientIp()   ;
        $detalle_anticipo->utilizado        = 0                         ;
        $detalle_anticipo->tipo_cuenta      = $request->tipo_cuenta     ;
        $detalle_anticipo->importe_dolares  = $request->importe_dolares ;
        $detalle_anticipo->tipo_cambio      = $request->tipo_cambio     ;
        
        if (!$detalle_anticipo->save()) {
            throw new Exception('Error al guardar ingreso');
        }
        return $detalle_anticipo;
    }
}