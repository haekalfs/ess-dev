<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_assignment extends Model
{
    use HasFactory;
    protected $table = "Project_assignments";


    public function user(){
    	return $this->belongsTo('App\Models\Users');
    }

    public function worker(){
    	return $this->hasMany('App\Models\Project_assignment_user');
    }
}