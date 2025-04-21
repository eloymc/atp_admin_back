<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BeneficiarioModel extends Model
{
    use HasFactory;
    protected $table = 'beneficiarios';

    public function scopeActivos($query)
    {
        return $query->where('status', 1);
    }
    public function scopeCatalogo($query)
    {
        return $query->select(DB::raw("id_beneficiario as value"), DB::raw("nombre as field"))->orderBy("nombre");
    }

}
