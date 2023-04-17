<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_project extends Model
{
    use HasFactory;
    protected $table = "company_projects";


    public function role(){
    	return $this->hasMany('App\Models\Role');
    }

    public function project_assignment(){
    	return $this->hasMany('App\Models\Project_assignment');
    }
}
