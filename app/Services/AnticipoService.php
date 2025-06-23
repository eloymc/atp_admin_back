<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IngresoModel;
use App\Models\AnticipoModel;
use App\Models\DetalleAnticipoModel;
use App\Models\DetalleIngresoModel;
use Exception;

class AnticipoService
{
    public function guardarAnticipo(Request $request, $user, $ingreso = null)
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
            $id_concepto = $_ENV['ID_ANTICIPO'];
            $anticipo = AnticipoModel::where('id_anticipo',$request->id_anticipo)->first();
            foreach($request->desglosar_anticipos as $registro_anticipo){
                $detalle_anticipo = $this->InsertDetalleAnticipo($request, $user, $anticipo, $registro_anticipo);
                $anticipo->importe_aplicado = $anticipo->importe_aplicado + $registro_anticipo['importe'];
                if(!$anticipo->save()){
                    throw new Exception('Error al ajustar Anticipo');
                }
                $detalle_ingresos = DetalleIngresoModel::where('id_ingreso',$anticipo->id_ingreso)->where('id_concepto_ingreso',$id_concepto)->where('status','>',0)->first();
                $detalle_ingresos->importe_aplicado = $detalle_ingresos->importe_aplicado + $request->importe;
                if(!$detalle_ingresos->save()){
                    throw new Exception('Error al ajustar Ingreso desde Anticipo');
                }
            }
            return $detalle_anticipo;
        });
    }

    public function agregarMasImporte($id_ingreso,$importe){
        $anticipo = AnticipoModel::where('id_ingreso',$id_ingreso)->where('status','>',0)->first();
        $anticipo->importe = $anticipo->importe + $importe;
        if(!$anticipo->save()){
            throw new Exception('Error al ajustar con suma Anticipo');
        }
        return $anticipo;
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
            throw new Exception('Error al guardar Anticipo');
        }
        return $anticipo;
    }

    protected function InsertDetalleAnticipo(Request $request, $user, $anticipo, $desgloce){
        $detalle_anticipo                   = new DetalleAnticipoModel() ;
        $detalle_anticipo->id_anticipo      = $anticipo->id_anticipo     ;
        $detalle_anticipo->referencia       = $desgloce['referencia']    ;
        $detalle_anticipo->cliente          = $anticipo->cliente         ;
        $detalle_anticipo->fecha_aplicacion = $anticipo->fecha_movimiento;
        $detalle_anticipo->impuestos        = $desgloce['importe']       ;
        $detalle_anticipo->fletes           = 0                          ;
        $detalle_anticipo->pagos_terceros   = 0                          ;
        $detalle_anticipo->hon_comp         = 0                          ;
        $detalle_anticipo->observaciones    = $anticipo->observaciones   ;
        $detalle_anticipo->status           = 1                          ;
        $detalle_anticipo->usuario_registro = $user->cveusuario          ;
        $detalle_anticipo->fecharegistro    = date('Y-m-d H:i:s')        ;
        $detalle_anticipo->ip_registro      = $request->getClientIp()    ;
        $detalle_anticipo->utilizado        = 0                          ;
        $detalle_anticipo->tipo_cuenta      = $desgloce['tipo_cuenta']   ;
        //if(isset($request->importe_dolares)){
        //    $detalle_anticipo->importe_dolares  = $request->importe_dolares ;
        //}
        //if(isset($request->tipo_cambio)){
        //    $detalle_anticipo->tipo_cambio      = $request->tipo_cambio     ;
        //}
        if (!$detalle_anticipo->save()) {
            throw new Exception('Error al guardar Anticipo Detalle');
        }
        return $detalle_anticipo;
    }
}