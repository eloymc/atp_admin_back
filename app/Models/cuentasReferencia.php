<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuentasReferencia extends Model
{
    use HasFactory;
    protected $table = "cuentas_referencias";

    public function cuenta(){
        return $this->hasOne(cuenta::class,'id_cuenta','id_cuenta')->where('cuentas.status','>',0);
    }
}
