<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentasModel extends Model
{
    use HasFactory;
    protected $table = "cuentas";

    public function scopeActivo($query)
    {
        return $query->where('status', '>', 0);
    }

    public function DetalleAnticipos()
    {
        return $this->hasMany(DetalleAnticipoModel::class, 'numero_cuenta', 'numero_cuenta')
                    ->where('detalle_anticipos.status','>',0);
    }

    public function DesgloceCheque()
    {
        return $this->belongsTo(DesgloceChequeModel::class,'numero_cuenta','numero_cuenta')->where('status','>=',1);
    }

    public function DetalleCuenta()
    {
        return $this->hasMany(DetalleCuentaModel::class,'id_cuenta','id_cuenta')->where('status','>=',1);
    }

    public function CuentaReferencias()
    {
        return $this->hasMany(CuentasReferenciaModel::class,'id_cuenta','id_cuenta');
    }

    public function CuentaPedimentos()
    {
        return $this->hasMany(CuentasPedimentoModel::class,'id_cuenta','id_cuenta')->where('status','>=',1);
    }
}
