<?php

namespace App\Http\Controllers;

use App\Exports\TimesheetExport;
use App\Mail\ApprovalTimesheet;
use App\Mail\RejectedTimesheet;
use App\Models\Approval_status;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
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

        $tsCount = Timesheet_detail::whereIn('ts_status_id', ['20', '30', '40', '25', '404'])
             ->where(function($query) {
                 $query->where('RequestTo', Auth::user()->id);
             })
             ->count();

        $pCount = Project_assignment::where('approval_status', 40)->count();

		return view('approval.main', ['tsCount' => $tsCount, 'pCount' => $pCount]);
	}

    public function timesheet_approval()
    {
        $currentYear = date('Y');

        // Get the current day of the month
        $currentDay = date('j');

        // Check if the current day is within the range 5-8
        if ($currentDay >= 5 && $currentDay <= 8) {
            $approvals = DB::table('timesheet_details')
                ->select('*')
                ->whereYear('date_submitted', $currentYear)
                ->where('RequestTo', Auth::user()->id)
                ->whereNotIn('ts_status_id', [29, 404, 30])
                ->groupBy('user_timesheet', 'month_periode')
                ->get();
            return view('approval.timesheet_approval', ['approvals' => $approvals]);
        } else {
            // Handle the case when the date is not within the range
            return redirect()->back()->with('failed', 'This page can only be accessed between the 5th - 8th of each month.');
        }
    }

    public function approve($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [45, 40])->pluck('approver');
        $checkUserDir = $timesheetApproversDir->toArray();
        foreach ($countRows as $row) {
            $tsStatusId = '30';
            $activity = 'Approved';
        
            switch (true) {
                case in_array(Auth::user()->id, $checkUserDir):
                    $tsStatusId = '29';
                    $activity = 'All Approved';
                    break;
                default:
                    $tsStatusId = '30';
                    break;
            }
        
            Timesheet_detail::where('month_periode', $year.$month)
                ->where('user_timesheet', $user_timesheet)
                ->where('RequestTo', Auth::user()->id)
                ->where('ts_task_id', $row->ts_task_id)
                ->update(['ts_status_id' => $tsStatusId, 'activity' => $activity]);

            Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
                ->where('ts_user_id', $user_timesheet)
                ->update(['ts_status_id' => $tsStatusId]);
        }        

        Session::flash('success',"You approved $user_timesheet timereport!");
        return redirect()->back();
    }

    public function reject($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', Auth::user()->id)
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '404', 'activity' => 'Rejected']);
        }

        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '404']);

        $employees = User::where('id', $user_timesheet)->get();

        foreach ($employees as $employee) {
            $notification = new RejectedTimesheet($employee, $year, $month);
            Mail::send('mailer.rejected_timesheet', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                        ->subject($notification->emailSubject());
            });
        }
        Session::flash('failed',"You rejected $user_timesheet timereport!");
        return redirect()->back();
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

    public function ts_preview($id, $year, $month)
	{
		// $year = Crypt::decrypt($year);
        // $month = Crypt::decrypt($month);
        // Set the default time zone to Jakarta 
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', $id)->get();
        
        $user_info = User::find($id);

        $workflow = Timesheet_detail::where('user_timesheet', $id)->where('month_periode', $year.intval($month))->get();

        $info = [];
        $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $month)
                ->whereYear('ts_date', $year)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', $id)
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
        return view('approval.ts_preview', compact('year', 'month','info', 'id'), ['timesheet' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
	}
}
