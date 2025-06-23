<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeModel extends Model
{
    use HasFactory;
    protected $table = 'cheques';
    public $timestamps = false;
    protected $primaryKey = 'id_cheque';

    public function DetalleIngreso()
    {
        return $this->belongsTo(DetalleIngresoModel::class,'id_ingreso','id_ingreso')->where('status','>=',1);
    }

    public function Detalle()
    {
        return $this->hasMany(DesgloceChequeModel::class,'id_cheque','id_cheque')->where('status','>=',1);
    }

    public function Banco()
    {
        return $this->hasOne(BancoModel::class,'id_banco','banco')->where('status','>=',1)->select('id_banco','nombre_banco','cuenta_bancaria','cc_central');
    }
}
