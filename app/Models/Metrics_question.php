<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metrics_question extends Model
{
    use HasFactory;
    protected $table = "metrics_question";
    protected $fillable = ['id', 'description'];
}
