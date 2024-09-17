<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ingreso extends Model
{
    use HasFactory;
    protected $table = 'ingresos';
    
    public $id_conceptos = array(
        "altamira"=>array("anticipos"=>10,"cheques"=>9,"varios"=>12)
    );

    public function scopeCancelados($query)
    {
        return $query->where('status', 0);
    }

    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }

    public function scopePendientes($query)
    {
        return $query->where('status', 1)->whereColumn('importe_aplicado','<','importe');
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', 1)->whereColumn('importe_aplicado','importe');
    }
    
    public function scopeDameMes($query,$anio,$mes)
    {
        return $query->where('status', 1)
                 ->whereYear('fecha_movimiento', $anio)
                 ->when($mes, function($query) use ($mes) {
                     return $query->whereMonth('fecha_movimiento', $mes);
                 });
    }

    public function scopeDameAnio($query,$anio)
    {
        return $query->where('status', 1)
                 ->whereYear('fecha_movimiento', $anio);
    }

    public function detalleIngresosAnticipos()
    {
        $schema = DB::select("SELECT current_schema();")[0]->current_schema;
        return $this->hasMany(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['anticipos'])->where('status','>=',1);
    }

    public function detalleIngresosCheques()
    {
        $schema = DB::select("SELECT current_schema();")[0]->current_schema;
        return $this->hasMany(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['cheques'])->where('status','>=',1);
    }

    public function detalleIngresosDepositosVarios()
    {
        $schema = DB::select("SELECT current_schema();")[0]->current_schema;
        return $this->hasMany(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['varios'])->where('status','>=',1);
    }

    public function detalleIngresos()
    {
        return $this->hasMany(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function banco()
    {
        return $this->hasOne(bancos::class,'id_banco','id_banco')->select('id_banco','nombre_banco','cuenta_bancaria','cc_central');
    }

    public function cliente()
    {
        return $this->hasOne(cliente::class,'id','deudor')->select('id','nombre_cliente','rfc');
    }

    public function beneficiario()
    {
        return $this->hasOne(beneficiario::class,'id_beneficiario','deudor')->select('id_beneficiario','nombre','rfc');
    }
    public function tipoMovimientoBancario()
    {
        return $this->hasOne(tiposMovimientosBancarios::class,'id_tipo_mov_banc','id_tipo_mov_banc')->select('id_tipo_mov_banc','descripcion_mov');
    }

}