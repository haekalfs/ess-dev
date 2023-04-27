<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_access extends Model
{
    use HasFactory;
    protected $table = "user_access";
    protected $primaryKey = 'id';

    protected $fillable = ["id", "page_id", "role_id", "created_at", "updated_at"];

    public function role(){
    	return $this->belongsTo('App\Models\Role');
    }
}
