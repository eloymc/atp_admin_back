<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BancoModel extends Model
{
    use HasFactory;
    protected $table = 'bancos';

    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }
    public function scopeCatalogo($query)
    {
        return $query->select(DB::raw("id_banco as value"), DB::raw("nombre_banco as field"))->orderBy("nombre_banco");
    }
}
