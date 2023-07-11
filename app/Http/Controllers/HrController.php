<?php

namespace App\Http\Controllers;

use App\Models\Cutoffdate;
use App\Models\Financial_password;
use App\Models\Timesheet_approval_cutoff_date;
use App\Models\Timesheet_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class HrController extends Controller
{
    public function index(){
		//Cutoff Date Timesheet Submission
		$Cutoffdate = Cutoffdate::find(1);
		$CutoffdateTimesheetApproval = Cutoffdate::find(2);
		$leaveApprovalCutoffdate = Cutoffdate::find(3);
		$reimburseApprovalCutoffdate = Cutoffdate::find(4);

		$financialPasscode = Financial_password::find(1);
		$financialPass = Crypt::decrypt($financialPasscode->password);

		$approvers = Timesheet_approver::all();

		return view('hr.compliance.main', ['cutoffDate' => $Cutoffdate, 'tsCutoffdate' => $CutoffdateTimesheetApproval, 'leaveCutoffdate' => $leaveApprovalCutoffdate, 'reimburseCutoffdate' => $reimburseApprovalCutoffdate, 'financialPass' => $financialPass]); 
	}
}
