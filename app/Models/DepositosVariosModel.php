<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositosVariosModel extends Model
{
    use HasFactory;
    protected $table = "depositos_varios";
    public $timestamps = false;
    protected $primaryKey = 'id_deposito';
}
