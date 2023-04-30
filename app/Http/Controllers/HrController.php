<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HrController extends Controller
{
    public function index(){
		return view('hr.compliance.main'); 
	}

    public function timesheet(){
		return view('hr.compliance.timesheet_settings'); 
	}

    public function timesheet_settings_save(){
		return view('hr.compliance.main'); 
	}
}
