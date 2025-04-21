<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleIngresoModel extends Model
{
    use HasFactory;
    protected $table = 'detalle_ingresos';

    public function Ingreso()
    {
        return $this->belongsTo(IngresoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Anticipo()
    {
        return $this->hasOne(AnticipoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Cheque()
    {
        return $this->hasOne(ChequeModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function DepositosVarios()
    {
        return $this->hasOne(DepositosVariosModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }
}
