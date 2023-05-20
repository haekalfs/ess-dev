<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emp_leave_quota extends Model
{
    use HasFactory;
    protected $table = 'emp_leave_quota';
    protected $fillable = ['user_id', 'leave_id', 'quota_left', 'active_periode'];
}
