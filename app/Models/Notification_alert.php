<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification_alert extends Model
{
    use HasFactory;
    protected $table = "notification_alerts";
    protected $fillable = ['user_id', 'message', 'type','month_periode' ,'importance', 'read_stat'];
}
