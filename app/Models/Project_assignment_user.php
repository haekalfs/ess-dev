<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project_assignment_user extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $table = "project_assignment_users";

    protected $fillable = ['user_id', 'role', 'responsibility', 'periode_start', 'periode_end', 'project_assignment_id', 'company_project_id'];

    public function assigned(){
    	return $this->belongsTo('App\Models\Project_assignment', 'project_assignment_id', 'id');
    }

    public function timesheet_detail(){
    	return $this->hasMany('App\Models\Timesheet_detail');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function project_role(){
    	return $this->belongsTo('App\Models\Project_role', 'role', 'role_code');
    }

    public function company_project(){
    	return $this->belongsTo('App\Models\Company_project');
    }
}
