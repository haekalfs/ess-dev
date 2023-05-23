<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat_penugasan extends Model
{
    use HasFactory;

    protected $table = "surat_penugasan";
    protected $fillable = ["id", "user_id", "ts_date", "file_name", "file_path","timesheet_id", "created_at", "updated_at"];

    public function timesheet(){
    	return $this->belongsTo('App\Models\Timesheet', 'timesheet_id', 'ts_id_date');
    }
}
