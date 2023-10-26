<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emp_medical_balance extends Model
{
    use HasFactory;
    protected $table = 'emp_medical_balance';
    protected $fillable = ['id', 'user_id', 'medical_balance', 'medical_deducted', 'active_periode', 'expiration'];

    public function user(){
    	return $this->belongsTo('App\Models\User')
        ->withDefault();
    }

}
