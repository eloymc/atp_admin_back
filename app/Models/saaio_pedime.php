<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class saaio_pedime extends Model
{
    use HasFactory;
    protected $table = 'saaio_pedime';

    public function scopeConcluidos($query){
        $desde = (date("Y") - 7)."-01-01 00:00:00";
        $hasta = date("Y-m-d H:i:s");
        $query->whereNotNull('fec_pago')->whereNotNull('fir_pago')->whereNotNull('fir_elec')->whereBetween('fec_pago',[$desde,$hasta]);
    }

    public function cuentasReferencias()
    {
        return $this->hasMany(cuentasReferencia::class,'referencia','num_refe');
    }

    public function cuentasPedimentos()
    {
        return $this->hasMany(CuentasPedimento::class, 'pedimento', 'num_pedi')
                ->whereRaw("CAST(pedimento AS VARCHAR) = num_pedi")
                ->where('patente', $this->pat_agen);
    }


}
