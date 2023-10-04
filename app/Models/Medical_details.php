<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical_details extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;

    // protected $dates = ['deleted_at'];
    protected $primaryKey = 'mdet_id';
    protected $table = 'medicals_details';
    protected $fillable = [
        'mdet_id',
        'medical_id',
        'mdet_attachment',
        'mdet_amount',
        'mdet_desc',
        'amount_approved'
    ];

    public function medical()
    {
        return $this->belongsTo(Medical::class, 'medical_id');
    }

    // public function medical(){
    // 	return $this->hasMany('App\Models\Medical');
    // }
}