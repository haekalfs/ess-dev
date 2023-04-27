<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory;

    protected $table = "roles";
    protected $fillable = ["id", "role", "description", "role_id", "created_at", "updated_at"];

    public function role(){
    	return $this->hasMany('App\Models\Usr_role');
    }

    public function user_access(){
    	return $this->hasMany('App\Models\User_access');
    }
}
