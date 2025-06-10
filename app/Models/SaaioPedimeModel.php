<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class SaaioPedimeModel extends EsquemaBaseModel
{
    use HasFactory;
    protected $table = 'saaio_pedime';

    public function scopeConFechaPago($query, $rango){
        $desde = $rango[0] ?? (date("Y") - 7)."-01-01";
        $hasta = $rango[1] ?? date("Y-m-d");
        $query->whereNotNull('fec_pago')
            ->whereNotNull('fir_pago')
            ->whereNotNull('fir_elec')
            ->whereBetween(DB::raw('fec_pago::date'),[$desde,$hasta]);
    }


    public function scopeImportaciones($query){
        $query->where('imp_expo','1');
    }

    public function scopeExportaciones($query){
        $query->where('imp_expo','2');
    }

    public function scopeQuitarRectificaciones($query){
        $query->whereNull('tip_pedi');
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

    public function Cliente()
    {
        return $this->hasOne(ClienteModel::class,'id','cve_impo');
    }

    
}
