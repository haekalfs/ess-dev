<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "department";
    protected $fillable = ["id", "department_name", "department_id","created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo('App\Models\Users');
    }

}