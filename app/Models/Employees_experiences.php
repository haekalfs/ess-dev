<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees_experiences extends Model
{
    use HasFactory;
    protected $table = "employees_experiences";
    protected $fillable = ['id', 'description'];
}
