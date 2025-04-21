<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UsuarioModel extends Model
{
    use HasApiTokens;
    use HasFactory;
    protected $table = 'usuario';

    public function scopeActivo($query)
    {
        return $query->where('activo', 'S');
    }
}
