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
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'medicals';
    protected $fillable = [
        'id',
        'med_number',
        'user_id',
        'med_req_date',
        'med_payment',
        'med_status',
        'med_total_amount'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function medical_details(){
    	return $this->ManyTo('App\Models\Medical_details');
    }
}
