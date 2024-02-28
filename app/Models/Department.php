<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "department";
    protected $fillable = ["id", "department_name","created_at", "updated_at"];

    public function user()
    {
        return $this->hasOne('App\Models\Users');
    }

    public function approvers(){
    	return $this->hasMany('App\Models\Timesheet_approver', 'department_id', 'id');
    }
}
