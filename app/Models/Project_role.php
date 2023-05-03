<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_role extends Model
{
    use HasFactory;
    protected $table = "project_roles";
    protected $fillable = ['id', 'role_code', 'role_name'];

    public function project_assignment_user(){
    	return $this->hasMany('App\Models\Project_assignment_user');
    }
}
