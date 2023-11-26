<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_fingerprint extends Model
{
    use HasFactory;
    protected $table = "users_fingerprint";
    protected $fillable = ["id","user_id", "fingerprint_id", "created_at", "updated_at"];

    public function user(){
    	return $this->belongsTo('App\Models\User');
    }
}
