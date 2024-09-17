<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class desgloceCheque extends Model
{
    use HasFactory;
    protected $table = 'desgloce_cheques';

    public function cheque()
    {
        return $this->belongsTo(cheque::class,'id_cheque','id_cheque')->where('status','>=',1);
    }
}
