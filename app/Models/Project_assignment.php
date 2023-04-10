<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_assignment extends Model
{
    use HasFactory;
    protected $table = "Project_assignments";


    protected $fillable = ['assignment_no', 'req_date', 'req_by', 'reference_doc', 'notes', 'company_project_id'];

    public function user(){
    	return $this->belongsTo('App\Models\Users');
    }

    public function worker(){
    	return $this->hasMany('App\Models\Project_assignment_user');
    }
}
