<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet_workflow extends Model
{
    protected $primaryKey = 'id';
    protected $table = "timesheet_workflows";
    protected $fillable = ['id', 'status_id' , 'RequestTo', 'user_id', 'user_timesheet', 'month_periode','created_at','updated_at'];


    public function details(){
    	return $this->hasMany('App\Models\Timesheet_detail');
    }
}
