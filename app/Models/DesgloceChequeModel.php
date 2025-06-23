<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesgloceChequeModel extends Model
{
    use HasFactory;
    protected $table = 'desgloce_cheques';
    public $timestamps = false;
    protected $primaryKey = 'id_desgloce';

    public function Cheque()
    {
        return $this->belongsTo(ChequeModel::class,'id_cheque','id_cheque')->where('status','>=',1);
    }
}
