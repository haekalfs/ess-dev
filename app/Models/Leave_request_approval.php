<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave_request_approval extends Model
{
    use HasFactory;
    protected $table = 'leave_request_approval';
    protected $fillable = ['status', 'RequestTo', 'notes', 'leave_request_id'];

    public function leave_request(){
    	return $this->belongsTo('App\Models\Leave_request', 'leave_request_id', 'id')
        ->withDefault();
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'RequestTo', 'id')
        ->withDefault();
    }
}
