<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCuentaModel extends Model
{
    use HasFactory;
    protected $table = "detalle_cuentas";
    public $timestamps = false;
    protected $primaryKey = false;
}
