<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id',
        'user_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function users_detail(){
    	return $this->hasOne('App\Models\Users_detail')->withDefault();
    }

    public function role_id(){
    	return $this->hasMany('App\Models\Usr_role');
    }

    public function project_assignment_user(){
    	return $this->hasMany('App\Models\Project_assignment_user');
    }

    public function medical(){
    	return $this->hasOne('App\Models\Medical');
    }

    public function medical_approval()
    {
        return $this->hasMany('App\Models\Medical_approval');
    }
    public function medical_payment()
    {
        return $this->hasOne('App\Models\Medical_payment');
    }

    public function timesheet_detail(){
    	return $this->hasMany('App\Models\Timesheet_detail');
    }

    public function requested_assignment(){
    	return $this->hasMany('App\Models\Requested_assignment');
    }

    public function emp_leave_quota(){
    	return $this->hasMany('App\Models\Emp_leave_quota');
    }

    public function leave_request_approval(){
    	return $this->hasMany('App\Models\Leave_request_approval');
    }

    public function leave_request(){
    	return $this->hasMany('App\Models\Leave_request');
    }

    public function approver()
    {
        return $this->hasOne('App\Models\Timesheet_approver');
    }

    public function export()
    {
        return $this->hasOne('App\Models\Setting');
    }
}
