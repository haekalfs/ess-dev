<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project_assignment_user extends Model
{
    use HasFactory;
    protected $table = "Project_assignment_users";


    public function assigned(){
    	return $this->belongsTo('App\Models\Project_assignment');
    }
}
