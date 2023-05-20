<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $table = 'leave';
    
    public function leave_request(){
    	return $this->hasMany('App\Models\Leave_request');
    }
}
