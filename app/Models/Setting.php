<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = "setting";
    protected $fillable = ['id', 'name_setting', 'user_id', 'position_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\Users')->withDefault();
    }

}
