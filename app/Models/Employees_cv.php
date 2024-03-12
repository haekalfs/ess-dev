<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees_cv extends Model
{
    use HasFactory;
    protected $table = "employees_cv";
    protected $fillable = ['id', 'description'];
}
