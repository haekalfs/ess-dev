<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement_item extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $table = "reimbursement_items";
    protected $fillable = ["id", "receipt_file", 'receipt_expiration',"file_path", "amount", "description", "status", "edited_by_finance", "receivable_receipt", "reimbursement_id", "created_at", "updated_at"];

    public function request(){
    	return $this->belongsTo('App\Models\Reimbursement', 'reimbursement_id', 'id')
        ->withDefault();
    }

    public function approval(){
        return $this->hasMany('App\Models\Reimbursement_approval', 'reimb_item_id', 'id');
    }

}
