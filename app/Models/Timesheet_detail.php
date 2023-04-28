<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet_detail extends Model
{
    protected $primaryKey = 'id';
    protected $table = "timesheet_details";
    protected $fillable = ['id', 'ts_task', 'ts_task_id' , 'roleAs', 'workhours','RequestTo', 'ts_location', 'timesheet_workflow_id' , 'ts_mandays','user_id','activity','note', 'date_submitted', 'ts_status_id','user_timesheet', 'month_periode','created_at','updated_at'];

    public function workflow(){
    	return $this->belongsTo('App\Models\Timesheet_workflow');
    }
}
