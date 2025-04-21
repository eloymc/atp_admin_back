<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAnticipoModel extends Model
{
    use HasFactory;
    protected $table = 'detalle_anticipos';

    public function Anticipo()
    {
        return $this->belongsTo(AnticipoModel::class,'id_anticipo','id_anticipo')->where('status','>=',1);
    }

    public function Cuenta()
    {
        return $this->belongsTo(CuentasModel::class, 'numero_cuenta', 'numero_cuenta')
                    ->where('folio_fiscal', $this->folio_fiscal)
                    ->where('status','>',0);
    }
}
