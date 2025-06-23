<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class IngresoModel extends EsquemaBaseModel
{
    use HasFactory;
    protected $table = 'ingresos';
    protected $primaryKey = 'id_ingreso';
    public $incrementing = true;
    public $timestamps = false;
    protected $appends = [
        'total_anticipos',
        'total_pago_cuentas',
        'total_varios',
        'total_garantias',
        //'total_anticipos_aplicados',
        'total_pago_cuentas_aplicados',
        'total_varios_aplicados',
        'total_garantias_aplicados',
    ];
    
    public $id_conceptos = array(
        "altamira"=>array("anticipos"=>10,"cheques"=>9,"varios"=>12,"garantias"=>13),
        "vallejo"=>array("anticipos"=>6,"cheques"=>2,"varios"=>3,"garantias"=>0)
    );

    ////////////////////////////////////
    ///SCOPE'S
    ////////////////////////////////////

    public function scopeCancelados($query)
    {
        return $query->where('status', 0);
    }

    public function scopeOrdenFechaDesc($query)
    {
        return $query->orderBy('fecha_movimiento', 'desc');
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

    public function scopeRangoFecha($query,$inicio,$fin)
    {
        return $query->where('status', 1)
                 ->whereBetween('fecha_movimiento', [$inicio,$fin]);
    }

    ////////////////////////////////////
    ///RELACIONES
    ////////////////////////////////////

    public function DetalleIngresosAnticipos()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->hasMany(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['anticipos'])->where('status','>=',1);
    }

    public function DetalleIngresosCheques()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->hasMany(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['cheques'])->where('status','>=',1);
    }

    public function DetalleIngresosDepositosVarios()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->hasMany(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('id_concepto_ingreso', $this->id_conceptos[$schema]['varios'])->where('status','>=',1);
    }

    public function DetalleIngresos()
    {
        return $this->hasMany(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Anticipo()
    {
        return $this->hasOne(AnticipoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Cheque()
    {
        return $this->hasOne(ChequeModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Banco()
    {
        return $this->hasOne(BancoModel::class,'id_banco','id_banco')->select('id_banco','nombre_banco','cuenta_bancaria','cc_central');
    }

    public function Cliente()
    {
        return $this->hasOne(ClienteModel::class,'id','deudor')->select('id','nombre_cliente','rfc');
    }

    public function Beneficiario()
    {
        return $this->hasOne(BeneficiarioModel::class,'id_beneficiario','deudor')->select('id_beneficiario','nombre','rfc');
    }
    public function TipoMovimientoBancario()
    {
        return $this->hasOne(TiposMovimientoBancarioModel::class,'id_tipo_mov_banc','id_tipo_mov_banc')->select('id_tipo_mov_banc','descripcion_mov');
    }

    ////////////////////////////////////
    ///OTROS ATRIBUTOS
    ////////////////////////////////////

    public function getTotalAnticiposAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['anticipos'])->sum('importe');
    }

    public function getTotalPagoCuentasAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['cheques'])->sum('importe');
    }

    public function getTotalVariosAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['varios'])->sum('importe');
    }

    public function getTotalGarantiasAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['garantias'])->sum('importe');
    }

    //public function getTotalAnticiposAplicadosAttribute()
    //{
    //    return $this->anticipos->flatMap(function ($anticipo) {
    //        return $anticipo->detalleConFolios;
    //    })->sum('impuestos');
    //}

    public function getTotalPagoCuentasAplicadosAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['cheques'])->sum('importe_aplicado');
    }

    public function getTotalVariosAplicadosAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['varios'])->sum('importe_aplicado');
    }

    public function getTotalGarantiasAplicadosAttribute()
    {
        $schema = env('OFICINA_SCHEMA');
        return $this->DetalleIngresos()->where('id_concepto_ingreso', $this->id_conceptos[$schema]['garantias'])->sum('importe_aplicado');
    }
}
