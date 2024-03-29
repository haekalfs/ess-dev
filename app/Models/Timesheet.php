<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timesheet extends Model
{

    protected $primaryKey = 'ts_id';
    protected $table = "timesheet";
    protected $dates = ['deleted_at'];
    protected $fillable = ['ts_id', 'ts_id_date', 'ts_task_id', 'ts_date','ts_activity', 'ts_status_id', 'ts_location', 'ts_role', 'ts_task','allowance', 'ts_type', 'incentive','ts_from_time','ts_to_time','ts_user_id','created_at','updated_at','deleted_at'];

    public function surat_penugasan(){
    	return $this->hasMany('App\Models\Surat_penugasan', 'ts_id_date', 'timesheet_id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'ts_user_id', 'id')->withDefault();
    }
}
