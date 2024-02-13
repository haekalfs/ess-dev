<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical_payment extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;

    protected $table = 'medical_payment';
    protected $fillable = [
        'id',
        'medical_id',
        'payment_approver',
        'paid_status',
        'note',
        'payment_date',
        'total_payment'
    ];

    public function medical()
    {
        return $this->belongsTo('App\Models\Medical', 'medical_id', 'id')
            ->withDefault();
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'payment_approver', 'id')
        ->withDefault();
    }
}
