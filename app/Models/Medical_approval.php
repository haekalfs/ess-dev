<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medical_approval extends Authenticatable
{
    use HasFactory;
    // use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'medicals_approval';
    protected $fillable = [
        'id',
        'medical_id',
        'RequestTo',
        'status',
        'approval_amount',
        'approval_notes',
    ];

    public function medical()
    {
        return $this->belongsTo('App\Models\Medical', 'medical_id', 'id')
        ->withDefault();
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'RequestTo', 'id')
            ->withDefault();
    }
    public function approval_status()
    {
        return $this->belongsTo('App\Models\Approval_status', 'status', 'approval_status_id')
        ->withDefault();
    }
}
