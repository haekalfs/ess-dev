<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company_project extends Model
{
    use HasFactory;
    protected $table = "company_projects";

    protected $fillable = ['id', 'project_code', 'alias', 'project_name', 'address', 'periode_start', 'periode_end', 'client_id'];

    public function role(){
    	return $this->hasMany('App\Models\Role');
    }

    public function project_assignment(){
    	return $this->hasMany('App\Models\Project_assignment');
    }

    public function requested_assignment(){
    	return $this->hasMany('App\Models\Requested_assignment');
    }
    
    public function client(){
        return $this->belongsTo('App\Models\Client');
    }
}
