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
use App\Models\Cutoffdate;
//medical
use App\Models\Notification_alert;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Reimbursement_approval;
use App\Models\Surat_penugasan;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateTime;
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
        // $accessController = new AccessController();
        // $result = $accessController->usr_acc(202);

        $tsCount = Timesheet_detail::whereNotIn('ts_status_id', ['30', '404', '29', '10'])
            ->where(function ($query) {
                $query->where('RequestTo', Auth::user()->id)
                    ->groupBy('user_timesheet', 'month_periode');
            })
            ->groupBy('user_timesheet', 'month_periode')
            ->count();

        $pCount = Project_assignment::where('approval_status', 40)->count();
        $leaveCount = Leave_request_approval::whereNotIn('status', ['20', '30', '29', '404'])
            ->where(function ($query) {
                $query->where('RequestTo', Auth::user()->id);
            })
            ->count();

        $reimbCount = Reimbursement_approval::whereNotIn('status', ['30', '29', '404'])->groupBy('reimbursement_id')
            ->where(function ($query) {
                $query->where('RequestTo', Auth::user()->id);
            })
            ->count();

        $ts_approver = Timesheet_approver::where('id', [99])->pluck('approver')->toArray();
        $medCount = Medical_approval::whereIn('RequestTo', $ts_approver)
        ->where('RequestTo', Auth::user()->id)
        ->whereNotIn('status', [20, 29, 404])
        ->count();
        return view('approval.main', ['tsCount' => $tsCount, 'pCount' => $pCount, 'reimbCount' => $reimbCount, 'leaveCount' => $leaveCount, 'medCount' => $medCount]);
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

        $ts_approver = Timesheet_approver::whereIn('id', [40, 45, 55, 60])->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
        }
        $dateCut = Cutoffdate::find(2);

        // Check if the current day is within the range 5-8
        if ($currentDay >= $dateCut->start_date && $currentDay <= $dateCut->closed_date) {
            if (in_array($checkUserPost, [7, 8, 12])) {
                $Check = Timesheet_detail::select('*')
                    ->whereYear('date_submitted', $Year)
                    ->where('month_periode', $Year . intval($Month))
                    ->whereNotIn('ts_status_id', [10])
                    ->whereNotIn('RequestTo', $ts_approver)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 WHEN ts_status_id = 15 THEN 1 ELSE 0 END)')
                    ->pluck('user_timesheet')
                    ->toArray();
                if (!empty($Check)) {
                    $approvals = Timesheet_detail::select('*')
                        ->whereYear('date_submitted', $Year)
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
                    ->whereYear('date_submitted', $Year)
                    ->where('month_periode', $Year . intval($Month))
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 404 THEN 0 ELSE 1 END)')
                    ->pluck('user_timesheet')
                    ->toArray();
                if (!empty($Check)) {
                    if (in_array($checkUserPost, [24])) {
                        $checkApprovalPC = Timesheet_detail::select('*')
                            ->whereIn('priority', [3, 4])
                            ->whereYear('date_submitted', $Year)
                            ->whereIn('user_timesheet', $Check)
                            ->where('month_periode', $Year . intval($Month))
                            ->groupBy('user_timesheet', 'month_periode')
                            ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 ELSE 0 END)')
                            ->pluck('user_timesheet')
                            ->toArray();
                        if (!empty($checkApprovalPC)) {
                            $approvals = Timesheet_detail::select('*')
                                ->where('RequestTo', Auth::user()->id)
                                ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                ->whereIn('user_timesheet', $checkApprovalPC)
                                ->whereYear('date_submitted', $Year)
                                ->where('month_periode', $Year . intval($Month))
                                ->groupBy('user_timesheet', 'month_periode')
                                ->get();
                        } else {
                            //gagal
                            $approvals = Timesheet_detail::select('*')
                                ->whereYear('date_submitted', $Year)
                                ->where('month_periode', $Year . intval($Month))
                                ->where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
                                ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                                ->groupBy('user_timesheet', 'month_periode')
                                ->get();
                        }
                    } else {
                        $approvals = Timesheet_detail::select('*')
                            ->where('RequestTo', Auth::user()->id)
                            ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                            ->whereIn('user_timesheet', $Check)
                            ->whereYear('date_submitted', $Year)
                            ->where('month_periode', $Year . intval($Month))
                            ->groupBy('user_timesheet', 'month_periode')
                            ->get();
                    }
                } else {
                    $approvals = Timesheet_detail::select('*')
                        ->whereYear('date_submitted', $Year)
                        ->where('month_periode', $Year . intval($Month))
                        ->where('RequestTo', "xxxxxxxxxhaekalsxxxxx")
                        ->whereNotIn('ts_status_id', [29, 404, 30, 15])
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                }
            }

            $getNotification = Notification_alert::where('type', 2)->whereNull('read_stat')->first();
            if($getNotification){
                $notifyMonth = substr($getNotification->month_periode, 4);
                $notify = $getNotification->id;
            } else {
                $notifyMonth = false;
                $notify = false;
            }

            $setToRead = Notification_alert::where('type', 2)
                ->whereNull('read_stat')
                ->where('month_periode', $Year . intval($Month))
                ->get();

            if ($setToRead->count() > 0) {
                Notification_alert::where('type', 2)
                    ->whereNull('read_stat')
                    ->where('month_periode', $Year . intval($Month))
                    ->update(['read_stat' => 1]);
            }
            return view('approval.timesheet_approval', ['notify' => $notify,'notifyMonth' => $notifyMonth, 'approvals' => $approvals, 'yearsBefore' => $yearsBefore, 'Month' => $Month, 'Year' => $Year, 'employees' => $employees]);
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

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year . $month)->get();

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [40, 45, 55, 60])->pluck('approver');
        $checkUserDir = $timesheetApproversDir->toArray();

        $Check = DB::table('timesheet_details')
                    ->select('*')
                    ->where('month_periode', $year.$month)
                    ->where('user_timesheet', $user_timesheet)
                    ->whereNotIn('ts_status_id', [10, 15, 29])
                    ->whereNotIn('RequestTo', $checkUserDir)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 1 ELSE 0 END)')
                    ->count();

        if (!empty($Check)) {
            $tsStatusId = 29;
            Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
            ->where('ts_user_id', $user_timesheet)
            ->update(['ts_status_id' => $tsStatusId]);
        } else {
            $checkTotalRows = DB::table('timesheet_details')
                    ->select('*')
                    ->where('month_periode', $year.$month)
                    ->where('user_timesheet', $user_timesheet)
                    ->whereNotIn('ts_status_id', [10, 15])
                    ->count();
            if ($checkTotalRows == 1){
                $tsStatusId = 29;
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
                ->where('ts_user_id', $user_timesheet)
                ->update(['ts_status_id' => $tsStatusId]);
            } else {
                $tsStatusId = 30;
                Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
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
                    break;
                default:
                    $tsStatusId = '30';
                    break;
            }

            $approve = Timesheet_detail::where('month_periode', $year . $month)
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
        $entry->month_periode = $year.$month;
        $entry->type = 2;
        $entry->save();

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
                'quota_left' => 1,
                'active_periode' => date('Y-m-d'),
                'expiration' => "$expirationYear-04-1", //this should be change to dynamic
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

            $reject = Timesheet_detail::where('month_periode', $year . $month)
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
        $entry->month_periode = $year.$month;
        $entry->type = 2;
        $entry->save();

        return redirect('/approval/timesheet/p')->with('failed', "You rejected $user_timesheet timereport!");
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

        $surat_penugasan = Surat_penugasan::where('user_id', $user_id)->pluck('ts_date')->toArray();
        $srtDate = [];
        foreach ($surat_penugasan as $ts_date_srt) {
            $dateArraySrt = explode(',', $ts_date_srt);
            foreach ($dateArraySrt as $dateSrt) {
                $srtDate[] = date('Y-m-d', strtotime($dateSrt));
            }
        }

        $assignmentNames = $assignment->pluck('project_name')->implode(', ');
        if ($assignment->isEmpty()) {
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
        return view('approval.ts_preview', compact('year', 'month', 'getTotalDays', 'totalHours', 'info', 'assignmentNames', 'user_id', 'srtDate', 'startDate', 'endDate', 'formattedDates'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
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

        $medAppUpdate = Medical_approval::where('medical_id', $med->id)
        ->whereIn('RequestTo', [Auth::user()->id])
        ->whereNotIN('status', [15])
        ->orderByDesc('updated_at')
        ->orderBy('medical_id')
        ->first();

        $currentYear = Carbon::now()->year;
        $medBalance = Emp_medical_balance::where('user_id', $userMedId)
        ->where('active_periode', '<=', $currentYear) ->where('expiration', '>=', $currentYear)
        ->first();
        $medBalance = Emp_medical_balance::where('user_id', $med->user_id)->first();

        return view('medical.medical_edit_approval', ['med' => $med, 'medDet' => $medDet, 'medAppUpdate' => $medAppUpdate, 'medBalance' => $medBalance]);
    }

    public function update_approval(Request $request, $mdet_id, $medical_id)
    {

        $medDet = Medical_details::where('mdet_id', $medical_id)->first();
        $request->validate([
        'input_mdet_amount_approved' => 'sometimes',
        'input_mdet_desc' => 'sometimes',
        ]);
        $medDet->amount_approved = $request->input_mdet_amount_approved;
        $medDet->mdet_desc = $request->input_mdet_desc;
        $medDet->save();

        return redirect()->back()->with('success', 'Medical Approval Edit Success');
    }

    public function approve_medical(Request $request, $id)
    {

        $request->validate([
        'input_approve_note' => 'required',
        ]);

        $user_med = Medical::where('id', $id)->first(); // Mengambil objek Medical dengan ID tertentu
        $userName = $user_med->user->name; // Mengambil nama pengguna terkait
        $userMedId =$user_med->user_id;
        $totalAmountApproved = $request->input('totalAmountApprovedInput');

        $medApproveFinance = Timesheet_approver::whereIn('id', [99])->pluck('approver')->toArray();

        $medApprove = medical_approval::whereIn('RequestTo', $medApproveFinance)->where('RequestTo', Auth::user()->id)->where('medical_id', $id)->first();

        $medApprove->status = 29;
        $medApprove->approval_notes = $request->input_approve_note;
        $medApprove->approval_date = $request->date_approved;
        $medApprove->save();

        $medical = Medical::findOrFail($id);
        $medical->paid_status = 20;
        $medical->total_amount_approved = $totalAmountApproved;
        $medical->save();

        $currentYear = Carbon::now()->year;

        // Cari $medBalance yang masih aktif dan memiliki tahun yang sama
        $medBalance = Emp_medical_balance::where('user_id', $userMedId)
        ->where('active_periode', '<=', $currentYear) ->where('expiration', '>=', $currentYear)
        ->first();

        if ($medBalance) {
        $balance = $medBalance->medical_balance;
        $balanceAmount = str_replace('.', '', $balance);
        $AmountApproved = str_replace('.', '', $totalAmountApproved);
        $total = $balanceAmount - $AmountApproved;
        $formattedTotal = number_format($total, 0, ',', '.');

        $medBalance->medical_remaining = $formattedTotal;
        $medBalance->save();

        $deducted = $medBalance->medical_deducted;
        $deductedAmount = str_replace('.', '', $deducted);
        $AmountApproved = str_replace('.', '', $totalAmountApproved);
        $totalDeducted = $deductedAmount + $AmountApproved;
        $formattedDeductedTotal = number_format($totalDeducted, 0, ',', '.');

        $medBalance->medical_deducted = $formattedDeductedTotal;
        $medBalance->save();

        $MedId = $medical->med_number;
        $employees = User::where('id', $userMedId)->get();
        $userName = Auth::user()->name;

        foreach ($employees as $employee) {
            dispatch(new NotifyMedicalApproved($employee, $userName, $MedId));
        }

        return redirect('/approval/medical')->with('success', "You've Approved $userName Medical Reimburse ");
        } else {
        return response()->json(['message' => 'Data Emp_medical_balance aktif untuk tahun ' . $currentYear . ' tidak ditemukan'], 404);
        }
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

        $medical = Medical::findOrFail($id);
        $medical->paid_status = 404;
        $medical->save();

        $employees = User::where('id', $userMedId)->get();
        $MedId = $medical->med_number;

        foreach ($employees as $employee) {
            dispatch(new NotifyMedicalRejected($employee, $MedId));
        }

        return redirect('/approval/medical')->with('success', "You rejected $userName Medical Reimburse !");
    }
}
