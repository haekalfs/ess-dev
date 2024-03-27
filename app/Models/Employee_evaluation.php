<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee_evaluation extends Model
{
    use HasFactory;
    protected $table = 'employee_evaluation';
    protected $fillable = ['id', 'question_id', 'month', 'year', 'user_id', 'q_value'];


    public function question(){
    	return $this->belongsTo('App\Models\Metrics_question', 'question_id', 'id')
        ->withDefault();
    }
}
