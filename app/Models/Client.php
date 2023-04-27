<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = "clients";
    protected $fillable = ['id', 'client_name', 'address'];

    public function projects(){
        return $this->hasMany('App\Models\Company_project');
    }
}
