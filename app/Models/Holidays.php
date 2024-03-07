<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holidays extends Model
{
    use HasFactory;

    protected $table = "holidays";
    protected $fillable = ["id", "user_id", "ts_date", 'surat_edar','description', "isHoliday", "status", "approvedBy", "intended_for", "isProject", "timesheet_id", "created_at", "updated_at"];

    public function timesheet(){
    	return $this->belongsTo('App\Models\Timesheet', 'timesheet_id', 'ts_id_date');
    }

    public function document(){
    	return $this->belongsTo('App\Models\Document_letter', 'surat_edar', 'id');
    }

    public function role(){
    	return $this->belongsTo('App\Models\Role', 'intended_for', 'id')->withDefault();
    }

    public function company_project(){
    	return $this->belongsTo('App\Models\Company_project', 'intended_for', 'id')->withDefault();
    }
}
