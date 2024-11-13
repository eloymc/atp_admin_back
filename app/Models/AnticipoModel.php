<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnticipoModel extends Model
{
    use HasFactory;
    protected $table = 'anticipos';

    public function DetalleIngreso()
    {
        return $this->belongsTo(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Detalle()
    {
        return $this->hasMany(DetalleAnticipoModel::class,'id_anticipo','id_anticipo')->where('status','>=',1);
    }
}
