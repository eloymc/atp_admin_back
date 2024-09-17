<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cheque extends Model
{
    use HasFactory;
    protected $table = 'cheques';

    public function detalleIngreso()
    {
        return $this->belongsTo(DetalleIngreso::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function detalle()
    {
        return $this->hasMany(DesgloceCheque::class,'id_cheque','id_cheque')->where('status','>=',1);
    }

    public function banco()
    {
        return $this->hasOne(bancos::class,'id_banco','banco')->where('status','>=',1)->select('id_banco','nombre_banco','cuenta_bancaria','cc_central');
    }
}

