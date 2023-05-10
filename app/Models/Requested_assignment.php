<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requested_assignment extends Model
{
    use HasFactory;
    protected $table = "requested_assignment";
    protected $fillable = ["id", "req_date", "req_by", "status", "role", "responsibility", "company_project_id", "periode_start", "periode_end","created_at", "updated_at"];

    public function company_project(){
    	return $this->belongsTo('App\Models\Company_project');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'req_by', 'id');
    }
}
