<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "roles";
    protected $fillable = ["id", "role_name", "role_id", "user_id", "created_at", "updated_at"];

    public function user(){
    	return $this->belongsTo('App\Models\Users');
    }

    public function role_template(){
    	return $this->hasOne('App\Models\Role_template');
    }
}
