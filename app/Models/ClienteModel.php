<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ClienteModel extends EsquemaBaseModel
{
    use HasFactory;
    protected $table = 'cliente';
    protected $rawTable = 'cliente';
    public $timestamps = false;

    public function scopeCatalogo($query)
    {
        return $query->select(DB::raw("id as value"), DB::raw("nombre_cliente as field"))->orderBy("nombre_cliente");
    }

    public function SaaioPedime()
    {
        return $this->belongsTo(SaaioPedimeModel::class,'id','cve_impo');
    }
}
