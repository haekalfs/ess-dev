<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = "position";
    protected $fillable = ["id", "position_name", 'position_level',"created_at", "updated_at"];

    public function user()
    {
        return $this->hasOne('App\Models\Users');
    }
}
