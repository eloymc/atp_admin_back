<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentasReferenciaModel extends Model
{
    use HasFactory;
    protected $table = "cuentas_referencias";
    public $timestamps = false;

    public function Cuentas(){
        return $this->hasOne(CuentasModel::class,'id_cuenta','id_cuenta')->where('cuentas.status','>',0);
    }
}
