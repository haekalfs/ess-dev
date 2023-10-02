<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement_item extends Model
{
    use HasFactory;
    protected $table = "reimbursement_items";
    protected $fillable = ["id", "receipt_file", "amount", "description", "reimbursement_id", "created_at", "updated_at"];
}
