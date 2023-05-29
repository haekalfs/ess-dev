<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emp_leave_quota extends Model
{
    use HasFactory;
    protected $table = 'emp_leave_quota';
    protected $fillable = ['user_id', 'leave_id', 'quota_left', 'active_periode', 'quota_used', 'once_in_service_years'];

    public function user(){
    	return $this->belongsTo('App\Models\User')
        ->withDefault();
    }

    public function leave(){
    	return $this->belongsTo('App\Models\Leave')
        ->withDefault();
    }
}
