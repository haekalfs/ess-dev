<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave_request_history extends Model
{
    use HasFactory;
    protected $table = 'leave_request_history';
    protected $fillable = ['req_date', 'user_id', 'quota_used', 'description', 'quota_left', 'emp_leave_quota_id', 'leave_id', 'leave_request_id'];

    public function leave(){
    	return $this->belongsTo('App\Models\Leave');
    }

    public function emp_leave_quota(){
    	return $this->belongsTo('App\Models\Emp_leave_quota');
    }

    public function leave_request(){
    	return $this->belongsTo('App\Models\Leave_request', 'leave_request_id', 'id');
    }
}
