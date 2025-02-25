<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ClienteModel extends Model
{
    use HasFactory;
    protected $table = 'cliente';

    public function scopeCatalogo($query)
    {
        return $query->select(DB::raw("id as value"), DB::raw("nombre_cliente as field"))->orderBy("nombre_cliente");
    }
}
