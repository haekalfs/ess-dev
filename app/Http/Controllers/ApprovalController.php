<?php

namespace App\Http\Controllers;

use App\Exports\TimesheetExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Session;

class ApprovalController extends Controller
{
    public function index()
	{
        $workflows = Timesheet_workflow::orderBy('updated_at', 'desc')->get();
        
        $currentMonth = date('m');
        $currentYear = date('Y');

        $approvals = Timesheet_workflow::where('ts_status_id', '20')->whereYear('date_submitted', $currentYear)->get();
		return view('approval.main', compact('workflows'));
	}

    public function approval_director()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $approvals = Timesheet_workflow::where('ts_status_id', '20')->whereYear('date_submitted', $currentYear)->get();
        return view('approval.director', compact('approvals'));
    }

    public function approve_director($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        $activities = Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', "haekals")
        ->update(['ts_status_id' => '29']);
        Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->user_id, 'month_periode' => $year.$month],['date_approved' => date('Y-m-d'),'activity' => 'Approved', 'ts_status_id' => '29', 'note' => '', 'user_timesheet' => $user_timesheet]);
        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"Your approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function reject_director($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        $activities = Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', "haekals")
        ->update(['ts_status_id' => '404']);
        Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->user_id, 'month_periode' => $year.$month],['activity' => 'Approved', 'ts_status_id' => '404', 'note' => '', 'user_timesheet' => $user_timesheet]);
        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('warning',"Your rejected $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function export_excel()
	{
		return Excel::download(new TimesheetExport, 'siswa.xlsx');
	}

    public function review()
	{
		$currentMonth = date('m');
        $currentYear = date('Y');

        $approvals = Timesheet_workflow::where('ts_status_id', '20')->whereYear('date_submitted', $currentYear)->get();
		return view('review.finance', compact('approvals'));
	}
}
