<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;
    protected $table = "reimbursements";

    protected $fillable = ["id", "f_id", "f_type", "f_req_by", "f_purpose_of_purchase","f_top", "status_id", "created_at", "updated_at"];
}
