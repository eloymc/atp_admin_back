<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalleAnticipo extends Model
{
    use HasFactory;
    protected $table = 'detalle_anticipos';

    public function anticipo()
    {
        return $this->belongsTo(anticipo::class,'id_anticipo','id_anticipo')->where('status','>=',1);
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'numero_cuenta', 'numero_cuenta')
                    ->where('folio_fiscal', $this->folio_fiscal)
                    ->where('status','>',0);
    }
}
