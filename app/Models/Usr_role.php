<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usr_role extends Model
{
    protected $table = "usr_roles";
    protected $fillable = ["id", "role_name", "role_id", "user_id", "created_at", "updated_at"];

    public function user(){
    	return $this->belongsTo('App\Models\Users');
    }

    public function role(){
    	return $this->belongsTo('App\Models\Role');
    }
}
