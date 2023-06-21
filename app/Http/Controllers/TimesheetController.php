<?php

namespace App\Http\Controllers;

use App\Jobs\SendTimesheetReminderSummary;
use App\Mail\EssMailer;
use App\Mail\TimesheetReminderEmployee;
use App\Models\Additional_fare;
use App\Models\Approval_status;
use App\Models\Company_project;
use App\Models\Cutoffdate;
use App\Models\Emp_leave_quota;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Project_role;
use App\Models\Surat_penugasan;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class TimesheetController extends Controller
{
    public function index($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentMonth = date('m');
        $currentYear = date('Y');
        if ($yearSelected) {
            $currentMonth = 12;
            if ($yearSelected == date('Y')) {
                $currentMonth = date('m');
            }
            $currentYear = $yearSelected;
        }
        $entries = [];
        foreach (range(1, $currentMonth) as $entry) {
            $month = date("F", mktime(0, 0, 0, $entry, 1));
            $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $entry)
                ->whereYear('ts_date', $currentYear)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', Auth::user()->id)
                ->first();
            if ($lastUpdate) {
                $status = Approval_status::where('approval_status_id', $lastUpdate->ts_status_id)->pluck('status_desc')->first();
                if (!$status) {
                    $status = "Unknown Status";
                }
                $encryptYear = Crypt::encrypt($currentYear);
                $encryptMonth = Crypt::encrypt($entry);
                $validStatusIDs = Approval_status::whereIn('id', [2, 3, 6, 8])->pluck('approval_status_id')->toArray();
                $isSubmitted = in_array($lastUpdate->ts_status_id, $validStatusIDs);
                $lastUpdatedAt = $lastUpdate->updated_at;
                $editUrl = "/timesheet/entry/" . $encryptYear . "/" . $encryptMonth;
            } else {
                $encryptYear = Crypt::encrypt($currentYear);
                $encryptMonth = Crypt::encrypt($entry);
                $isSubmitted = false;
                $status = 'None';
                $lastUpdatedAt = 'None';
                $editUrl = "/timesheet/entry/" . $encryptYear . "/" . $encryptMonth;
            }
            $encryptYear = Crypt::encrypt($currentYear);
            $encryptMonth = Crypt::encrypt($entry);
            $previewUrl = "/timesheet/entry/preview/" . $encryptYear . "/" . $encryptMonth;
            $entries[] = compact('month', 'lastUpdatedAt', 'status', 'editUrl', 'previewUrl', 'isSubmitted');
        }
        return view('timereport.timesheet', compact('entries', 'yearsBefore', 'yearSelected'));
    }

    public function getDayStatus($date)
    {
        $cachedData = Cache::get('holiday_data');
        $maxAttempts = 15;
        $attempts = 0;

        while (!$cachedData && $attempts < $maxAttempts) {
            try {
                $json = file_get_contents("https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json");
                $array = json_decode($json, true);
                Cache::put('holiday_data', $array, 60 * 24); // Cache the data for 24 hours
                $cachedData = $array;
            } catch (Exception $e) {
                // Handle exception or log error
                sleep(5); // Wait for 5 seconds before retrying
                $attempts++;
            }
        }

        if (!$cachedData) {
            Session::flash('failed', 'No Internet Connection, Please Try Again Later!');
            return redirect(url()->previous());
        } else {
            $array = $cachedData;
            // Use the cached data
        }

        $leaveApproved = [];
        $checkLeaveApproval = Leave_request::where('req_by', Auth::user()->id)->pluck('id');
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk)->where('status', 29)->pluck('leave_request_id')->first();
            if (!empty($checkApp)) {
                $leaveApproved[] = $checkApp;
            }
        }

        $leave_day = Leave_request::where('req_by', Auth::user()->id)->whereIn('id', $leaveApproved)->pluck('leave_dates')->toArray();

        $formattedDates = [];
        foreach ($leave_day as $dateString) {
            $dateArray = explode(',', $dateString);
            foreach ($dateArray as $dateA) {
                $formattedDates[] = date('Ymd', strtotime($dateA));
            }
        }

        // Check tanggal merah berdasarkan libur nasional
        if (isset($array[$date->format('Ymd')])) {
            return "red";
        }
        // Check tanggal merah berdasarkan hari minggu
        elseif ($date->format('D') === "Sun") {
            return "red";
        } elseif ($date->format('D') === "Sat") {
            return "red";
        }
        // Bukan tanggal merah
        else {
            $dateToCheck = $date->format('Ymd');
            if (in_array($dateToCheck, $formattedDates)) {
                return 2907;
            } else {
                return "";
            }
        }
    }

    public function timesheet_entry($year, $month)
    {
        $year = Crypt::decrypt($year);
        $month = Crypt::decrypt($month);
        $lastUpdate = DB::table('timesheet')
            ->whereMonth('ts_date', $month)
            ->whereYear('ts_date', $year)
            ->orderBy('updated_at', 'desc')
            ->where('ts_user_id', Auth::user()->id)
            ->first();

        date_default_timezone_set("Asia/Jakarta");
        $numDays = Carbon::create($year, $month)->daysInMonth;

        $calendar = [];

        $calendar[] = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        $lastDayPrevMonth = Carbon::create($year, $month, 1)->subDay()->day;

        $dayOfWeek = Carbon::create($year, $month, 1)->dayOfWeek;

        $dayCounter = 1;

        $firstDayAdded = false;

        for ($i = 0; $i < 6; $i++) {
            $week = [];
            if (!$firstDayAdded) {
                for ($j = $dayOfWeek - 1; $j >= 0; $j--) {
                    $week[] = $lastDayPrevMonth - $j;
                }
                $week[] = $dayCounter;
                $dayCounter++;
                $firstDayAdded = true;
            }
            for ($j = count($week); $j < 7 && $dayCounter <= $numDays; $j++) {
                $date = Carbon::create($year, $month, $dayCounter);
                $holiday = $this->getDayStatus($date);
                $week[] = [
                    'day' => $dayCounter,
                    'status' => $holiday
                ];
                $dayCounter++;
            }
            while (count($week) < 7) {
                $week[] = '';
            }
            $calendar[] = $week;
            if ($dayCounter > $numDays) {
                break;
            }
        }
        $pLocations = Project_location::all();

        $checkUserAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->get();

        if ($checkUserAssignment->isEmpty()) {
            $pLocations = $pLocations->reject(function ($pLocation) {
                return $pLocation->location_code === 'WFH'; // Replace 'WFH' with the specific value you want to remove
            });
        }
        // Company_project::all();
        $userId = Auth::user()->id;

        $leaveApproved = [];
        $checkLeaveApproval = Leave_request::where('req_by', Auth::user()->id)->whereMonth('req_date', $month)->get();
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk->id)->where('status', 29)->pluck('leave_request_id');
            if (!$checkApp->isEmpty()) {
                $leaveApproved[] = $checkApp;
            } else {
            }
        }
        $leaveRequests = Leave_request::where('req_by', Auth::user()->id)->whereIn('id', $leaveApproved)->get();
        foreach ($leaveRequests as $lr) {
            $dates = explode(',', $lr->leave_dates);
            $currentMonth = null;
            $dateGroups = [];
            $group = [];

            foreach ($dates as $date) {
                $formattedDate = date('d', strtotime($date));
                $monthYear = date('F Y', strtotime($date));

                if ($currentMonth !== $monthYear) {
                    if (!empty($group)) {
                        $dateGroups[] = $group;
                        $group = [];
                    }
                    $group['monthYear'] = $monthYear;
                    $group['dates'] = [$formattedDate];
                    $currentMonth = $monthYear;
                } else {
                    $group['dates'][] = $formattedDate;
                }
            }

            if (!empty($group)) {
                $dateGroups[] = $group;
            }

            $lr->dateGroups = $dateGroups;

            $approved = false;
        }
        // Get the current day
        $currentDay = date('d');

        $date = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');

        $findAssignment = Project_assignment_user::where('user_id', '=', $userId)
            ->whereMonth('periode_start', '<=', $month)
            ->whereDate('periode_end', '>=', $date)
            ->get();

        $assignmentArray = [];
        foreach ($findAssignment as $fa) {
            $assignmentArray[] = $fa->project_assignment_id;
        }

        $assignment = Project_assignment_user::join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.*', 'project_assignments.*')
            ->where('project_assignment_users.user_id', '=', $userId)
            ->where('project_assignments.approval_status', 29)
            ->whereIn('project_assignment_users.project_assignment_id', $assignmentArray)
            ->get();

        $validStatusIDs = Approval_status::whereIn('id', [2, 3, 6, 8])->pluck('approval_status_id')->toArray();
        if ($lastUpdate) {
            if (in_array($lastUpdate->ts_status_id, $validStatusIDs)) {
                Session::flash('failed', "You've already submitted your timereport!");
                return redirect()->route('timesheet');
            } else {
                $encryptYear = Crypt::encrypt($year);
                $encryptMonth = Crypt::encrypt($month);
                $previewButton = "/timesheet/entry/preview/" . $encryptYear . "/" . $encryptMonth;
                return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment', 'pLocations', 'leaveRequests'));
            }
        } else {
            $encryptYear = Crypt::encrypt($year);
            $encryptMonth = Crypt::encrypt($month);
            $previewButton = "/timesheet/entry/preview/" . $encryptYear . "/" . $encryptMonth;
            return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment', 'pLocations', 'leaveRequests'));
        }
        // // Return the calendar view with the calendar data
        // return view('timereport.timesheet_entry', compact('calendar', 'year', 'month'));
    }


    // public function save(Request $request)
    // {
    //     foreach ($request->activities as $date => $activities) {
    //         Timesheet::updateOrCreate(
    //             ['ts_user_id' => 'haekals', 'ts_date' => $date],
    //             [
    //                 'ts_from_time' => $activities['from'],
    //                 'ts_to_time' => $activities['to'],
    //                 'ts_activity' => $activities['activity'],
    //                 // Add more activity columns as needed
    //             ]
    //         );
    //     }
    //     Session::flash('success',"Timesheet has been saved!");
    //     return redirect()->back();
    // }

    public function save_entries(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'task' => 'required',
            'clickedDate' => 'required',
            'location' => 'required',
            'from' => 'required',
            'to' => 'required',
            'activity' => 'required',
            'surat_penugasan_wfh' => 'sometimes|mimes:pdf,png,jpeg,jpg|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $totalIncentive = 0;
        $totalIncentive = 0;
        $entry = new Timesheet;
        $ts_task_id = $request->task;
        $id_project = $request->task;
        $task_project = Project_assignment::where('id', $ts_task_id)->get();
        if (Project_assignment::where('id', $ts_task_id)->exists()) {
            foreach ($task_project as $tp) {
                $ts_task_id = $tp->company_project->project_name;
            }
        }
        $entry->ts_user_id = Auth::user()->id;
        $entry->ts_id_date = str_replace('-', '', $request->clickedDate);
        $entry->ts_date = $request->clickedDate;
        $entry->ts_task = $ts_task_id;
        $entry->ts_task_id = $request->task;
        $entry->ts_location = $request->location;
        $entry->ts_from_time = date('H:i', strtotime($request->from));
        $entry->ts_to_time = date('H:i', strtotime($request->to));
        $entry->ts_activity = $request->activity;
        $entry->ts_status_id = '10';

        $user = Auth::user();

        if (Project_assignment_user::where('user_id', $user->id)->where('project_assignment_id', $id_project)->exists()) {
            try {
                $checkRole = Project_assignment_user::where('user_id', $user->id)
                    ->where('project_assignment_id', $id_project)
                    ->value('role');

                if ($checkRole === NULL) {
                    //do nothing
                } elseif ($checkRole === "MT") {
                    $mt_hiredDate = Users_detail::where('user_id', $user->id)
                        ->value('hired_date');

                    // Assuming the hired_date is in the format 'Y-m-d' (e.g., 2022-02-04)
                    $hiredDate = new DateTime($mt_hiredDate);
                    $currentDate = new DateTime(date('Y-m-d'));
                    $intervalDate = $hiredDate->diff($currentDate);
                    $totalMonthsDifference = ($intervalDate->format('%y') * 12) + $intervalDate->format('%m');

                    if ($totalMonthsDifference > 6 && $totalMonthsDifference <= 37) {
                        $roleFare = Additional_fare::where('id', $totalMonthsDifference > 24 ? 3 : ($totalMonthsDifference > 12 ? 2 : 1))
                            ->value('fare');
                        $totalIncentive = $roleFare * 0.7;
                    }
                } else {
                    $roleFare = Project_role::where('role_code', $checkRole)
                        ->value('fare');
                    $totalIncentive = $roleFare * 0.7;
                }
            } catch (Exception $e) {
                //do nothing
            }
        }

        if($ts_task_id == "StandbyLK"){
            $fare = 110000;
        } elseif ($ts_task_id == "StandbyLK") {
            $fare = 200000;
        } else {
            $fare = Project_location::where('location_code', $request->location)->pluck('fare')->first();
        }
        $countAllowances = $fare;

        $entry->allowance = $countAllowances;
        $entry->incentive = $totalIncentive;
        $entry->save();

        try {

            // Store the file if it is provided
            if ($request->hasFile('surat_penugasan_wfh')) {
                $file = $request->file('surat_penugasan_wfh');
                $surat_penugasan_wfh = $request->file('surat_penugasan_wfh');
                $fileExtension = $surat_penugasan_wfh->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $filePath = 'surat_penugasan/' . $fileName;
                $upload_folder = public_path('surat_penugasan/');

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                // Save the file details in the database
                $fileEntry = new Surat_penugasan;
                $fileEntry->user_id = Auth::user()->id;
                $fileEntry->ts_date = $request->clickedDate;
                $fileEntry->file_name = $fileName;
                $fileEntry->file_path = $filePath;
                $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDate);
                $fileEntry->save();
            }
        } catch (Exception $e) {
            //do nothing
        }

        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => date("Yn", strtotime($request->clickedDate))], ['date_submitted' => date('Y-m-d'), 'ts_status_id' => '10', 'ts_task' => '-', 'RequestTo' => '-', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        return response()->json(['success' => 'Entry saved successfully.']);
    }

    public function save_entries_on_holiday(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'task' => 'required',
            'clickedDateRed' => 'required',
            'location' => 'required',
            'from' => 'required',
            'to' => 'required',
            'activity' => 'required',
            'surat_penugasan' => 'sometimes|mimes:pdf,png,jpeg,jpg|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $totalIncentive = 0;
        $totalIncentive = 0;
        $id_project = $request->task;
        $entry = new Timesheet;
        $ts_task_id = $request->task;
        $task_project = Project_assignment::where('id', $ts_task_id)->get();
        while (Project_assignment::where('id', $ts_task_id)->exists()) {
            foreach ($task_project as $tp) {
                $ts_task_id = $tp->company_project->project_name;
            }
        }
        $entry->ts_user_id = Auth::user()->id;
        $entry->ts_id_date = str_replace('-', '', $request->clickedDateRed);
        $entry->ts_date = $request->clickedDateRed;
        $entry->ts_task = $ts_task_id;
        $entry->ts_task_id = $request->task;
        $entry->ts_location = $request->location;
        $entry->ts_from_time = date('H:i', strtotime($request->from));
        $entry->ts_to_time = date('H:i', strtotime($request->to));
        $entry->ts_activity = $request->activity;
        $entry->ts_status_id = '10';

        $user = Auth::user();

        if (Project_assignment_user::where('user_id', $user->id)->where('project_assignment_id', $id_project)->exists()) {
            try {
                $checkRole = Project_assignment_user::where('user_id', $user->id)
                    ->where('project_assignment_id', $id_project)
                    ->value('role');

                if ($checkRole === NULL) {
                    //do nothing
                } elseif ($checkRole === "MT") {
                    $mt_hiredDate = Users_detail::where('user_id', $user->id)
                        ->value('hired_date');

                    // Assuming the hired_date is in the format 'Y-m-d' (e.g., 2022-02-04)
                    $hiredDate = new DateTime($mt_hiredDate);
                    $currentDate = new DateTime(date('Y-m-d'));
                    $intervalDate = $hiredDate->diff($currentDate);
                    $totalMonthsDifference = ($intervalDate->format('%y') * 12) + $intervalDate->format('%m');

                    if ($totalMonthsDifference > 6 && $totalMonthsDifference <= 37) {
                        $roleFare = Additional_fare::where('id', $totalMonthsDifference > 24 ? 3 : ($totalMonthsDifference > 12 ? 2 : 1))
                            ->value('fare');
                        $totalIncentive = $roleFare * 0.7;
                    }
                } else {
                    $roleFare = Project_role::where('role_code', $checkRole)
                        ->value('fare');
                    $totalIncentive = $roleFare * 0.7;
                }
            } catch (Exception $e) {
                //do nothing
            }
        }

        if($ts_task_id == "StandbyLK"){
            $fare = 110000;
        } elseif ($ts_task_id == "StandbyLK") {
            $fare = 200000;
        } else {
            $fare = Project_location::where('location_code', $request->location)->pluck('fare')->first();
        }
        $countAllowances = $fare;

        $entry->allowance = $countAllowances;
        $entry->incentive = $totalIncentive;
        $entry->save();

        try {
            // Store the file if it is provided
            if ($request->hasFile('surat_penugasan')) {
                $file = $request->file('surat_penugasan');
                $surat_penugasan = $request->file('surat_penugasan');
                $fileExtension = $surat_penugasan->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $filePath = 'surat_penugasan/' . $fileName;
                $upload_folder = public_path('surat_penugasan/');

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                // Save the file details in the database
                $fileEntry = new Surat_penugasan;
                $fileEntry->user_id = Auth::user()->id;
                $fileEntry->ts_date = $request->clickedDateRed;
                $fileEntry->file_name = $fileName;
                $fileEntry->file_path = $filePath;
                $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDateRed);
                $fileEntry->save();
            }
        } catch (Exception $e) {
            //do nothing
        }

        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => date("Yn", strtotime($request->clickedDateRed))], ['date_submitted' => date('Y-m-d'), 'ts_status_id' => '10', 'ts_task' => '-', 'RequestTo' => '-', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        return response()->json(['success' => 'Entry saved successfully.']);
    }

    public function save_multiple_entries(Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'task' => 'required',
            'daterange' => 'required',
            'location' => 'required',
            'from' => 'required',
            'to' => 'required',
            'activity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $totalIncentive = 0;
        $totalIncentive = 0;

        $totalIncentive = 0;
        $totalIncentive = 0;

        $dateString = $request->daterange;
        list($startDateString, $endDateString) = explode(' - ', $dateString);
        $startDate = DateTime::createFromFormat('m/d/Y', $startDateString);
        $endDate = DateTime::createFromFormat('m/d/Y', $endDateString);
        $interval = new DateInterval('P1D'); // Interval of 1 day

        $month_periode = $startDate->format('Yn');

        $leaveApproved = [];
        $checkLeaveApproval = Leave_request::where('req_by', Auth::user()->id)->get();
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk->id)->where('status', 29)->pluck('leave_request_id');
            if (!$checkApp->isEmpty()) {
                $leaveApproved[] = $checkApp;
            } else {
            }
        }
        $leave_day = Leave_request::where('req_by', Auth::user()->id)->whereIn('id', $leaveApproved)->pluck('leave_dates')->toArray();

        $formattedDates = [];
        foreach ($leave_day as $dateString) {
            $dateArray = explode(',', $dateString);
            foreach ($dateArray as $dateA) {
                $formattedDates[] = date('Ymd', strtotime($dateA));
            }
        }

        $id_project = $request->task;

        $user = Auth::user();

        if (Project_assignment_user::where('user_id', $user->id)->where('project_assignment_id', $id_project)->exists()) {
            try {
                $checkRole = Project_assignment_user::where('user_id', $user->id)
                    ->where('project_assignment_id', $id_project)
                    ->value('role');

                if ($checkRole === NULL) {
                    //do nothing
                } elseif ($checkRole === "MT") {
                    $mt_hiredDate = Users_detail::where('user_id', $user->id)
                        ->value('hired_date');

                    // Assuming the hired_date is in the format 'Y-m-d' (e.g., 2022-02-04)
                    $hiredDate = new DateTime($mt_hiredDate);
                    $currentDate = new DateTime(date('Y-m-d'));
                    $intervalDate = $hiredDate->diff($currentDate);
                    $totalMonthsDifference = ($intervalDate->format('%y') * 12) + $intervalDate->format('%m');

                    if ($totalMonthsDifference > 6 && $totalMonthsDifference <= 37) {
                        $roleFare = Additional_fare::where('id', $totalMonthsDifference > 24 ? 3 : ($totalMonthsDifference > 12 ? 2 : 1))
                            ->value('fare');
                        $totalIncentive = $roleFare * 0.7;
                    }
                } else {
                    $roleFare = Project_role::where('role_code', $checkRole)
                        ->value('fare');
                    $totalIncentive = $roleFare * 0.7;
                }
            } catch (Exception $e) {
                //do nothing
            }
        }

        try {
            $fare = Project_location::where('location_code', $request->location)->pluck('fare')->first();
            $countAllowances = $fare;
        } catch (Exception $e) {
            //do nothing
        }

        // Loop through each day between start and end dates
        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->format('N');
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $startDate->add($interval);
                continue;
            }
            $dateToCheck = $startDate->format('Ymd');
            if (in_array($dateToCheck, $formattedDates)) {
                $startDate->add($interval);
            } else {
                // Insert the entry to the database for this day
                $entry = new Timesheet;
                $ts_task_id = $request->task;
                try {
                    $task_project = Project_assignment::where('id', $ts_task_id)->get();
                    if (Project_assignment::where('id', $ts_task_id)->exists()) {
                        foreach ($task_project as $tp) {
                            $ts_task_id = $tp->company_project->project_name;
                        }
                    }
                } catch (Exception $e) {
                    //do nothing
                }
                $entry->ts_user_id = Auth::user()->id;
                $entry->ts_id_date = $startDate->format('Ymd');
                $entry->ts_date = $startDate->format('Y-m-d');
                $entry->ts_task = $ts_task_id;
                $entry->ts_task_id = $request->task;
                $entry->ts_location = $request->location;
                $entry->ts_from_time = date('H:i', strtotime($request->from));;
                $entry->ts_to_time = date('H:i', strtotime($request->to));
                $entry->ts_activity = $request->activity;
                $entry->ts_status_id = '10';
                $entry->allowance = $countAllowances;
                $entry->incentive = $totalIncentive;
                $entry->save();

                // Move to the next day
                $startDate->add($interval);
            }
        }
        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => $month_periode], ['date_submitted' => date('Y-m-d'), 'ts_task' => '-', 'RequestTo' => '-', 'ts_status_id' => '10', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        return response()->json(['success' => "Entry saved successfully. $request->daterange"]);
    }

    public function updateActivitiesEntry($id, Request $request)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'update_task' => 'required',
            'update_clickedDate' => 'required',
            'update_location' => 'required',
            'update_from' => 'required',
            'update_to' => 'required',
            'update_activity' => 'required',
        ]);

        $inputFromTimeUpdate = $request->update_from;
        $inputToTimeUpdate = $request->update_to;
        $totalIncentive = 0;
        $totalIncentive = 0;
        $id_project = $request->update_task;

        $entry = Timesheet::find($id);

        $ts_task_id = $request->update_task;
        $task_project = Project_assignment::where('id', $ts_task_id)->get();
        while (Project_assignment::where('id', $ts_task_id)->exists()) {
            foreach ($task_project as $tp) {
                $ts_task_id = $tp->company_project->project_name;
            }
        }
        $entry->ts_task = $ts_task_id;
        $entry->ts_task_id = $request->update_task;
        $entry->ts_location = $request->update_location;
        $entry->ts_from_time = date('H:i', strtotime($inputFromTimeUpdate));
        $entry->ts_to_time = date('H:i', strtotime($inputToTimeUpdate));
        $entry->ts_activity = $request->update_activity;

        $user = Auth::user();

        if (Project_assignment_user::where('user_id', $user->id)->where('project_assignment_id', $id_project)->exists()) {
            try {
                $checkRole = Project_assignment_user::where('user_id', $user->id)
                    ->where('project_assignment_id', $id_project)
                    ->value('role');

                if ($checkRole === NULL) {
                    //do nothing
                } elseif ($checkRole === "MT") {
                    $mt_hiredDate = Users_detail::where('user_id', $user->id)
                        ->value('hired_date');

                    // Assuming the hired_date is in the format 'Y-m-d' (e.g., 2022-02-04)
                    $hiredDate = new DateTime($mt_hiredDate);
                    $currentDate = new DateTime(date('Y-m-d'));
                    $intervalDate = $hiredDate->diff($currentDate);
                    $totalMonthsDifference = ($intervalDate->format('%y') * 12) + $intervalDate->format('%m');

                    if ($totalMonthsDifference > 6 && $totalMonthsDifference <= 37) {
                        $roleFare = Additional_fare::where('id', $totalMonthsDifference > 24 ? 3 : ($totalMonthsDifference > 12 ? 2 : 1))
                            ->value('fare');
                        $totalIncentive = $roleFare * 0.7;
                    }
                } else {
                    $roleFare = Project_role::where('role_code', $checkRole)
                        ->value('fare');
                    $totalIncentive = $roleFare * 0.7;
                }
            } catch (Exception $e) {
                //do nothing
            }
        }

        $fare = Project_location::where('location_code', $request->update_location)->pluck('fare')->first();
        $countAllowances = $fare;

        $entry->allowance = $countAllowances;
        $entry->incentive = $totalIncentive;
        $entry->save();

        return response()->json(['success' => 'Entry updated successfully.']);
    }

    public function getActivities($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

        return response()->json($activities);
    }

    public function destroy($id)
    {
        $activity = Timesheet::find($id);

        if ($activity) {
            $surat_penugasan = Surat_penugasan::where("timesheet_id", $activity->ts_id_date);

            // Delete the file from the public folder if it exists
            if ($surat_penugasan->exists()) {
                $fileEntry = $surat_penugasan->first();
                $filePath = public_path($fileEntry->file_path);

                // Delete the file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Delete the Surat_penugasan entry
                $surat_penugasan->delete();
            }

            // Delete the activity entry
            $activity->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Activity not found']);
        }
    }

    public function destroy_all($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Retrieve the activities within the date range
        $activities = Timesheet::where('ts_user_id', Auth::user()->id)->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();

        foreach ($activities as $activity) {
            $surat_penugasan = Surat_penugasan::where("timesheet_id", $activity->ts_id_date);

            // Delete the file from the public folder if it exists
            if ($surat_penugasan->exists()) {
                $fileEntry = $surat_penugasan->first();
                $filePath = public_path($fileEntry->file_path);

                // Delete the file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Delete the Surat_penugasan entry
                $surat_penugasan->delete();
            }

            // Delete the activity entry
            $activity->delete();
        }

        return response()->json(['success' => true]);
    }

    public function preview($year, $month)
    {
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
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

        $user_info = User::find(Auth::user()->id);

        $workflow = Timesheet_detail::where('user_timesheet', Auth::user()->id)->where('month_periode', $year . $month)->get();

        $userId = Auth::user()->id;

        $checkisSubmitted = DB::table('timesheet_details')
        ->select('*')
        ->whereYear('date_submitted', $year)
        ->where('user_timesheet', Auth::user()->id)
        ->whereNotIn('ts_status_id', [10,404])
        ->where('month_periode', $year.intval($month))
        ->whereNotExists(function ($query) use ($year, $month) {
            $query->select(DB::raw(1))
                ->from('timesheet_details')
                ->where('month_periode', $year.intval($month))
                ->where('ts_status_id', [404]);
        })
        ->groupBy('user_timesheet', 'month_periode')
        ->get();

        $Check = DB::table('timesheet_details')
            ->select('ts_status_id')
            ->where('user_timesheet', Auth::user()->id)
            ->where('month_periode', $year . intval($month))
            ->havingRaw('COUNT(*) = SUM(CASE WHEN ts_status_id = 30 THEN 0 WHEN ts_status_id = 29 THEN 0 ELSE 1 END)')
            ->groupBy('user_timesheet', 'month_periode')
            ->pluck('ts_status_id')
            ->toArray();
        if (!$checkisSubmitted->isEmpty()) {
            $removeBtnSubmit = 1;
            if (empty($Check)) {
                $removeBtnSubmit = 29;
            }
        } else {
            $removeBtnSubmit = 0;
        }

        // Get the current day
        $currentDay = date('d');

        // Create the date string in the format Y-m-d
        $date = $year . '-' . $month . '-' . $currentDay;

        $assignment = Project_assignment_user::join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.*', 'project_assignments.*')
            ->where('project_assignment_users.user_id', '=', $userId)
            ->where('project_assignments.approval_status', 29)
            ->where(function ($query) use ($date) {
                $query->whereDate('project_assignment_users.periode_start', '<=', $date)
                    ->whereDate('project_assignment_users.periode_end', '>=', $date);
            })
            ->get();

        $leaveApproved = [];
        $checkLeaveApproval = Leave_request::where('req_by', Auth::user()->id)->pluck('id');
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk)->where('status', 29)->pluck('leave_request_id')->first();
            if (!empty($checkApp)) {
                $leaveApproved[] = $checkApp;
            }
        }

        $leave_day = Leave_request::where('req_by', Auth::user()->id)->whereIn('id', $leaveApproved)->pluck('leave_dates')->toArray();

        $formattedDates = [];
        foreach ($leave_day as $dateString) {
            $dateArray = explode(',', $dateString);
            foreach ($dateArray as $dateA) {
                $formattedDates[] = date('Y-m-d', strtotime($dateA));
            }
        }

        $surat_penugasan = Surat_penugasan::where('user_id', Auth::user()->id)->pluck('ts_date')->toArray();
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
            ->where('ts_user_id', Auth::user()->id)
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
        // return response()->json($activities);
        return view('timereport.preview', compact('year', 'month', 'removeBtnSubmit', 'totalHours', 'info', 'assignmentNames', 'srtDate', 'startDate', 'endDate', 'formattedDates'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }

    public function submit_timesheet($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        $checkisSubmitted = DB::table('timesheet_details')
            ->select('*')
            ->whereYear('date_submitted', $year)
            ->where('user_timesheet', Auth::user()->id)
            ->whereNotIn('ts_status_id', [10, 404])
            ->where('month_periode', $year . intval($month))
            ->whereNotExists(function ($query) use ($year, $month) {
                $query->select(DB::raw(1))
                    ->from('timesheet_details')
                    ->where('month_periode', $year . intval($month))
                    ->where('ts_status_id', [404]);
            })
            ->groupBy('user_timesheet', 'month_periode')
            ->get();

        if (!$checkisSubmitted->isEmpty()) {
            Session::flash('failed', 'Your Timesheet has already been submitted!');
            return redirect()->back();
        }

        // Get the current date
        $currentDate = Carbon::now();

        $dateCut = Cutoffdate::first();
        // Get the cutoff date for submitting timesheets (7th of the next month)
        $cutoffDate = Carbon::create($year, $month)->addMonths(1)->startOfMonth()->addDays(($dateCut->date - 1));
        $cutoffDate = Carbon::createFromFormat('Y-m-d', "2023-07-$dateCut->date");

        // Check if the current date is on or after the cutoff date
        if ($currentDate->gte($cutoffDate)) {
            Session::flash('failed', 'Timesheet submission is closed for this month.');
            return redirect()->back();
        }

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $tsOfTheMonth = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_id_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

        if ($tsOfTheMonth->isEmpty()) {
            Session::flash('failed', "You have to fill your timesheet first!");
            return redirect(url()->previous());
        }
        $total_work_hours = 0;
        foreach ($tsOfTheMonth as $sum) {
            $start_time = strtotime($sum->ts_from_time);
            $end_time = strtotime($sum->ts_to_time);
            $time_diff_seconds = $end_time - $start_time;
            $time_diff_hours = gmdate('H', $time_diff_seconds);
            $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
            $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
            echo $time_diff_hours . ':' . $time_diff_minutes;
        }
        $userId = Auth::user()->id;

        $subquery = DB::table('timesheet')
            ->select('ts_task', 'ts_location', 'ts_user_id', DB::raw('CAST(allowance AS DECIMAL(10, 2)) AS allowance'), 'ts_task_id', 'ts_id_date', 'incentive')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY ts_id_date ORDER BY CAST(allowance AS DECIMAL(10, 2)) DESC) AS rn')
            ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('ts_user_id', $userId)
            ->groupBy('ts_id_date', 'ts_task', 'ts_location', 'ts_user_id', 'allowance', 'ts_task_id');

        $sql = '(' . $subquery->toSql() . ') as subquery';

        $results = DB::table(DB::raw($sql))
            ->mergeBindings($subquery)
            ->select('ts_task', 'ts_location', 'ts_user_id', 'allowance as max_allowance', 'ts_task_id', 'incentive', DB::raw('COUNT(*) as total_rows'))
            ->where('rn', 1)
            ->groupBy('ts_task', 'ts_location', 'ts_user_id', 'allowance', 'ts_task_id')
            ->get();

        // echo "Total days: " . $totalDays;
        $checkUserDept = Auth::user()->users_detail->department->id;

        $approvalGA = Timesheet_approver::whereIn('id', [10, 45])->get();
        $approvalFinance = Timesheet_approver::whereIn('id', [15, 45])->get();
        $approvalSales = Timesheet_approver::whereIn('id', [50, 55])->get();
        $approvalHCM = Timesheet_approver::whereIn('id', [10, 60])->get();
        $approvalService = Timesheet_approver::whereIn('id', [20, 40])->get();

        $checkUserRole = Usr_role::where('user_id', Auth::user()->id)->pluck('role_name')->toArray();
        $serviceDirOnly = ["pc"];
        $gaDirOnly = ["hr"]; //1

        Timesheet_detail::updateOrCreate([
            'user_id' => Auth::user()->id,
            'workhours' => intval($total_work_hours),
            'activity' => 'Submitted',
            'month_periode' => $year . $month,
        ], [
            'ts_status_id' => 15,
            'date_submitted' => date('Y-m-d'),
            'note' => '',
            'ts_task' => '-',
            'RequestTo' => '-',
            'user_timesheet' => Auth::user()->id
        ]);
        $empApproval = [];
        $totalIncentive = 0;
        // var_dump($countRows);
        foreach ($results as $row) {
            $test = Project_assignment::where('id', $row->ts_task_id)->pluck('company_project_id')->first();
            $test2 = Project_assignment_user::where('role', "PM")->where('company_project_id', $test)->pluck('user_id')->first();
            $pa = Project_assignment_user::where('role', "PA")->where('company_project_id', $test)->pluck('user_id')->first();
            $checkRole = Project_assignment_user::where('user_id', Auth::user()->id)->where('project_assignment_id', $row->ts_task_id)->pluck('role')->first();

            $countIncentive = $row->total_rows * $row->incentive;

            $countAllowance = $row->total_rows * $row->max_allowance;

            $totalIncentive = $countIncentive;
            $totalAllowances = $countAllowance;
            switch ($row->ts_task) {
                case "HO":
                case "Sick":
                case "Standby":
                case "Other":
                case "Absent":
                    switch ($checkUserDept) {
                        case 4:
                            if (in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray())) {
                                foreach ($approvalFinance as $approverFinance) {
                                    $newArrayFm = [
                                        'name' => $approverFinance->approver,
                                        'task' => $row->ts_task,
                                        'location' => $row->ts_location,
                                        'mandays' => $row->total_rows,
                                        'role' => $checkRole,
                                        'task_id' => $row->ts_task_id,
                                        'total_incentive' => $totalIncentive,
                                        'total_allowance' => $totalAllowances,
                                    ];
                                    $empApproval[] = $newArrayFm;
                                }
                            } else {
                                if (!empty(array_intersect($gaDirOnly, $checkUserRole))) {
                                    $newArrayS = [
                                        'name' => Timesheet_approver::where('id', 45)->pluck('approver')->first(),
                                        'task' => $row->ts_task,
                                        'location' => $row->ts_location,
                                        'mandays' => $row->total_rows,
                                        'role' => $checkRole,
                                        'task_id' => $row->ts_task_id,
                                        'total_incentive' => $totalIncentive,
                                        'total_allowance' => $totalAllowances,
                                    ];
                                    $empApproval[] = $newArrayS;
                                } else {
                                    foreach ($approvalGA as $approverGa) {
                                        $newArrayHO = [
                                            'name' => $approverGa->approver,
                                            'task' => $row->ts_task,
                                            'location' => $row->ts_location,
                                            'mandays' => $row->total_rows,
                                            'role' => $checkRole,
                                            'task_id' => $row->ts_task_id,
                                            'total_incentive' => $totalIncentive,
                                            'total_allowance' => $totalAllowances,
                                        ];
                                        $empApproval[] = $newArrayHO;
                                    }
                                }
                            }
                            break;
                        case 2:
                            foreach ($approvalService as $approverService) {
                                $newArrayService = [
                                    'name' => $approverService->approver,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayService;
                            }
                            break;
                        case 3:
                            foreach ($approvalGA as $approverGa) {
                                $newArrayHO = [
                                    'name' => $approverGa->approver,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayHO;
                            }
                            break;
                        case 1:
                            foreach ($approvalSales as $approverSales) {
                                $newArrayHO = [
                                    'name' => $approverSales->approver,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayHO;
                            }
                            break;
                    }
                    break;
                case "Training":
                case "Absent":
                case "Absent":
                    foreach ($approvalHCM as $approverHCM) {
                        $newArrayHO = [
                            'name' => $approverHCM->approver,
                            'task' => $row->ts_task,
                            'location' => $row->ts_location,
                            'mandays' => $row->total_rows,
                            'role' => $checkRole,
                            'task_id' => $row->ts_task_id,
                            'total_incentive' => $totalIncentive,
                            'total_allowance' => $totalAllowances,
                        ];
                        $empApproval[] = $newArrayHO;
                    }
                    break;
                case "Trainer":
                case "Presales":
                    foreach ($approvalSales as $approverSales) {
                        $newArrayPresales = [
                            'name' => $approverSales->approver,
                            'task' => $row->ts_task,
                            'location' => $row->ts_location,
                            'mandays' => $row->total_rows,
                            'role' => $checkRole,
                            'task_id' => $row->ts_task_id,
                            'total_incentive' => $totalIncentive,
                            'total_allowance' => $totalAllowances,
                        ];
                        $empApproval[] = $newArrayPresales;
                    }
                    break;
                default:
                    $tsExceptProject = Project_assignment::where('id', $row->ts_task_id)->get();
                    if ($tsExceptProject->isEmpty()) {
                        foreach ($approvalGA as $approverGa) {
                            $newArrayHO = [
                                'name' => $approverGa->approver,
                                'task' => $row->ts_task,
                                'location' => $row->ts_location,
                                'mandays' => $row->total_rows,
                                'role' => $checkRole,
                                'task_id' => $row->ts_task_id,
                                'total_incentive' => $totalIncentive,
                                'total_allowance' => $totalAllowances,
                            ];
                            $empApproval[] = $newArrayHO;
                        }
                    } else {
                        switch (true) {
                            case (array_intersect($serviceDirOnly, $checkUserRole)):
                                $newArrayS = [
                                    'name' => Timesheet_approver::where('id', 40)->pluck('approver')->first(),
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayS;
                            break;
                            default:
                            if(!$test2 == NULL){
                                $newArrayPM = [
                                    'name' => $test2,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayPM;
                            }
                            if(!$pa == NULL){
                                $newArrayPA = [
                                    'name' => $pa,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayPA;
                            }
                            foreach($approvalService as $approverService){
                                $newArrayS = [
                                    'name' => $approverService->approver,
                                    'task' => $row->ts_task,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                    'total_incentive' => $totalIncentive,
                                    'total_allowance' => $totalAllowances,
                                ];
                                $empApproval[] = $newArrayS;
                            }
                            break;
                        }
                    }
                    break;
            }
        }

        foreach ($empApproval as $test) {
            Timesheet_detail::updateOrCreate([
                'user_id' => Auth::user()->id,
                'workhours' => intval($total_work_hours),
                'month_periode' => $year.$month,
                'RequestTo' => $test['name'],
                'ts_task' => $test['task'],
                'ts_location' => $test['location']
            ], [
                'ts_mandays' => $test['mandays'],
                'activity' => 'Waiting for Approval',
                'roleAs' => $test['role'],
                'date_submitted' => date('Y-m-d'),
                'ts_status_id' => 20,
                'total_incentive' => $test['total_incentive'],
                'total_allowance' => $test['total_allowance'],
                'note' => '',
                'ts_task_id' => $test['task_id'],
                'user_timesheet' => Auth::user()->id
            ]);
        }
        Timesheet_detail::where('RequestTo', Auth::user()->id)->where('month_periode', $year . $month)->where('user_timesheet', Auth::user()->id)->delete();
        // Update Timesheet records between the start and end dates
        Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('created_at', 'desc')->update(['ts_status_id' => '15']);

        $ts_date_desc = date("F", mktime(0, 0, 0, $month, 1)) . ' ' . $year;
        // return response()->json($activities);
        Session::flash('success', "Your Timereport $ts_date_desc has been submitted!");
        return redirect()->back();
    }

    public function cancel_submit_timesheet($year, $month)
    {
        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        Timesheet_detail::whereNotIn('ts_status_id', [10])->where('month_periode', $year . intval($month))->where('user_timesheet', Auth::user()->id)->delete();
        Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('created_at', 'desc')->update(['ts_status_id' => '10']);
        return redirect()->back()->with('success', 'Timesheet submission has been canceled!');
    }

    public function print($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $user_info = User::find(Auth::user()->id);

        $user_info_details = Users_detail::where('user_id', Auth::user()->id)->first();
        $user_info_emp_id = $user_info_details->employee_id;
        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('ts_date', 'asc')->get();

        $pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'), ['timesheet' => $activities,  'user_info' => $user_info,]);
        return $pdf->download('timesheet - ' . $year . $month . '.pdf');
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
        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', $user_timesheet)->orderBy('ts_date', 'asc')->get();

        $pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'), ['timesheet' => $activities,  'user_info' => $user_info,]);
        return $pdf->download('timesheet #' . $user_timesheet . '-' . $year . $month . '.pdf');
    }

    public function getActivitiesEntry($year, $month, $id)
    {
        // Use the $year, $month, and $id parameters to fetch data from your database or other data source
        $data = Timesheet::find($id);

        // Return the data as a JSON response
        return response()->json($data);
    }

    public function summary(Request $request)
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
        $approvals = Timesheet_detail::groupBy('user_timesheet', 'ts_task', 'RequestTo')->orderBy('user_timesheet', 'asc')->orderBy('created_at', 'asc');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Year . intval($Month));
        } else {
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Year . intval($Month));
        }

        $approvals = $approvals->get();
        // dd($approvals);
        return view('timereport.summary', compact('approvals', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function remind($id, $year, $month)
    {
        $employees = User::where('id', $id)->get();

        foreach ($employees as $employee) {
            dispatch(new SendTimesheetReminderSummary($employee, $year, $month));
        }
        return redirect()->back()->with('success', "An email has been sent!");
    }

    public function download_surat($timesheet_id)
    {
        $getFile = Surat_penugasan::where('timesheet_id', $timesheet_id)->first();
        $filePath = public_path($getFile->file_path);

        // Check if the file exists
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        // File not found
        abort(404);
    }
}
