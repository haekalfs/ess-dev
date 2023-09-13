<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval_status extends Model
{
    use HasFactory;
    protected $table = "approval_status";

    public function leave_request(){
    	return $this->hasMany('App\Models\Leave_request');
    }

    public function medical_approval()
    {
        return $this->hasMany('App\Models\Medical_approval');
    }
}
