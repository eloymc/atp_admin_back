<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposMovimientoBancarioModel extends Model
{
    use HasFactory;
    protected $table = 'tipos_mov_banc';
    public $timestamps = false;
}
