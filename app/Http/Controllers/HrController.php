<?php

namespace App\Http\Controllers;

use App\Models\Cutoffdate;
use App\Models\Financial_password;
use App\Models\Timesheet_approval_cutoff_date;
use Illuminate\Http\Request;

class HrController extends Controller
{
    public function index(){
		//Cutoff Date Timesheet Submission
		$Cutoffdate = Cutoffdate::find(1);
		$timesheetApprovals = Timesheet_approval_cutoff_date::find(1);
		$financialPass = Financial_password::find(1);

		return view('hr.compliance.main', ['cutoffDate' => $Cutoffdate, 'timesheetApprovals' => $timesheetApprovals, 'financialPass' => $financialPass]); 
	}
}
