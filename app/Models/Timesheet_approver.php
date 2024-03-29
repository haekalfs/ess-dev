<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet_approver extends Model
{
    use HasFactory;
    protected $table = "timesheet_approver";
    protected $fillable = ['approver', 'department_id', 'group_id', 'approver_level'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'approver', 'id')->withDefault();
    }
}
