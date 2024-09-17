<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class detalleIngreso extends Model
{
    use HasFactory;
    protected $table = 'detalle_ingresos';

    public function ingreso()
    {
        return $this->belongsTo(ingreso::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function anticipo()
    {
        return $this->hasOne(anticipo::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function cheque()
    {
        return $this->hasOne(cheque::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function depositoVarios()
    {
        return $this->hasOne(depositosVarios::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }
}
