<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical_type extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $table = 'medical_type';
    protected $fillable = [
        'id',
        'name_type',
    ];

    public function medical()
    {
        return $this->hasOne(Medical::class, 'id');
    }

    // public function medical(){
    // 	return $this->hasMany('App\Models\Medical');
    // }
}
