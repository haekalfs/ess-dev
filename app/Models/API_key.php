<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class API_key extends Model
{
    use HasFactory;
    protected $table = "api_key";
    protected $fillable = ['id', 'name', 'public_key', 'secret_key', 'created_at'];
}
