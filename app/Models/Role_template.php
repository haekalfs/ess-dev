<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role_template extends Model
{
    use HasFactory;

    protected $table = "role_templates";
    protected $fillable = ["id", "role", "role_name", "role_id", "created_at", "updated_at"];

    public function role(){
    	return $this->belongsTo('App\Models\Role');
    }
}
