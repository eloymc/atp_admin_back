<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuenta extends Model
{
    use HasFactory;
    protected $table = "cuentas";

    public function scopeActivo($query)
    {
        return $query->where('status', '>', 0);
    }

    public function detalleAnticipo()
    {
        return $this->belongsTo(detalleAnticipo::class,'numero_cuenta','numero_cuenta')->where('status','>=',1);
    }

    public function desgloceCheque()
    {
        return $this->belongsTo(desgloceCheque::class,'numero_cuenta','numero_cuenta')->where('status','>=',1);
    }

    public function detalleCuenta()
    {
        return $this->hasMany(detalleCuenta::class,'id_cuenta','id_cuenta')->where('status','>=',1);
    }

    public function cuentaReferencias()
    {
        return $this->hasMany(cuentasReferencia::class,'id_cuenta','id_cuenta');
    }

    public function cuentaPedimentos()
    {
        return $this->hasMany(cuentasPedimento::class,'id_cuenta','id_cuenta')->where('status','>=',1);
    }


}
