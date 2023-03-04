<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_detail extends Model
{
    use HasFactory;
    protected $table = 'users_details';
    protected $fillable = ['status',];
    public function user(){
    	return $this->belongsTo('App\Models\User');
    }
}
