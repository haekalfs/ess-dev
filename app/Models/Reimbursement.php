<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;
    protected $table = "reimbursements";

    protected $fillable = ["id", "f_id", "f_type", "f_req_by","f_payment_method", "f_approver", "notes", "status_id", "created_at", "updated_at"];

    public function items(){
    	return $this->hasMany('App\Models\Reimbursement_item', 'reimbursement_id', 'id');
    }

    public function approval(){
    	return $this->hasMany('App\Models\Reimbursement_approval', 'reimbursement_id', 'id');
    }

    public function approval_status(){
    	return $this->belongsTo('App\Models\Approval_status', 'status_id', 'approval_status_id')
        ->withDefault();
    }

    public function dept(){
    	return $this->belongsTo('App\Models\Department', 'f_approver', 'id')
        ->withDefault();
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'f_req_by', 'id')
        ->withDefault();
    }
}
