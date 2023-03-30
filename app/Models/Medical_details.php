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
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'medical_details';
    protected $fillable = [
        'id',
        'mdet_number',
        'mdet_attachment',
        'mdet_amount',
        'mdet_desc',
    ];
    
    // public static function generateId()
    // {
    //     $lastId = Medical_details::orderBy('id', 'desc')->first();
    //     $newId = $lastId ? $lastId->id + 1 : 1;
    //     return 'MED_' . str_pad($newId, 4, '0', STR_PAD_LEFT);
    // }

    public function medical(){
    	return $this->belongsTo('App\Models\Medical');
    }
}