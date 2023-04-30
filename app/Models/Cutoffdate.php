<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cutoffdate extends Model
{
    use HasFactory;
    protected $table = "cutoffdate";
    protected $fillable = ['date'];
}
