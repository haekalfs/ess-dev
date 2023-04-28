<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users_detail extends Model
{
    use HasFactory;
    protected $table = 'users_details';
    protected $fillable = ['user_id','status_active','employee_status', 'resignation_date', 'hired_date', 'position_id', 'department_id', 
    'employee_id', 'usr_address', 'current_address', 'usr_address_city', 'usr_address_city', 'usr_address_postal', 'usr_phone_home', 
    'usr_phone_mobile','usr_npwp', 'usr_id_type', 'usr_npwp', 'usr_id_type', 'usr_id_no', 'usr_id_expiration', 'usr_dob', 'usr_birth_place', 
    'usr_gender', 'usr_religion', 'usr_merital_status', 'usr_children', 'usr_bank_name', 'usr_bank_branch', 'usr_bank_account'];
            
    public function user(){
    	return $this->belongsTo('App\Models\User');
    }
    public function role()
    {
        return $this->hasMany('App\Models\Usr_role');
    }

    public function department()
    {
        return $this->belongsTO('App\Models\Department');
    }

    public function position()
    {
        return $this->belongsTO('App\Models\Position');
    }

}
