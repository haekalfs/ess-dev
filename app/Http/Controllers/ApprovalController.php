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
//Medical
use App\Models\Medical;
use App\Models\Emp_medical_balance;
use App\Models\Medical_details;
use App\Models\Medical_approval;
use App\Jobs\NotifyMedicalRejected;
use App\Jobs\NotifyMedicalApproved;
use App\Jobs\NotifyMedicalToFinance;
use App\Models\Cutoffdate;
use App\Models\Holidays;
use App\Models\Medical_payment;
use App\Models\Log;
// use App\Http\Controllers\GlobalDateTime;
//medical
use App\Models\Notification_alert;
use App\Models\Position;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Surat_penugasan;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApprovalController extends Controller
{
    public function index()
    {
        // $accessController = new AccessController();
        // $result = $accessController->usr_acc(202);
        $dateCut = Cutoffdate::find(1);
        $currentDay = date('j');
        if ($currentDay >= $dateCut->start_date && $currentDay <= $dateCut->closed_date) {
            $text = "Timesheet Submission has begin, remember timesheet approvals is only available on $dateCut->start_date & $dateCut->closed_date";
            Session::flash('marquee', $text);
        }
        $tsCount = Timesheet_detail::whereNotIn('ts_status_id', ['30', '404', '29', '10'])
            ->where('RequestTo', Auth::user()->id)
            ->groupBy('user_timesheet', 'month_periode')
            ->distinct('user_timesheet', 'month_periode')
            ->count();

        $pCount = Project_assignment::where('approval_status', 40)->count();

        $leaveCount = Leave_request_approval::whereNotIn('status', ['20', '30', '29', '404'])
            ->where('RequestTo', Auth::user()->id)
            ->groupBy('leave_request_id')
            ->distinct('leave_request_id')
            ->count();

        $reimbCount = Reimbursement_approval::where('status', 20)
            ->where('RequestTo', Auth::user()->id)
            ->groupBy('reimbursement_id')
            ->distinct('reimbursement_id')
            ->count();

        $ts_approver = Timesheet_approver::where('id', [99])->pluck('approver')->toArray();
        $medCount = Medical_approval::whereIn('RequestTo', $ts_approver)
            ->where('RequestTo', Auth::user()->id)
            ->whereNotIn('status', [20, 29, 404])
            ->count();
        return view('approval.main', ['tsCount' => $tsCount, 'pCount' => $pCount, 'reimbCount' => $reimbCount, 'leaveCount' => $leaveCount, 'medCount' => $medCount]);
    }

    public function history($yearSelected = NULL)
    {
        $accessController = new AccessController();
        $result = $accessController->usr_acc(204);

        $history = Log::groupBy(['user_id', 'intended_for', 'created_at'])->orderBy('created_at', 'desc')->get();

        return view('approval.history', ['history' => $history]);
    }

    public function timesheet_approval(Request $request)
    {
        $Month = Carbon::now()->subMonth()->format('m');
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::all();

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        $currentYear = date('Y');

        // Get the current day of the month
        $currentDay = date('j');

        $checkUserPost = Auth::user()->users_detail->position->id;
        $getHighPosition = Position::where('position_level', 1)->pluck('id')->toArray();

        $ts_approver = Timesheet_approver::where('group_id', 1)->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
        }
        $dateCut = Cutoffdate::find(2);

        // Check if the current day is within the range 5-8
        if ($currentDay >= $dateCut->start_date && $currentDay <= $dateCut->closed_date) {
            if (in_array($checkUserPost, $getHighPosition)) {
                $Check = Timesheet_detail::select('*')
                    ->where('month_periode', $Year . intval($Month))
                    ->whereNotIn('ts_status_id', [10])
                    ->whereNotIn('RequestTo', $ts_approver)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 WHEN ts_status_id = 15 THEN 1 ELSE 0 END)')
                    ->pluck('user_timesheet')
                    ->toArray();
                if (!empty($Check)) {
                    $approvals = Timesheet_detail::select('*')
                        ->where('month_periode', $Year . intval($Month))
                        ->where('RequestTo', Auth::user()->id)
                        ->whereIn('user_timesheet', $Check)
                        ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                } else {
                    $approvals = Timesheet_detail::select('*')
                        ->where('RequestTo', 'xxhaekalsxx')
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                }
            } else {
                $Check = Timesheet_detail::select('*')
                    ->whereNotIn('ts_status_id', [10, 15])
                    ->whereNotIn('RequestTo', [Auth::user()->id])
                    ->where('month_periode', $Year . intval($Month))
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 404 THEN 0 ELSE 1 END)')
                    ->pluck('user_timesheet')
                    ->toArray();
                if (!empty($Check)) {
                    if (in_array($checkUserPost, [24])) {
                        $checkApprovalPC = Timesheet_detail::select('*')
                            ->whereIn('priority', [3, 4])
                            ->whereIn('user_timesheet', $Check)
                            ->where('month_periode', $Year . intval($Month))
                            ->groupBy('user_timesheet', 'month_periode')
                            ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 ELSE 0 END)')
                            ->pluck('user_timesheet')
                            ->toArray();
                        if (!empty($checkApprovalPC)) {
                            $checkRowsLeft = Timesheet_detail::select('*')
                                ->where('RequestTo', Auth::id())
                                ->where('ts_status_id', 20)
                                ->whereNotIn('user_timesheet', $checkApprovalPC)
                                ->where('month_periode', $Year . intval($Month))
                                ->groupBy('user_timesheet', 'month_periode')
                                ->pluck('user_timesheet')
                                ->toArray();
                            if ($checkRowsLeft) {
                                $approvals = Timesheet_detail::select('*')
                                    ->where('RequestTo', Auth::user()->id)
                                    ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                    ->whereIn('user_timesheet', $checkRowsLeft)
                                    ->where('month_periode', $Year . intval($Month))
                                    ->groupBy('user_timesheet', 'month_periode')
                                    ->get();
                            } else {
                                $approvals = Timesheet_detail::select('*')
                                    ->where('RequestTo', Auth::user()->id)
                                    ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                    ->whereIn('user_timesheet', $checkApprovalPC)
                                    ->where('month_periode', $Year . intval($Month))
                                    ->groupBy('user_timesheet', 'month_periode')
                                    ->get();
                            }
                        } else {
                            $checkApprovalNonPC = Timesheet_detail::select('*')
                                ->whereIn('user_timesheet', $Check)
                                ->where('month_periode', $Year . intval($Month))
                                ->groupBy('user_timesheet', 'month_periode')
                                ->pluck('user_timesheet')
                                ->toArray();
                            if (!empty($checkApprovalNonPC)) {
                                $approvals = Timesheet_detail::select('*')
                                    ->where('RequestTo', Auth::user()->id)
                                    ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                    ->whereNotIn('user_timesheet', $checkApprovalNonPC)
                                    ->where('month_periode', $Year . intval($Month))
                                    ->groupBy('user_timesheet', 'month_periode')
                                    ->get();
                            } else {
                                $approvals = Timesheet_detail::select('*')
                                    ->where('month_periode', $Year . intval($Month))
                                    ->where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
                                    ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                    ->groupBy('user_timesheet', 'month_periode')
                                    ->get();
                            }
                        }
                    } else {
                        $approvals = Timesheet_detail::select('*')
                            ->where('RequestTo', Auth::user()->id)
                            ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                            ->whereIn('user_timesheet', $Check)
                            ->where('month_periode', $Year . intval($Month))
                            ->groupBy('user_timesheet', 'month_periode')
                            ->get();
                    }
                } else {
                    $approvals = Timesheet_detail::select('*')
                        ->where('month_periode', $Year . intval($Month))
                        ->where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
                        ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                }
            }

            //Notification untuk Timesheet Approval adalah 2 sedangkan untuk status di tiap user 2A
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
            return view('approval.timesheet_approval', ['notify' => $notify, 'notifyMonth' => $notifyMonth, 'notifyYear' => $notifyYear, 'approvals' => $approvals, 'yearsBefore' => $yearsBefore, 'Month' => $Month, 'Year' => $Year, 'employees' => $employees]);
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

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year . intval($month))->get();

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [40, 45, 55, 60])->pluck('approver');
        $checkUserDir = $timesheetApproversDir->toArray();

        $Check = DB::table('timesheet_details')
            ->select('*')
            ->where('month_periode', $year . intval($month))
            ->where('user_timesheet', $user_timesheet)
            ->whereNotIn('ts_status_id', [10, 15, 29])
            ->whereNotIn('RequestTo', $checkUserDir)
            ->groupBy('user_timesheet', 'month_periode')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 ELSE 0 END)')
            ->count();

        if (!empty($Check)) {
            $tsStatusId = 29;
            Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
                ->where('ts_user_id', $user_timesheet)
                ->update(['ts_status_id' => $tsStatusId]);
        } else {
            $checkTotalRows = DB::table('timesheet_details')
                ->select('*')
                ->where('month_periode', $year . intval($month))
                ->where('user_timesheet', $user_timesheet)
                ->whereNotIn('ts_status_id', [10, 15])
                ->count();
            if ($checkTotalRows == 1) {
                $tsStatusId = 29;
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
                    ->where('ts_user_id', $user_timesheet)
                    ->update(['ts_status_id' => $tsStatusId]);
            } else {
                $tsStatusId = 30;
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
                    ->where('ts_user_id', $user_timesheet)
                    ->update(['ts_status_id' => $tsStatusId]);
            }
        }

        foreach ($countRows as $row) {
            $tsStatusId = '30';
            $activity = 'Approved';

            switch (true) {
                case in_array(Auth::user()->id, $checkUserDir):
                    $tsStatusId = '29';
                    $activity = 'Approved';

                    //soon fixed
                    $entry = new Notification_alert;
                    $entry->user_id = 'suryadi';
                    $entry->message = "Emp's Timesheet Pending!";
                    $entry->importance = 1;
                    $entry->month_periode = $year . intval($month);
                    $entry->type = "2";
                    $entry->save();
                    break;
                default:
                    $tsStatusId = '30';
                    break;
            }

            $approve = Timesheet_detail::where('month_periode', $year . intval($month))
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

            // $Check = DB::table('timesheet_details')
            //     ->select('*')
            //     ->whereYear('date_submitted', $currentYear)
            //     ->whereNotIn('ts_status_id', [10, 15])
            //     ->whereNotIn('RequestTo', [Auth::user()->id])
            //     ->groupBy('user_timesheet', 'month_periode')
            //     ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 0 ELSE 1 END)')
            //     ->pluck('user_timesheet')
            //     ->toArray();

            // if (!empty($Check)) {
            //     Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
            //         ->where('ts_user_id', $user_timesheet)
            //         ->update(['ts_status_id' => $tsStatusId]);
            // } else {
            //     Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
            //         ->where('ts_user_id', $user_timesheet)
            //         ->update(['ts_status_id' => $tsStatusId]);
            // }
        }

        $approverName = Auth::user()->name;
        $entry = new Notification_alert;
        $entry->user_id = $user_timesheet;
        $ts_name = date("F", mktime(0, 0, 0, $month, 1)) . ' - ' . $year;
        $entry->message = "Your Timesheet of $ts_name has been Approved! by $approverName";
        $entry->importance = 1;
        $entry->month_periode = $year . intval($month);
        $entry->type = "2A";
        $entry->save();

        Log::create([
            'user_id' => Auth::id(),
            'type' => 2,
            'message' => 'Timesheet has been approved by ' . Auth::user()->name,
            'intended_for' => $user_timesheet,
            'importance' => 1
        ]);

        //perlu dirombak karena jika reject salah satu, quota ini harus di deduct// removed 5 year term
        $weekendReplacementInCurrentMonth = Surat_penugasan::where('user_id', $user_timesheet)->whereMonth('ts_date', $month)->whereYear('ts_date', $year)->count();
        $countWeekendReplacement = Emp_leave_quota::where('user_id', $user_timesheet)
            ->where('leave_id', 100)
            ->where('active_periode', '>=', date('Y-m-d'))
            ->sum('quota_left');

        $getYear = date('Y');
        $expirationYear = $getYear + 1;

        if (!$weekendReplacementInCurrentMonth) {
            Emp_leave_quota::updateOrCreate([
                'user_id' => Auth::user()->id,
                'leave_id' => 100,
            ], [
                'quota_left' => 0,
                'active_periode' => date('Y-m-d'),
                'expiration' => "$expirationYear-03-31", //this should be change to dynamic
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

        return redirect('/approval/timesheet/p')->with('success', "You approved $user_timesheet timereport!");
    }

    public function reject(Request $request, $user_timesheet, $year, $month)
    {
        date_default_timezone_set("Asia/Jakarta");

        $validator = Validator::make($request->all(), [
            'reject_notes' => 'sometimes'
        ]);

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year . intval($month))->get();

        foreach ($countRows as $row) { ///test buat dihapus nnti karna double loops

            $reject = Timesheet_detail::where('month_periode', $year . intval($month))
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

        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date', $month)
            ->where('ts_user_id', $user_timesheet)
            ->update(['ts_status_id' => '404']);

        $employees = User::where('id', $user_timesheet)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifRejectedTimesheet($employee, $year, $month));
        }
        $entry = new Notification_alert;
        $entry->user_id = $user_timesheet;
        $ts_name = date("F", mktime(0, 0, 0, $month, 1)) . ' - ' . $year;
        $entry->message = "Your Timesheet of $ts_name has been rejected!";
        $entry->importance = 404;
        $entry->month_periode = $year . intval($month);
        $entry->type = "2A";
        $entry->save();

        Log::create([
            'user_id' => Auth::id(),
            'type' => 2,
            'message' => 'Timesheet has been rejected by ' . Auth::user()->name,
            'intended_for' => $user_timesheet,
            'importance' => 1
        ]);

        return redirect('/approval/timesheet/p')->with('failed', "You rejected $user_timesheet timereport!");
    }

    public function ts_preview($user_id, $year, $month)
    {
        $user_id = Crypt::decrypt($user_id);
        $year = Crypt::decrypt($year);
        $month = Crypt::decrypt($month);
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

        $workflow = Timesheet_detail::where('user_timesheet', $user_id)->where('month_periode', $year . intval($month))->get();

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
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk)->where('status', 29)->pluck('leave_request_id')->first();
            if (!empty($checkApp)) {
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

        $surat_penugasan = Surat_penugasan::where('user_id', $user_id)->pluck('ts_date')->toArray();
        $srtDate = [];
        foreach ($surat_penugasan as $ts_date_srt) {
            $dateArraySrt = explode(',', $ts_date_srt);
            foreach ($dateArraySrt as $dateSrt) {
                $srtDate[] = date('Y-m-d', strtotime($dateSrt));
            }
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

        $assignmentNames = $assignment->pluck('project_name')->implode(', ');
        if ($assignment->isEmpty()) {
            $assignmentNames = "None";
        }

        $info = [];
        $lastUpdate = DB::table('timesheet')
            ->whereMonth('ts_date', $month)
            ->whereNull('ts_type')
            ->whereYear('ts_date', $year)
            ->orderBy('updated_at', 'desc')
            ->where('ts_user_id', $user_id)
            ->first();
        if ($lastUpdate) {
            $status = Approval_status::where('approval_status_id', $lastUpdate->ts_status_id)->pluck('status_desc')->first();
            if (!$status) {
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
        return view('approval.ts_preview', compact('year', 'month', 'getTotalDays', 'totalHours', 'info', 'formattedDatesWeekendRepl', 'assignmentNames', 'user_id', 'srtDate', 'startDate', 'endDate', 'formattedDates', 'formattedDatesHoliday'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }

    public function holidayApproval()
    {
        $accessController = new AccessController();
        $result = $accessController->usr_acc(204);

        $listHolidays = Holidays::where('status', FALSE)->groupBy('surat_edar')->get();
        return view('approval.holidays', ['holidaysList' => $listHolidays]);
    }

    public function retrieveHolidaysData($id)
    {
        // Get the Timesheet records between the start and end dates
        $itemData = Holidays::where('surat_edar', $id)->where('status', FALSE)->get();

        return response()->json($itemData);
    }

    public function approve_holidays($docId)
    {
        date_default_timezone_set("Asia/Jakarta");
        Holidays::where('surat_edar', $docId)->where('status', FALSE)->update(['status' => TRUE, 'approvedBy' => Auth::id()]);

        Session::flash('success', "You approved the holiday dates!");
        return redirect()->back();
    }

    public function reject_holidays($docId)
    {
        date_default_timezone_set("Asia/Jakarta");
        Holidays::where('surat_edar', $docId)->where('status', FALSE)->update(['status' => 404, 'approvedBy' => Auth::id()]);

        Session::flash('success', "You approved the holiday dates!");
        return redirect()->back();
    }

    // Medical Approval
    public function medical_approval()
    {
        $name = User::all();

        $checkUserPost = Auth::user()->users_detail->position->id;
        $ts_approver = Timesheet_approver::whereIn('id', [99])->pluck('approver')->toArray();
        //MAS RONNY
        // if (in_array($checkUserPost, [21])) {
        // $Check = Medical_approval::whereNotIn('RequestTo', $ts_approver)
        // ->whereNotIn('RequestTo', [Auth::user()->id])
        // ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 20 THEN 1 ELSE 0 END)')
        // ->groupBy('RequestTo')
        // ->pluck('RequestTo')
        // ->toArray();
        // var_dump($Check);
        // if (!empty($Check)) {
        // $medical = Medical_approval::where('RequestTo', Auth::user()->id)
        // ->whereNotIn('status', [20, 29, 404])
        // ->whereNotIn('RequestTo', $Check)
        // ->groupBy('medical_id')
        // ->get();
        // } else {
        // //gagal
        // $medical = Medical_approval::where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
        // ->whereNotIn('status', [20, 29, 404, 30, 15, 10])
        // ->groupBy('medical_id')
        // ->get();
        // }
        // } else {
        // HR
        $medical = Medical_approval::whereIn('RequestTo', $ts_approver)
            ->where('RequestTo', Auth::user()->id)
            ->whereNotIn('status', [20, 29, 404])
            ->groupBy('medical_id')
            ->get();
        // }

        return view('/approval/medical_approval', ['medical' => $medical, 'name' => $name,]);
    }

    public function approval_edit($id)
    {

        $med = Medical::findOrFail($id);
        $userMedId = $med->user_id;
        $medDet = Medical_details::where('medical_id', $med->id)->get();
        $user = Users_detail::where('user_id', $userMedId)->first();

        $hired_date = $user->hired_date; // assuming $hired_date is in Y-m-d format
        $current_date = date('Y-m-d'); // get the current date

        // create DateTime objects from the hired_date and current_date values
        $hired_date_obj = new DateTime($hired_date);
        $current_date_obj = new DateTime($current_date);

        // calculate the difference between the hired_date and current_date
        $diff = $current_date_obj->diff($hired_date_obj);

        // get the total number of years from the difference object
        $total_years_of_service = $diff->y;

        $medAppUpdate = Medical_approval::where('medical_id', $med->id)
            ->whereIn('RequestTo', [Auth::user()->id])
            ->whereNotIN('status', [15])
            ->orderByDesc('updated_at')
            ->orderBy('medical_id')
            ->first();

        $currentYear = Carbon::now()->year;
        $medBalance = Emp_medical_balance::where('user_id', $userMedId)
            ->where('active_periode', '<=', $currentYear)->where('expiration', '>=', $currentYear)
            ->first();
        $medBalance = Emp_medical_balance::where('user_id', $med->user_id)->first();

        $position = Position::all();
        return view(
            'medical.medical_edit_approval',
            [
                'med' => $med,
                'medDet' => $medDet,
                'medAppUpdate' => $medAppUpdate,
                'medBalance' => $medBalance,
                'position' => $position,
                'total_years_of_service' => $total_years_of_service
            ]
        );
    }

    public function update_approval(Request $request, $mdet_id, $medical_id)
    {

        $medDet = Medical_details::where('mdet_id', $medical_id)->first();
        $request->validate([
            'input_mdet_amount_approved' => 'sometimes',
        ]);
        $medDet->amount_approved = $request->input_mdet_amount_approved;
        // $medDet->mdet_desc = $request->input_mdet_desc;


        $medDet->save();

        return redirect()->back()->with('success', 'Medical Approval Edit Success');
    }

    public function approve_medical(Request $request, $id)
    {

        $request->validate([
            'input_approve_note' => 'sometimes',
        ]);

        $user_med = Medical::where('id', $id)->first(); // Mengambil objek Medical dengan ID tertentu
        $userNameRequestor = $user_med->user->name; // Mengambil nama pengguna terkait
        $userMedId = $user_med->user_id;
        $totalAmountApproved = $request->input('totalAmountApprovedInput');

        $medApproveFinance = Timesheet_approver::whereIn('id', [99])->pluck('approver')->toArray();

        $medApprove = medical_approval::whereIn('RequestTo', $medApproveFinance)->where('RequestTo', Auth::user()->id)->where('medical_id', $id)->first();

        $medApprove->status = 29;
        $medApprove->approval_notes = $request->input_approve_note;
        $medApprove->approval_date = $request->date_approved;
        $medApprove->total_amount_approved = $totalAmountApproved;
        $medApprove->save();

        $medPay = Medical_payment::where('medical_id', $id)->firstorFail();
        $medPay->paid_status = 20;
        $medPay->save();

        $userFinance = $medPay->payment_approver;
        $Finance = User::where('id', $userFinance)->first();
        $emailFinance = $Finance->email;
        // dd($emailFinance);
        $MedId = $user_med->id;

        $employees = User::where('id', $userMedId)->get();
        $userName = Auth::user()->name;

        // foreach ($employees as $employee) {
        //     dispatch(new NotifyMedicalApproved($employee, $userName, $MedId));
        // }

        // dispatch(new NotifyMedicalToFinance($employee, $emailFinance, $MedId));

        return redirect('/approval/medical')->with('success', "You've Approved $userNameRequestor Medical Reimburse No. MED_$MedId");
    }

    public function reject_medical(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'input_reject_note' => 'required'
        ]);

        $user_med = Medical::where('id', $id)->first(); // Mengambil objek Medical dengan ID tertentu
        $userName = $user_med->user->name; // Mengambil nama pengguna terkait
        $userMedId = $user_med->user_id;

        $medApproveFinance = Timesheet_approver::whereIn('id', [99])->pluck('approver')->toArray();

        $medApprove = medical_approval::whereIn('RequestTo', $medApproveFinance)->where('RequestTo', Auth::user()->id)->where('medical_id', $id)->first();

        $medApprove->status = 404;
        $medApprove->approval_notes = $request->input_approve_note;
        $medApprove->approval_date = $request->date_approved;
        $medApprove->save();

        $medical = Medical_payment::where('medical_id', $id)->firstorFail();
        $medical->paid_status = 404;
        $medical->save();

        $employees = User::where('id', $userMedId)->get();
        $approverName = Auth::user()->name;
        $MedId = $id;

        foreach ($employees as $employee) {
            dispatch(new NotifyMedicalRejected($employee, $MedId, $approverName));
        }

        return redirect('/approval/medical')->with('success', "You rejected $userName Medical Reimburse !");
    }

    public function reject_med_det($id, $mdet_id)
    {
        $medical_det = Medical_details::where('medical_id', $id)->where('mdet_id', $mdet_id)->first();
        $medical_det->amount_approved = 0;
        $medical_det->status = 0;
        $medical_det->save();
        // dd($medical_det);

        return redirect()->back()->with('success', "You rejected some Medical Attachment !");
    }
}
