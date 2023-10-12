<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement_approval extends Model
{
    use HasFactory;
    protected $table = "reimbursement_approval";

    protected $fillable = ["id", "status", "RequestTo", "reimb_item_id", "notes", "approved_status", "reimbursement_id", "created_at", "updated_at"];

    public function request(){
    	return $this->belongsTo('App\Models\Reimbursement', 'reimbursement_id', 'id')
        ->withDefault();
    }

    public function item(){
    	return $this->belongsTo('App\Models\Reimbursement_item', 'reimb_item_id', 'id')
        ->withDefault();
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'RequestTo', 'id')
        ->withDefault();
    }
}
