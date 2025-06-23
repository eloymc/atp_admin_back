<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnticipoModel extends Model
{
    use HasFactory;
    protected $table = 'anticipos';
    public $timestamps = false;
    protected $primaryKey = 'id_anticipo';

    public function DetalleIngreso()
    {
        return $this->belongsTo(DetalleIngresoModel::class,'id_ingreso','id_ingreso');
    }

    public function Ingreso()
    {
        return $this->belongsTo(IngresoModel::class,'id_ingreso','id_ingreso');
    }

    public function Detalle()
    {
        return $this->hasMany(DetalleAnticipoModel::class,'id_anticipo','id_anticipo')->where('status','>=',1);
    }

    public function DetalleConFolios()
    {
        return $this->hasMany(DetalleAnticipoModel::class,'id_anticipo','id_anticipo')->whereNotNull('folio_fiscal')->whereNotNull('numero_cuenta')->where('status','>=',1);
    }
}
