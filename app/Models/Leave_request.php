<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave_request extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = 'leave_request';
    protected $fillable = ['id','req_date', 'req_by', 'leave_dates', 'total_days', 'reason', 'leave_id', 'contact_number', 'status', 'RequestTo'];

    public function leave(){
    	return $this->belongsTo('App\Models\Leave');
    }

    public function approval_status(){
    	return $this->belongsTo('App\Models\Approval_status', 'status', 'approval_status_id');
    }

    public function leave_request_approval(){
    	return $this->hasMany('App\Models\Leave_request_approval');
    }
}
