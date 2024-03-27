<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;
    public $incrementing = false;

    protected $dates = ['deleted_at'];
    protected $table = 'medicals';
    protected $fillable = [
        'id',
        'user_id',
        'med_req_date',
        'med_payment',
        'med_status',
        'medical_type',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function medical_details()
    {
        return $this->hasMany('App\Models\Medical_details');
    }

    public function medical_approval()
    {
        return $this->hasOne('App\Models\Medical_approval');
    }
    public function medical_payment()
    {
        return $this->hasOne('App\Models\Medical_payment');
    }
    public function medical_type()
    {
        return $this->belongsTo(Medical_type::class, 'type_id');
    }
}
