<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class anticipo extends Model
{
    use HasFactory;
    protected $table = 'anticipos';

    public function detalleIngreso()
    {
        return $this->belongsTo(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function detalle()
    {
        return $this->hasMany(detalleAnticipo::class,'id_anticipo','id_anticipo')->where('status','>=',1);
    }
}
