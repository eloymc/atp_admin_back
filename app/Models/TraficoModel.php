<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraficoModel extends EsquemaBaseModel
{
    use HasFactory;
    protected $table = "trafico";
    public $timestamps = false;

}
