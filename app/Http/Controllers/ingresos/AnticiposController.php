<?php

namespace App\Http\Controllers\ingresos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IngresoModel;
use App\Models\DetalleIngresoModel;
use App\Models\AnticipoModel;
use App\Http\Controllers\LoginController;

class AnticiposController extends Controller
{
    public static function registrar(Request $req, $id_ingreso){
        $token = $req->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado'], 401);
        }
        $login = new LoginController();
        $user = $login->DatosToken($token);
        $id_anticipo = $_ENV['ID_ANTICIPO'];
        $ingreso = IngresoModel::where('id_ingreso',$id_ingreso)->first();
        $detalle_ingreso = DetalleIngresoModel::where('id_ingreso',$id_ingreso)->where('id_concepto',$id_anticipo)->where('status','>',0)->first();
        if(!$detalle_ingreso){
            // INSERTAR DETALLE_INGRESOS
            $detalle_ingreso = new DetalleIngresoModel();
            $detalle_ingreso->id_ingreso          = $id_ingreso             ;
            $detalle_ingreso->deudor              = $ingreso->deudor        ;
            $detalle_ingreso->id_concepto_ingreso = $id_anticipo            ;
            $detalle_ingreso->importe             = $req->anticipo          ;
            $detalle_ingreso->status              = 1                       ;
            $detalle_ingreso->usuario_registro    = $user->cveusuario       ;
            $detalle_ingreso->fecharegistro       = date('Y-m-d H:i:s')     ;
            $detalle_ingreso->ip_registro         = $request->getClientIp() ;
            $detalle_ingreso->importe_aplicado    = 0                       ;
            $detalle_ingreso->tipo_cambio         = 0                       ;
        }else{
            //EDITAR DETALLE_INGRESOS
        }
        if($detalle_ingreso->save()){
            $anticipo = AnticipoModel::where('id_ingreso',$id_ingreso)->first();
            if(!$anticipo){
                //INSERTAR ANTICIPO
                $anticipo = new AnticipoModel();
                $anticipo->id_banco         = $ingreso->id_banco        ;
                $anticipo->cliente          = $ingreso->deudor          ;
                $anticipo->fecha_movimiento = $ingreso->fecha_movimiento;
                $anticipo->fecha_cargo      = $ingreso->fecha_cargo     ;
                $anticipo->id_tipo_mov_banc = $ingreso->id_tipo_mov_banc;
                $anticipo->no_mov           = $ingreso->no_mov          ;
                $anticipo->importe          = $req->anticipo            ;
                $anticipo->importe_aplicado = 0                         ;
                $anticipo->status           = 1                         ;
                $anticipo->usuario_registro = $user->cveusuario         ;
                $anticipo->fecharegistro    = date('Y-m-d H:i:s')       ;
                $anticipo->ip_registro      = $request->getClientIp()   ;
                $anticipo->observaciones    = $ingreso->observaciones   ;
                $anticipo->tipo             = 1                         ;
                $anticipo->id_ingreso       = $id_ingreso               ;
            }else{
                //EDITAR ANTICIPO
            }
            if($anticipo->save()){
                return response()->json('1',200);
            }else{
                return response()->json('Error al guardar el Anticipo',400);
            }
        }else{
            return response()->json('Error al guardar el Desgloce del Ingreso',400);
        }
    }
}
