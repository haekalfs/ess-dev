<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_assignment extends Model
{
    use HasFactory;
    protected $table = "project_assignments";


    protected $fillable = ['id','assignment_no', 'req_date', 'req_by', 'reference_doc', 'notes', 'company_project_id', 'task_id', 'approval_status'];

    public function user(){
    	return $this->belongsTo('App\Models\Users');
    }

    public function worker(){
    	return $this->hasMany('App\Models\Project_assignment_user');
    }

    public function company_project(){
    	return $this->belongsTo('App\Models\Company_project');
    }
}
