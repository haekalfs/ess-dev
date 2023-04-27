<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_assignment_user extends Model
{
    use HasFactory;
    protected $table = "project_assignment_users";

    protected $fillable = ['user_id', 'role', 'responsibility', 'periode_start', 'periode_end', 'project_assignment_id', 'company_project_id'];

    public function assigned(){
    	return $this->belongsTo('App\Models\Project_assignment');
    }
}
