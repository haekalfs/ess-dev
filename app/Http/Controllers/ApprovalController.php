<?php

namespace App\Http\Controllers;

use App\Exports\TimesheetExport;
use App\Jobs\NotifRejectedTimesheet;
use App\Mail\ApprovalTimesheet;
use App\Mail\RejectedTimesheet;
use App\Models\Approval_status;
use App\Models\Emp_leave_quota;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Notification_alert;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Surat_penugasan;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Usr_role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApprovalController extends Controller
{
    public function index()
	{
        $accessController = new AccessController();
        $result = $accessController->usr_acc(202);

        $tsCount = Timesheet_detail::whereNotIn('ts_status_id', ['30', '404', '29', '10'])
        ->where(function($query) {
            $query->where('RequestTo', Auth::user()->id);
        })
        ->count();

        $pCount = Project_assignment::where('approval_status', 40)->count();
        $leaveCount = Leave_request_approval::whereNotIn('status', ['20', '30', '29', '404'])
        ->where(function($query) {
            $query->where('RequestTo', Auth::user()->id);
        })
        ->count();

		return view('approval.main', ['tsCount' => $tsCount, 'pCount' => $pCount, 'leaveCount' => $leaveCount]);
	}

    public function timesheet_approval()
    {
        $currentYear = date('Y');

        // Get the current day of the month
        $currentDay = date('j');

        $checkUserPost = Auth::user()->users_detail->position->id;

        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60])->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 5 && $currentDay <= 8) {
                if (in_array($checkUserPost, [7, 8, 12])) {
                    $Check = DB::table('timesheet_details')
                        ->select('*')
                        ->whereYear('date_submitted', $currentYear)
                        ->whereNotIn('ts_status_id', [10, 15])
                        ->whereNotIn('RequestTo', $ts_approver)
                        ->groupBy('user_timesheet', 'month_periode')
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 ELSE 0 END)')
                        ->pluck('user_timesheet')
                        ->toArray();
                        if (!empty($Check)) {
                            $approvals = DB::table('timesheet_details')
                                ->select('*')
                                ->whereYear('date_submitted', $currentYear)
                                ->where('RequestTo', Auth::user()->id)
                                ->whereIn('user_timesheet', $Check)
                                ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                ->groupBy('user_timesheet', 'month_periode')
                                ->get();
                        } else {
                            $approvals = DB::table('timesheet_details')
                                ->select('*')
                                ->where('RequestTo', 'xxhaekalsxx')
                                ->groupBy('user_timesheet', 'month_periode')
                                ->get();
                        }
                } else {
                    $Check = DB::table('timesheet_details')
                        ->select('*')
                        ->whereYear('date_submitted', $currentYear)
                        ->whereNotIn('ts_status_id', [10, 15])
                        ->whereNotIn('RequestTo', [Auth::user()->id])
                        ->groupBy('user_timesheet', 'month_periode')
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 404 THEN 0 ELSE 1 END)')
                        ->pluck('user_timesheet')
                        ->toArray();
                    if (!empty($Check)) {
                        $approvals = DB::table('timesheet_details')
                        ->select('*')
                        ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                        ->whereIn('user_timesheet', $Check)
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                    } else {
                        $approvals = DB::table('timesheet_details')
                        ->select('*')
                        ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
                        ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                    }
                }
            return view('approval.timesheet_approval', ['approvals' => $approvals]);
        } else {
            // Handle the case when the date is not within the range
            return redirect()->back()->with('failed', 'This page can only be accessed between the 5th - 8th of each month.');
        }
    }

    public function approve(Request $request, $user_timesheet, $year, $month)
    {
        date_default_timezone_set("Asia/Jakarta");

        $validator = Validator::make($request->all(), [
            'approval_notes' => 'sometimes'
        ]);

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [40,45,55,60])->pluck('approver');
        $checkUserDir = $timesheetApproversDir->toArray();
        foreach ($countRows as $row) {
            $tsStatusId = '30';
            $activity = 'Approved';
        
            switch (true) {
                case in_array(Auth::user()->id, $checkUserDir):
                    $tsStatusId = '29';
                    $activity = 'Approved';
                    break;
                default:
                    $tsStatusId = '30';
                    break;
            }
        
            $approve = Timesheet_detail::where('month_periode', $year.$month)
                ->where('user_timesheet', $user_timesheet)
                ->where('RequestTo', Auth::user()->id)
                ->where('ts_task_id', $row->ts_task_id);

            // $approvals = Timesheet_detail::groupBy('user_timesheet', 'ts_status_id', 'RequestTo');

            if ($validator->passes()) {
                $notes = $request->approval_notes;
                $approve->update(['ts_status_id' => $tsStatusId, 'activity' => $activity, 'note' => $notes]);
            } else {
                $approve->update(['ts_status_id' => $tsStatusId, 'activity' => $activity]);
            }
            $currentYear = date('Y');

            $Check = DB::table('timesheet_details')
                    ->select('*')
                    ->whereYear('date_submitted', $currentYear)
                    ->whereNotIn('ts_status_id', [10, 15])
                    ->whereNotIn('RequestTo', [Auth::user()->id])
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 20 THEN 0 ELSE 1 END)')
                    ->pluck('user_timesheet')
                    ->toArray();

            if (!empty($Check)) {
                $tsStatusId = '30';
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
                ->where('ts_user_id', $user_timesheet)
                ->update(['ts_status_id' => $tsStatusId]);
            } else {
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
                ->where('ts_user_id', $user_timesheet)
                ->update(['ts_status_id' => 30]);
            }
        }

        $approverName = Auth::user()->name;
        $entry = new Notification_alert;
        $entry->user_id = $user_timesheet;
        $ts_name = date("F", mktime(0, 0, 0, $month, 1)).' - '.$year;
        $entry->message = "Your Timesheet of $ts_name has been Approved! by $approverName";
        $entry->importance = 1;
        $entry->save();

        $weekendReplacementInCurrentMonth = Surat_penugasan::where('user_id', $user_timesheet)->whereMonth('ts_date', $month)->whereYear('ts_date', $year)->count();
        $countWeekendReplacement = Emp_leave_quota::where('user_id', $user_timesheet)
        ->where('leave_id', 100)
        ->where('active_periode', '>=', date('Y-m-d'))
        ->sum('quota_left');
        if(!$weekendReplacementInCurrentMonth){
            Emp_leave_quota::updateOrCreate([
                'user_id' => Auth::user()->id,
                'leave_id' => 100,
            ], [
                'quota_left' => 1,
                'active_periode' => date('Y-m-d'),
                'expiration' => "9999-01-01",
                'once_in_service_years' => false
            ]);
        } else {
            $totalWeekendReplacement = $weekendReplacementInCurrentMonth + $countWeekendReplacement;
            Emp_leave_quota::updateOrCreate([
                'user_id' => Auth::user()->id,
                'leave_id' => 100,
            ], [
                'quota_left' => $totalWeekendReplacement,
                'active_periode' => date('Y-m-d'),
                'expiration' => "9999-01-01",
                'once_in_service_years' => false
            ]);
        }

        return redirect('/approval/timesheet/p')->with('success',"You approved $user_timesheet timereport!");
    }

    public function reject(Request $request, $user_timesheet, $year, $month)
    {
        date_default_timezone_set("Asia/Jakarta");

        $validator = Validator::make($request->all(), [
            'reject_notes' => 'sometimes'
        ]);

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops

            $reject = Timesheet_detail::where('month_periode', $year.$month)
                ->where('user_timesheet', $user_timesheet)
                ->where('RequestTo', Auth::user()->id)
                ->where('ts_task_id', $row->ts_task_id);

            // $approvals = Timesheet_detail::groupBy('user_timesheet', 'ts_status_id', 'RequestTo');

            if ($validator->passes()) {
                $notes = $request->reject_notes;
                $reject->update(['ts_status_id' => '404', 'activity' => 'Rejected', 'note' => $notes]);
            } else {
                $reject->update(['ts_status_id' => '404', 'activity' => 'Rejected']);
            }
        }

        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '404']);

        $employees = User::where('id', $user_timesheet)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifRejectedTimesheet($employee, $year, $month));
        }
        $entry = new Notification_alert;
        $entry->user_id = $user_timesheet;
        $ts_name = date("F", mktime(0, 0, 0, $month, 1)).' - '.$year;
        $entry->message = "Your Timesheet of $ts_name has been rejected!";
        $entry->importance = 404;
        $entry->save();

        return redirect('/approval/timesheet/p')->with('failed',"You rejected $user_timesheet timereport!");
    }

    public function review(Request $request)
	{
		$Month = date('m');
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::all();
        
        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        // var_dump($Year.intval($Month));
        $approvals = Timesheet_detail::where('ts_status_id', 29)
            ->groupBy('user_timesheet', 'ts_task');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Year.intval($Month));
        } else {
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Year.intval($Month));
        }

        $approvals = $approvals->get();
		return view('review.finance', compact('approvals', 'yearsBefore', 'Month', 'Year', 'employees'));
	}

    public function ts_preview($user_id, $year, $month)
	{
		// $year = Crypt::decrypt($year);
        // $month = Crypt::decrypt($month);
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Create a DateTime object for the first day of the selected month
        $dateToCount = new DateTime("$year-$month-01");

        // Get the last day of the selected month
        $lastDay = $dateToCount->format('t');

        // Initialize a counter for weekdays
        $totalWeekdays = 0;

        // Loop through each day of the month and count weekdays
        for ($day = 1; $day <= $lastDay; $day++) {
            // Set the day of the month
            $dateToCount->setDate($year, $month, $day);
            
            // Check if the day is a weekday (Monday to Friday)
            if ($dateToCount->format('N') <= 5) {
                $totalWeekdays++;
            }
        }

        $totalHours = $totalWeekdays * 8; 

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', $user_id)->get();

        $user_info = User::find($user_id);

        $workflow = Timesheet_detail::where('user_timesheet', $user_id)->where('month_periode', $year.intval($month))->get(); 

        $assignment = DB::table('project_assignment_users')
            ->join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.*', 'project_assignments.*')
            ->where('project_assignment_users.user_id', '=', $user_id)
            ->whereMonth('project_assignment_users.periode_start', '<=', $month)
            ->whereMonth('project_assignment_users.periode_end', '>=', $month)
            ->whereYear('project_assignment_users.periode_start', $year)
            ->whereYear('project_assignment_users.periode_end', $year)
            ->where('project_assignments.approval_status', 29)
            ->get(); 

        $leaveApproved = [];
        $checkLeaveApproval = Leave_request::where('req_by', $user_id)->pluck('id');
        foreach ($checkLeaveApproval as $chk){
            $checkApp = Leave_request_approval::where('leave_request_id', $chk)->where('status', 29)->pluck('leave_request_id')->first();
            if(!empty($checkApp)){
                $leaveApproved[] = $checkApp;
            }
        }

        $leave_day = Leave_request::where('req_by', $user_id)->whereIn('id', $leaveApproved)->pluck('leave_dates')->toArray();

        $formattedDates = [];
        foreach ($leave_day as $dateString) {
            $dateArray = explode(',', $dateString);
            foreach ($dateArray as $dateA) {
                $formattedDates[] = date('Y-m-d', strtotime($dateA));
            }
        }

        $surat_penugasan = Surat_penugasan::where('user_id', $user_id)->pluck('ts_date')->toArray();
        $srtDate = [];
        foreach ($surat_penugasan as $ts_date_srt) {
            $dateArraySrt = explode(',', $ts_date_srt);
            foreach ($dateArraySrt as $dateSrt) {
                $srtDate[] = date('Y-m-d', strtotime($dateSrt));
            }
        }

        $assignmentNames = $assignment->pluck('project_name')->implode(', ');
        if($assignment->isEmpty()){
            $assignmentNames = "None";
        }

        $info = [];
        $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $month)
                ->whereYear('ts_date', $year)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', $user_id)
                ->first();
        if ($lastUpdate) {
            $status = Approval_status::where('approval_status_id', $lastUpdate->ts_status_id)->pluck('status_desc')->first();
            if(!$status){
                $status = "Unknown Status";
            }
            $lastUpdatedAt = $lastUpdate->updated_at;
        } else {
            $status = 'None';
            $lastUpdatedAt = 'None';
        }
        $info[] = compact('status', 'lastUpdatedAt');
        // return response()->json($activities);
        return view('approval.ts_preview', compact('year', 'month', 'totalHours', 'info', 'assignmentNames', 'user_id', 'srtDate', 'startDate','endDate', 'formattedDates'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }
}
