<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaaioPedimeModel extends Model
{
    use HasFactory;
    protected $table = 'saaio_pedime';

    public function scopeConcluidos($query){
        $desde = (date("Y") - 7)."-01-01 00:00:00";
        $hasta = date("Y-m-d H:i:s");
        $query->whereNotNull('fec_pago')->whereNotNull('fir_pago')->whereNotNull('fir_elec')->whereBetween('fec_pago',[$desde,$hasta]);
    }

    public function CuentasReferencias()
    {
        return $this->hasMany(CuentasReferenciaModel::class,'referencia','num_refe');
    }

    public function CuentasPedimentos()
    {
        return $this->hasMany(CuentasPedimentoModel::class, 'pedimento', 'num_pedi')
                ->whereRaw("CAST(pedimento AS VARCHAR) = num_pedi")
                ->where('patente', $this->pat_agen);
    }
}
