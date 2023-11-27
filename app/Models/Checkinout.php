<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkinout extends Model
{
    use HasFactory;
    protected $table = "checkinout";
    protected $fillable = ["id","user_id", "date", "time", "verify", "status", "created_at", "updated_at"];

    public function fingerId(){
    	return $this->hasOne('App\Models\Users_fingerprint', 'fingerprint_id', 'user_id')
        ->withDefault();
    }
}
