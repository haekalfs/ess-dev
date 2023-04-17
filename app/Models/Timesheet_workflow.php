<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet_workflow extends Model
{
    protected $primaryKey = 'id';
    protected $table = "timesheet_workflows";
    protected $fillable = ['id', 'ts_task', 'ts_task_id' , 'RequestTo', 'ts_location' , 'ts_mandays','user_id','activity','note', 'date_submitted', 'ts_status_id','user_timesheet', 'month_periode','created_at','updated_at'];
}
