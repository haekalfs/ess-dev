<?php

namespace App\Http\Controllers;

use App\Models\Approval_status;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Notification_alert;
use App\Models\Surat_penugasan;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use Carbon\Carbon;
use DateTime;
use Exception;
use PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReviewController extends Controller
{
    public function review(Request $request)
    {
        $Month = date('m');
        $Year = date('Y');
        $userSelected = Null;

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        $month_periode = $Year . intval($Month);

        $priorApproval = Timesheet_approver::where('group_id', 1)->pluck('approver')->toArray();

        $userArray = [];

        $employees = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNull('resignation_date');
		})->get();

        $approvals = Timesheet_detail::join('users as u', 'timesheet_details.user_timesheet', '=', 'u.id')
            ->join('users_details as ud', 'timesheet_details.user_timesheet', '=', 'ud.user_id');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $month_periode = $Year . intval($Month);
            $userSelected = $request->showOpt;

            if ($userSelected == 1) {
                $getData = Timesheet_detail::where('month_periode', $month_periode)
                ->whereNotIn('ts_status_id', [10, 15, 30])
                ->whereIn('RequestTo', $priorApproval)
                ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 29 THEN 1 ELSE 0 END)')
                ->groupBy('user_timesheet')
                ->get();
            } else {
                $getData = Timesheet_detail::where('month_periode', $month_periode)
                ->whereNotIn('ts_status_id', [10, 15, 30])->where('user_timesheet', $userSelected)
                ->whereIn('RequestTo', $priorApproval)
                ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 29 THEN 1 ELSE 0 END)')
                ->groupBy('user_timesheet')
                ->get();
            }

            $approvals->joinSub(function ($query) use ($month_periode) {
                $query->select('user_timesheet', DB::raw('MAX(created_at) AS latest_created_at'))
                    ->from('timesheet_details')
                    ->where('ts_status_id', 29)
                    ->where('month_periode', $month_periode)
                    ->groupBy('user_timesheet')
                    ->groupBy('ts_task')
                    ->groupBy('ts_location');
            }, 't', function ($join) {
                $join->on('timesheet_details.user_timesheet', '=', 't.user_timesheet')
                    ->on('timesheet_details.created_at', '=', 't.latest_created_at');
            });
        } else {
            $getData = Timesheet_detail::where('month_periode', $month_periode)
            ->whereNotIn('ts_status_id', [10, 15, 30])
            ->whereIn('RequestTo', $priorApproval)
            ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 29 THEN 1 ELSE 0 END)')
            ->groupBy('user_timesheet')->get();

            $approvals->joinSub(function ($query) use ($month_periode) {
                $query->select('user_timesheet', DB::raw('MAX(created_at) AS latest_created_at'))
                    ->from('timesheet_details')
                    ->where('ts_status_id', 29)
                    ->where('month_periode', $month_periode)
                    ->groupBy('user_timesheet')
                    ->groupBy('ts_task')
                    ->groupBy('ts_location');
            }, 't', function ($join) {
                $join->on('timesheet_details.user_timesheet', '=', 't.user_timesheet')
                    ->on('timesheet_details.created_at', '=', 't.latest_created_at');
            });
        }

        foreach ($getData as $data){
            $userArray[] = $data->user_timesheet;
        }

        $approvals = $approvals->where('timesheet_details.ts_status_id', 29)
            ->whereIn('timesheet_details.user_timesheet', $userArray)
            ->groupBy('timesheet_details.user_timesheet', 'timesheet_details.ts_task', 'timesheet_details.ts_location')
            ->select('timesheet_details.*', 'u.name', 'ud.employee_id')
            ->get();

        $notifyYear = [];
        $notifyMonth = [];
        $notify = false;
        $getNotification = Notification_alert::where('type', 2)->where('user_id', Auth::id())->whereNull('read_stat')->get();
        foreach ($getNotification as $getNotification) {
            if ($getNotification) {
                $notifyMonth[] = substr($getNotification->month_periode, 4);
                $notifyYear[] = substr($getNotification->month_periode, 0, 4);
                $notify = $getNotification->id;
            }
        }

        $setToRead = Notification_alert::where('type', 2)
            ->whereNull('read_stat')
            ->where('user_id', Auth::id())
            ->where('month_periode', $Year . intval($Month))
            ->get();

        if ($setToRead->count() > 0) {
            Notification_alert::where('type', 2)
                ->whereNull('read_stat')
                ->where('user_id', Auth::id())
                ->where('month_periode', $Year . intval($Month))
                ->update(['read_stat' => 1]);
        }
        return view('review.finance', compact('userSelected','notify','notifyMonth','notifyYear', 'approvals', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function ts_preview($user_id, $year, $month)
	{
        $user_id = Crypt::decrypt($user_id);
        $year = Crypt::decrypt($year);
        $month = Crypt::decrypt($month);
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

        //WeekendReplacement
        $weekendReplacement = Surat_penugasan::where('user_id', Auth::user()->id)
            ->where('isTaken', TRUE)
            ->pluck('date_to_replace')
            ->toArray();

        $formattedDatesWeekendRepl = [];
        foreach ($weekendReplacement as $dateString) {
            $formattedDatesWeekendRepl[] = date('Y-m-d', strtotime($dateString));
        }

        $json = null;
        $array = null;
        $cachedData = Cache::get('holiday_data');
        $maxAttempts = 5;
        $attempts = 0;

        // Check if the year of the given date is the current year
        $dateTime = new DateTime($startDate);
        $yearToCheck = $dateTime->format('Y');
        $isCurrentYear = ($yearToCheck == date('Y'));

        while (!$cachedData && $attempts < $maxAttempts) {
            try {
                if (!$isCurrentYear) {
                    // Check if the local file exists before reading it
                    $localFilePath = public_path("holidays_indonesia.json");
                    if (file_exists($localFilePath)) {
                        $json = file_get_contents($localFilePath);
                    } else {
                        $json = file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json");
                    }
                } else {
                    // Use the API to get the data
                    $json = file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json");
                }

                $array = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Handle JSON decoding error
                    throw new Exception('JSON decoding error: ' . json_last_error_msg());
                }

                Cache::put('holiday_data', $array, 60 * 24); // Cache the data for 24 hours
                $cachedData = $array;
            } catch (Exception $e) {
                // Handle exception or log error
                sleep(1); // Wait for 1 second before retrying
                $attempts++;
            }
        }

        if (!$isCurrentYear) {
            // Forget the cache if the year is not the current year
            Cache::forget('holiday_data');
        }

        if (!$cachedData) {
            Session::flash('failed', 'No Internet Connection, Please Try Again Later!');
            return redirect(url()->previous());
        } else {
            $array = $cachedData;
            // Use the cached data
        }

        $formattedDatesHoliday = [];

        foreach ($cachedData as $date => $data) {
            // Check if the 'holiday' key is true for the date
            if (isset($data['holiday']) && $data['holiday'] === true) {
                $formattedDatesHoliday[] = [
                    'date' => date('Y-m-d', strtotime($date)),
                    'summary' => implode(', ', $data['summary'])
                ];
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
                ->whereNull('ts_type')
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
        $getTotalDays = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', $user_id)
        ->groupBy('ts_date')
        ->get()
        ->count();

        // return response()->json($activities);
        return view('review.ts_preview', compact('year', 'month','info', 'formattedDatesHoliday', 'getTotalDays', 'formattedDatesWeekendRepl', 'totalHours', 'assignmentNames', 'user_id', 'srtDate', 'startDate','endDate', 'formattedDates'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }

    public function print_selected($year, $month, $user_timesheet)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $user_info = User::find($user_timesheet);

        $user_info_details = Users_detail::where('user_id', $user_timesheet)->first();
        $user_info_emp_id = $user_info_details->employee_id;

        $subquery = DB::table('timesheet')
            ->select('ts_task', 'ts_location', 'ts_user_id', DB::raw('CAST(incentive AS DECIMAL(10, 2)) AS incentive'), 'ts_task_id', 'ts_id_date', 'allowance')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY ts_id_date ORDER BY CAST(incentive AS DECIMAL(10, 2)) DESC) AS rn')
            ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('ts_user_id', $user_timesheet)
            ->groupBy('ts_id_date', 'ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id');

        $sql = '(' . $subquery->toSql() . ') as subquery';

        $results = DB::table(DB::raw($sql))
            ->mergeBindings($subquery)
            ->select('ts_task', 'ts_location', 'ts_user_id', 'incentive as incentive', 'ts_task_id', 'allowance as max_allowance', DB::raw('COUNT(*) as total_rows'))
            ->where('rn', 1)
            ->groupBy('ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id')
            ->get();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', $user_timesheet)->orderBy('ts_date', 'asc')->get();

        $pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'), ['timesheet' => $activities, 'results' => $results, 'user_info' => $user_info]);
        return $pdf->download('timesheet #' . $user_timesheet . '-' . $year . $month . '.pdf');
    }
}
