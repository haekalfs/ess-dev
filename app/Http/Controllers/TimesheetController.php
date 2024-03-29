<?php

namespace App\Http\Controllers;

use App\Jobs\SendTimesheetReminderSummary;
use App\Mail\EssMailer;
use App\Mail\TimesheetReminderEmployee;
use App\Models\Additional_fare;
use App\Models\Approval_status;
use App\Models\Checkinout;
use App\Models\Company_project;
use App\Models\Cutoffdate;
use App\Models\Emp_leave_quota;
use App\Models\Holidays;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Notification_alert;
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
use App\Models\Vendor_list;
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
            $getNotification = Notification_alert::where('type', "2A")->where('user_id', Auth::id())->whereNull('read_stat')->where('month_periode', $currentYear.$entry)->first();
            if($getNotification){
                $notify = $getNotification->id;
            } else {
                $notify = false;
            }
            $month = date("F", mktime(0, 0, 0, $entry, 1));
            $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $entry)
                ->whereYear('ts_date', $currentYear)
                ->orderBy('updated_at', 'desc')
                // ->whereNull('ts_type')
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
            $entries[] = compact('month', 'notify', 'lastUpdatedAt', 'status', 'editUrl', 'previewUrl', 'isSubmitted');
        }
        return view('timereport.timesheet', compact('entries', 'yearsBefore', 'yearSelected'));
    }

    public function getDayStatus($date)
    {
        $json = null;
        $array = null;
        $cachedData = Cache::get('holiday_data');
        $maxAttempts = 5;
        $attempts = 0;

        // Check if the year of the given date is the current year
        $dateTime = new DateTime($date);
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
                Cache::put('holiday_data', $array, 60 * 24); // Cache the data for 24 hours
                $cachedData = $array;
            } catch (Exception $e) {
                // Handle exception or log error
                sleep(1); // Wait for 5 seconds before retrying
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

        $usersProject = Project_assignment_user::where('user_id', Auth::id())->pluck('company_project_id')->toArray();
        $usersRoles = Usr_role::where('user_id', Auth::id())->pluck('role_id')->toArray();

        $holidays = Holidays::where(function($query) use ($usersProject) {
                $query->where('isProject', 1)
                    ->where('isHoliday', 1)
                    ->whereIn('intended_for', $usersProject);
            })
            ->orWhere(function($query) use ($usersRoles) {
                $query->where('isProject', 0)
                    ->where('isHoliday', 1)
                    ->whereIn('intended_for', $usersRoles);
            })
            ->where('status', 1)
            ->pluck('ts_date')
            ->toArray();

        $holidayInPerdana = [];
        foreach ($holidays as $holidayString) {
            $holidayInPerdana[] = date('Ymd', strtotime($holidayString));
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

        //WeekendReplacement
        $weekendReplacement = Surat_penugasan::where('user_id', Auth::user()->id)
            ->where('isTaken', TRUE)
            ->pluck('date_to_replace')
            ->toArray();

        $formattedDatesWeekendRepl = [];
        foreach ($weekendReplacement as $dateString) {
            $formattedDatesWeekendRepl[] = date('Ymd', strtotime($dateString));
        }

        // Get the hired_date
        $hired_date = Users_detail::where('user_id', Auth::user()->id)->pluck('hired_date')->first();
        $hired_date = new DateTime($hired_date);
        // Deducting one day from the date
        $hired_date->modify('-1 day');

        $yearH = $hired_date->format('Y');

        $formattedDatesHired = [];
        $currentDateH = clone $hired_date;

        while ($currentDateH->format('m') >= "1" && $currentDateH->format('Y') == $yearH) {
            $formattedDatesHired[] = $currentDateH->format('Ymd');
            $currentDateH->modify('-1 day');
        }

        // Check tanggal merah berdasarkan libur nasional
        $dateToCheck = $date->format('Y-m-d');
        // if (isset($array[$dateToCheck]) && $array[$dateToCheck]['holiday'] === true) {
        //     $description = $array[$dateToCheck]['summary'][0];
        //     // Use $description as needed.
        //     return "red";
        // }
        if (isset($array[$dateToCheck]) && $array[$dateToCheck]['holiday'] === true) {
            return "red";
        }
        // Check tanggal merah berdasarkan hari minggu
        elseif ($date->format('D') === "Sun" || $date->format('D') === "Sat") {
            return "red";
        }
        // Bukan tanggal merah
        else {
            $dateToCheck2 = $date->format('Ymd');
            if (in_array($dateToCheck2, $formattedDates)) {
                return 2907;
            } elseif(in_array($dateToCheck2, $formattedDatesHired)){
                return 404;
            }elseif(in_array($dateToCheck2, $formattedDatesWeekendRepl)){
                return 100;
            }elseif(in_array($dateToCheck2, $holidayInPerdana)){
                return "red";
            } else {
                return "";
            }
        }
    }

    public function getDaySummary($date)
    {
        $json = null;
        $array = null;
        $cachedData = Cache::get('holiday_data');
        $maxAttempts = 5;
        $attempts = 0;

        // Check if the year of the given date is the current year
        $dateTime = new DateTime($date);
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
                Cache::put('holiday_data', $array, 60 * 24); // Cache the data for 24 hours
                $cachedData = $array;
            } catch (Exception $e) {
                // Handle exception or log error
                sleep(1); // Wait for 5 seconds before retrying
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

        $usersProject = Project_assignment_user::where('user_id', Auth::id())->pluck('company_project_id')->toArray();
        $usersRoles = Usr_role::where('user_id', Auth::id())->pluck('role_id')->toArray();

        $holidays = Holidays::where(function($query) use ($usersProject) {
                $query->where('isProject', 1)
                    ->where('isHoliday', 1)
                    ->whereIn('intended_for', $usersProject);
            })
            ->orWhere(function($query) use ($usersRoles) {
                $query->where('isProject', 0)
                    ->where('isHoliday', 1)
                    ->whereIn('intended_for', $usersRoles);
            })
            ->where('status', 1)
            ->pluck('ts_date')
            ->toArray();

        $holidayInPerdana = [];
        foreach ($holidays as $holidayString) {
            $holidayInPerdana[] = date('Ymd', strtotime($holidayString));
        }

        //WeekendReplacement
        $weekendReplacement = Surat_penugasan::where('user_id', Auth::user()->id)
            ->where('isTaken', TRUE)
            ->pluck('date_to_replace')
            ->toArray();

        // Check tanggal merah berdasarkan libur nasional
        $dateToCheck = $date->format('Y-m-d');

        if (isset($array[$dateToCheck]) && $array[$dateToCheck]['holiday'] === true) {
            $summary = $array[$dateToCheck]['summary'][0];
            return $summary;
        } else {
            $dateToCheck2 = $date->format('Ymd');
            if(in_array($dateToCheck2, $holidayInPerdana)){
                return "Holiday in Perdana";
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
            ->whereNull('ts_type')
            ->orderBy('updated_at', 'desc')
            ->where('ts_user_id', Auth::user()->id)
            ->first();

        date_default_timezone_set("Asia/Jakarta");
        //get Hired date
        $hired_date = Users_detail::where('user_id', Auth::user()->id)->pluck('hired_date')->first();
        $hired_date = new DateTime($hired_date);

        // Get the year of the hired date
        $hiredYear = $hired_date->format('Y');

        // Check if the passed year is before the year of the hired date
        if ($year < $hiredYear) {
            // Redirect the user back or show an error message
            return redirect()->back()->with('failed', 'You cannot access timesheet entry for years before your hiring year.');
        }

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
                $date = Carbon::create($year, $month, $dayCounter);
                $holiday = $this->getDayStatus($date);
                $summary = $this->getDaySummary($date);
                $week[] = [
                    'day' => $dayCounter,
                    'status' => $holiday,
                    'summary' => $summary
                ];
                $dayCounter++;
                $firstDayAdded = true;
            }
            for ($j = count($week); $j < 7 && $dayCounter <= $numDays; $j++) {
                $date = Carbon::create($year, $month, $dayCounter);
                $holiday = $this->getDayStatus($date);
                $summary = $this->getDaySummary($date);
                $week[] = [
                    'day' => $dayCounter,
                    'status' => $holiday,
                    'summary' => $summary
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
        $checkLeaveApproval = Leave_request::where('req_by', Auth::user()->id)->whereMonth('created_at', intval($month))->get();
        foreach ($checkLeaveApproval as $chk) {
            $checkApp = Leave_request_approval::where('leave_request_id', $chk->id)->where('status', 29)->pluck('leave_request_id');
            if (!$checkApp->isEmpty()) {
                $leaveApproved[] = $checkApp;
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

        // $date = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $findAssignment = Project_assignment_user::where('user_id', '=', $userId)
        ->where(function ($query) use ($startDate, $endDate) {
            $query->where('periode_start', '<=', $endDate->endOfMonth()->format('Y-m-d'))
                ->where('periode_end', '>=', $startDate->startOfMonth()->format('Y-m-d'));
        })
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

        $filesUploaded = Surat_penugasan::where('user_id', Auth::id())
        ->whereYear('created_at', $year)
        ->orderBy('created_at', 'desc')
        ->take(5) // Limit the query to retrieve only 5 records
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
                Session::flash('timesheet-cutoffdate', "Timesheet Submission is only available until the end of 5th ".date("F", mktime(0, 0, 0, $month + 1, 1))."!");
                return view('timereport.timesheet_entry', compact('calendar', 'filesUploaded', 'year', 'month', 'previewButton', 'assignment', 'pLocations', 'leaveRequests'));
            }
        } else {
            $encryptYear = Crypt::encrypt($year);
            $encryptMonth = Crypt::encrypt($month);
            $previewButton = "/timesheet/entry/preview/" . $encryptYear . "/" . $encryptMonth;
            Session::flash('timesheet-cutoffdate', "Timesheet Submission is only available until the end of 5th ".date("F", mktime(0, 0, 0, $month + 1, 1))."!");
            return view('timereport.timesheet_entry', compact('calendar', 'filesUploaded', 'year', 'month', 'previewButton', 'assignment', 'pLocations', 'leaveRequests'));
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
            'surat_penugasan_wfh' => 'sometimes|mimes:pdf,png,jpeg,jpg|max:5000',
            'selectedFileUploadedWfh' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $findTaskOnSameDay = Timesheet::where('ts_date', $request->clickedDate)->where('ts_user_id', Auth::user()->id)->get();

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
        if ($ts_task_id == "Other" || $ts_task_id == "Sick") {
            $tsLoc = "-";
        } else {
            $tsLoc = $request->location;
        }
        $entry->ts_location = $tsLoc;
        $entry->ts_from_time = $request->from;
        $entry->ts_to_time = $request->to;
        // $entry->ts_from_time = date('H:i', strtotime($request->from));
        // $entry->ts_to_time = date('H:i', strtotime($request->to));
        $entry->ts_activity = $request->activity;
        $entry->ts_status_id = 10;

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
        } elseif ($ts_task_id == "StandbyLN") {
            $fare = 200000;
        } else {
            $fare = Project_location::where('location_code', $request->location)->pluck('fare')->first();
        }
        $countAllowances = $fare;

        $entry->allowance = $countAllowances;
        $entry->incentive = $totalIncentive;

        if (!$findTaskOnSameDay->isEmpty()) {
            $newTaskStartTime = date('H:i', strtotime($request->from));
            $newTaskEndTime = date('H:i', strtotime($request->to));

            foreach ($findTaskOnSameDay as $existingTask) {
                $existingTaskStartTime = $existingTask->ts_from_time;
                $existingTaskEndTime = $existingTask->ts_to_time;

                if (($newTaskStartTime >= $existingTaskEndTime)) {
                    $entry->ts_from_time = date('H:i', strtotime($request->from));
                    $entry->ts_to_time = date('H:i', strtotime($request->to));
                    $entry->save();
                } else {
                    // Tasks intersect, return an error response or handle accordingly
                    return response()->json(['error' => 'Task intersects with existing tasks'], 400);
                }
            }
        } else {
            $entry->save();
        }

        try {
            // Store the file if it is provided
            if ($request->hasFile('surat_penugasan_wfh')) {
                $file = $request->file('surat_penugasan_wfh');
                $surat_penugasan_wfh = $request->file('surat_penugasan_wfh');
                $fileExtension = $surat_penugasan_wfh->getClientOriginalExtension();
                $orgName = $surat_penugasan_wfh->getClientOriginalName();
                $fileName = uniqid() . '_' . $orgName;
                $filePath = 'surat_penugasan/' . $fileName;
                $upload_folder = public_path('surat_penugasan/');

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                $dateString = $request->clickedDate;
                $getDayStat = null;
                $originalDate = Carbon::createFromFormat('Y-m-d', $dateString);
                $threeMonthsLater = $originalDate->addMonths(3)->format('Y-m-d');

                // Check if conversion was successful
                if ($originalDate !== false) {
                    $getDayStat = $this->getDayStatus($originalDate);
                }

                // Save the file details in the database
                $fileEntry = new Surat_penugasan;
                $fileEntry->user_id = Auth::user()->id;
                $fileEntry->ts_date = $request->clickedDate;
                if($getDayStat == "red"){
                    $fileEntry->isWeekend = TRUE;
                }
                $fileEntry->expiration = $threeMonthsLater;
                $fileEntry->file_name = $fileName;
                $fileEntry->file_path = $filePath;
                $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDate);
                $fileEntry->save();
            } else {
                if($request->selectedFileUploadedWfh){
                    $checkFile = Surat_penugasan::where('file_name', $request->selectedFileUploadedWfh)->first();

                    // Assuming $fileEntry is your existing file entry
                    $originalFilePath = public_path($checkFile->file_path);
                    $newFileName = pathinfo($originalFilePath, PATHINFO_FILENAME); // Extract original file name without extension
                    $fileExtension = pathinfo($originalFilePath, PATHINFO_EXTENSION); // Extract file extension

                    $newFilePath = $originalFilePath; // Initial new file path, same as the original file

                    // Check if the file already exists with the new name, if it does, increment the name
                    $i = 1;
                    while (file_exists($newFilePath)) {
                        $newFileNameWithNumber = $newFileName . '_' . $i; // Append a number to the file name
                        $newFilePath = public_path('surat_penugasan/') . $newFileNameWithNumber . '.' . $fileExtension; // Create the new file path
                        $i++;
                    }

                    // Now, duplicate the file to the new path
                    if (copy($originalFilePath, $newFilePath)) {
                        // File has been duplicated successfully
                        // You can use $newFilePath as the path to the duplicated file
                        $fileEntry = new Surat_penugasan;
                        $fileEntry->user_id = Auth::user()->id;
                        $fileEntry->ts_date = $request->clickedDate;
                        $fileEntry->file_name = $newFileNameWithNumber . '.' . $fileExtension;
                        $fileEntry->file_path = 'surat_penugasan/' . $newFileNameWithNumber . '.' . $fileExtension;
                        $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDate);
                        $fileEntry->save();
                    }
                }
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
            'surat_penugasan' => 'sometimes|mimes:pdf,png,jpeg,jpg|max:5000',
            'selectedFileUploaded' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $findTaskOnSameDay = Timesheet::where('ts_date', $request->clickedDateRed)->where('ts_user_id', Auth::user()->id)->get();

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
        if ($ts_task_id == "Other" || $ts_task_id == "Sick") {
            $tsLoc = "-";
        } else {
            $tsLoc = $request->location;
        }
        $entry->ts_location = $tsLoc;
        $entry->ts_from_time = $request->from;
        $entry->ts_to_time = $request->to;
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
        } elseif ($ts_task_id == "StandbyLN") {
            $fare = 200000;
        } else {
            $fare = Project_location::where('location_code', $request->location)->pluck('fare')->first();
        }
        $countAllowances = $fare;

        $entry->allowance = $countAllowances;
        $entry->incentive = $totalIncentive;

        if (!$findTaskOnSameDay->isEmpty()) {
            $newTaskStartTime = date('H:i', strtotime($request->from));
            $newTaskEndTime = date('H:i', strtotime($request->to));

            foreach ($findTaskOnSameDay as $existingTask) {
                $existingTaskStartTime = $existingTask->ts_from_time;
                $existingTaskEndTime = $existingTask->ts_to_time;

                if (($newTaskStartTime >= $existingTaskEndTime)) {
                    $entry->ts_from_time = date('H:i', strtotime($request->from));
                    $entry->ts_to_time = date('H:i', strtotime($request->to));
                    $entry->save();
                } else {
                    // Tasks intersect, return an error response or handle accordingly
                    return response()->json(['error' => 'Task intersects with existing tasks'], 400);
                }
            }
        } else {
            $entry->save();
        }

        try {
            // Store the file if it is provided
            if ($request->hasFile('surat_penugasan')) {
                $file = $request->file('surat_penugasan');
                $surat_penugasan = $request->file('surat_penugasan');
                $fileExtension = $surat_penugasan->getClientOriginalExtension();
                $orgName = $surat_penugasan->getClientOriginalName();
                $fileName = uniqid() . '_' . $orgName;
                $filePath = 'surat_penugasan/' . $fileName;
                $upload_folder = public_path('surat_penugasan/');

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                $dateString = $request->clickedDateRed;
                $getDayStat = null;
                $originalDate = Carbon::createFromFormat('Y-m-d', $dateString);
                $threeMonthsLater = $originalDate->addMonths(3)->format('Y-m-d');

                // Check if conversion was successful
                if ($originalDate !== false) {
                    $getDayStat = $this->getDayStatus($originalDate);
                }

                // Save the file details in the database
                $fileEntry = new Surat_penugasan;
                $fileEntry->user_id = Auth::user()->id;
                $fileEntry->ts_date = $request->clickedDateRed;
                if($getDayStat == "red"){
                    $fileEntry->isWeekend = TRUE;
                }
                $fileEntry->expiration = $threeMonthsLater;
                $fileEntry->file_name = $fileName;
                $fileEntry->file_path = $filePath;
                $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDateRed);
                $fileEntry->save();
            } else {
                if($request->selectedFileUploaded){
                    $checkFile = Surat_penugasan::where('file_name', $request->selectedFileUploaded)->first();

                    // Assuming $fileEntry is your existing file entry
                    $originalFilePath = public_path($checkFile->file_path);
                    $newFileName = pathinfo($originalFilePath, PATHINFO_FILENAME); // Extract original file name without extension
                    $fileExtension = pathinfo($originalFilePath, PATHINFO_EXTENSION); // Extract file extension

                    $newFilePath = $originalFilePath; // Initial new file path, same as the original file

                    // Check if the file already exists with the new name, if it does, increment the name
                    $i = 1;
                    while (file_exists($newFilePath)) {
                        $newFileNameWithNumber = $newFileName . '_' . $i; // Append a number to the file name
                        $newFilePath = public_path('surat_penugasan/') . $newFileNameWithNumber . '.' . $fileExtension; // Create the new file path
                        $i++;
                    }

                    // Now, duplicate the file to the new path
                    if (copy($originalFilePath, $newFilePath)) {
                        // File has been duplicated successfully
                        // You can use $newFilePath as the path to the duplicated file
                        $fileEntry = new Surat_penugasan;
                        $fileEntry->user_id = Auth::user()->id;
                        $fileEntry->ts_date = $request->clickedDateRed;
                        $fileEntry->file_name = $newFileNameWithNumber . '.' . $fileExtension;
                        $fileEntry->file_path = 'surat_penugasan/' . $newFileNameWithNumber . '.' . $fileExtension;
                        $fileEntry->timesheet_id = str_replace('-', '', $request->clickedDateRed);
                        $fileEntry->save();
                    }
                }
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
                if ($ts_task_id == "Other" || $ts_task_id == "Sick") {
                    $tsLoc = "-";
                } else {
                    $tsLoc = $request->location;
                }
                $entry->ts_location = $tsLoc;
                $entry->ts_from_time = $request->from;
                $entry->ts_to_time = $request->to;
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
        if ($ts_task_id == "Other" || $ts_task_id == "Sick") {
            $tsLoc = "-";
        } else {
            $tsLoc = $request->update_location;
        }
        $entry->ts_location = $tsLoc;
        $entry->ts_from_time = $inputFromTimeUpdate;
        $entry->ts_to_time = $inputToTimeUpdate;
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
        $entry->ts_type = NULL;
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
        $activities = Timesheet::where('ts_user_id', Auth::user()->id)->whereNull('ts_type')->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->get();

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

        $workflow = Timesheet_detail::where('user_timesheet', Auth::user()->id)->where('month_periode', $year . intval($month))->groupBy('ts_task', 'ts_location', 'RequestTo', 'activity')->orderBy('created_at', 'asc')->orderBy('ts_task', 'asc')->orderBy('priority', 'desc')->orderBy('ts_task', 'asc')->get();

        $userId = Auth::user()->id;

        $checkisSubmitted = DB::table('timesheet_details')
        ->select('*')
        ->where('user_timesheet', Auth::user()->id)
        ->whereNotIn('ts_status_id', [10,404])
        ->where('month_periode', $year.intval($month))
        ->whereNotExists(function ($query) use ($year, $month) {
            $query->select(DB::raw(1))
                ->from('timesheet_details')
                ->where('month_periode', $year.intval($month))
                ->where('user_timesheet', Auth::user()->id)
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

        // dd($Check, $checkisSubmitted);

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
        $dateTime = new DateTime($date);
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
            ->whereNull('ts_type')
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

        $getTotalDays = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)
        ->groupBy('ts_date')
        ->get()
        ->count();
        // return response()->json($activities);
        return view('timereport.preview', compact('year', 'month', 'getTotalDays', 'removeBtnSubmit', 'totalHours', 'info', 'assignmentNames', 'formattedDatesWeekendRepl', 'srtDate', 'startDate', 'endDate', 'formattedDates', 'formattedDatesHoliday'), ['activities' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }

    public function submit_timesheet($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the current year and month
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Convert the passed month to an integer
        $month = intval($month);

        // Check if the passed year is beyond the current year
        // or if the passed month is beyond the current month
        if ($year > $currentYear || ($year == $currentYear && $month >= $currentMonth)) {
            // Don't allow submission
            // Redirect or return an error message
            return redirect()->back()->with('failed', 'Cannot submit timesheet for future months.');
        }

        $dateCut = Cutoffdate::find(1);
        $currentDay = date('j');

        // Get the cutoff date for submitting timesheets (7th of the next month)
        $cutoffDate = Carbon::create($year, $month)->addMonths(1)->startOfMonth()->addDays(($dateCut->closed_date - 1));

        // Get the start date for timesheets
        $startDate = Carbon::create($year, $month)->addMonths(1)->startOfMonth()->addDays(($dateCut->start_date - 1));

        // Check if the current date is on or after the start date AND on or before the cutoff date
        // if ($currentDate->gte($startDate) && $currentDate->lte($cutoffDate)) {
        if ($currentDay >= $dateCut->start_date && $currentDay <= $dateCut->closed_date) {
            // Allow access to the page
            // Get the start and end dates for the selected month
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month)->endOfMonth();

            // Get the Timesheet records between the start and end dates
            $tsOfTheMonth = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_id_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

            if ($tsOfTheMonth->isEmpty()) {
                Session::flash('failed', "You have to fill your timesheet first!");
                return redirect(url()->previous());
            }

            $userId = Auth::user()->id;

            $subquery = DB::table('timesheet')
                ->select('ts_task', 'ts_location', 'ts_user_id', DB::raw('CAST(incentive AS DECIMAL(10, 2)) AS incentive'), 'ts_task_id', 'ts_id_date', 'allowance')
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY ts_id_date ORDER BY CAST(incentive AS DECIMAL(10, 2)) DESC) AS rn')
                ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('ts_user_id', $userId)
                ->groupBy('ts_id_date', 'ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id');

            $sql = '(' . $subquery->toSql() . ') as subquery';

            $results = DB::table(DB::raw($sql))
                ->mergeBindings($subquery)
                ->select('ts_task', 'ts_location', 'ts_user_id', 'incentive as incentive', 'ts_task_id', 'allowance as max_allowance', DB::raw('COUNT(*) as total_rows'))
                ->where('rn', 1)
                ->groupBy('ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id')
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
                'activity' => 'Submitted',
                'month_periode' => $year . intval($month),
            ], [
                'ts_status_id' => 15,
                'date_submitted' => date('Y-m-d'),
                'workhours' => '-',
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
                // $test2 = Project_assignment_user::where('role', "PM")->where('company_project_id', $test)->where('periode_end', '>=', date('Y-m-d'))->pluck('user_id')->first();
                $getPM = Project_assignment_user::where('role', "PM")
                    ->where('company_project_id', $test)
                    ->where('periode_end', '>=', date('Y-m-d'))
                    ->pluck('user_id');
                $pa = Project_assignment_user::where('role', "PA")->where('company_project_id', $test)->where('periode_end', '>=', date('Y-m-d'))->pluck('user_id')->first();
                $checkRole = Project_assignment_user::where('user_id', Auth::user()->id)->where('project_assignment_id', $row->ts_task_id)->pluck('role')->first();

                $countIncentive = $row->total_rows * $row->incentive;

                $countAllowance = $row->total_rows * $row->max_allowance;

                $totalIncentive = $countIncentive;
                $totalAllowances = $countAllowance;
                switch ($row->ts_task) {
                    case "HO":
                    case "Sick":
                    case "StandbyLK":
                    case "StandbyLN":
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
                                            'priority' => 1,
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
                                            'priority' => 1,
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
                                                'priority' => 1,
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
                                        'priority' => 1,
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
                                        'priority' => 1,
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
                                        'priority' => 1,
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
                                'priority' => 1,
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
                                'priority' => 1,
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
                                    'priority' => 1,
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
                                        'priority' => 1,
                                    ];
                                    $empApproval[] = $newArrayS;
                                break;
                                default:
                                if(!$getPM->isEmpty()){
                                    foreach($getPM as $pm){
                                        if(Auth::id() == $pm){
                                            break;
                                        }
                                        $newArrayPM = [
                                            'name' => $pm,
                                            'task' => $row->ts_task,
                                            'location' => $row->ts_location,
                                            'mandays' => $row->total_rows,
                                            'role' => $checkRole,
                                            'task_id' => $row->ts_task_id,
                                            'total_incentive' => $totalIncentive,
                                            'total_allowance' => $totalAllowances,
                                            'priority' => 3,
                                        ];
                                        $empApproval[] = $newArrayPM;
                                    }
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
                                        'priority' => 4,
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
                                        'priority' => 1,
                                    ];
                                    $empApproval[] = $newArrayS;
                                }
                                break;
                            }
                        }
                        break;
                }
            }

            $getTotalDays = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('ts_user_id', Auth::user()->id)
            ->groupBy('ts_date')
            ->get()
            ->count();

            //delete previous data if resubmitted
            Timesheet_detail::where('month_periode', $year . intval($month))->where('user_timesheet', Auth::user()->id)->whereNotIn('ts_status_id', [10, 15])->delete();
            $work_hours = 0;
            $start_time = PHP_INT_MAX;
            $end_time = 0;
            $total_work_hours = 0;

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
            foreach ($tsOfTheMonth as $timesheet) {
                $current_start_time = strtotime($timesheet->ts_from_time);
                $current_end_time = strtotime($timesheet->ts_to_time);

                if ($current_start_time < $start_time) {
                    $start_time = $current_start_time;
                }

                if ($current_end_time > $end_time) {
                    $end_time = $current_end_time;
                }
            }

            if ($end_time > $start_time) {

                $time_diff_seconds = $end_time - $start_time;
                $time_diff_hours = gmdate('H', $time_diff_seconds);
                $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
            }
            $totalHoursWithoutDays = intval($total_work_hours) - $getTotalDays;
            $totalMinutes = ($total_work_hours - intval($total_work_hours)) * 60; // Extract minutes
            $percentage = (($totalHoursWithoutDays + $totalMinutes / 60) / $totalHours) * 100;
            $final = $totalHoursWithoutDays." Hours ". "(".intval($percentage)."%)";

            foreach ($empApproval as $test) {
                Timesheet_detail::updateOrCreate([
                    'user_id' => Auth::user()->id,
                    'month_periode' => $year . intval($month),
                    'RequestTo' => $test['name'],
                    'ts_task' => $test['task'],
                    'ts_location' => $test['location']
                ], [
                    'workhours' => $final,
                    'ts_mandays' => $test['mandays'],
                    'activity' => 'Waiting for Approval',
                    'roleAs' => $test['role'],
                    'date_submitted' => date('Y-m-d'),
                    'ts_status_id' => 20,
                    'total_incentive' => $test['total_incentive'],
                    'total_allowance' => $test['total_allowance'],
                    'note' => '',
                    'ts_task_id' => $test['task_id'],
                    'user_timesheet' => Auth::user()->id,
                    'priority' => $test['priority']
                ]);
            }
            Timesheet_detail::where('RequestTo', Auth::user()->id)->where('month_periode', $year . intval($month))->where('user_timesheet', Auth::user()->id)->delete();
            // Update Timesheet records between the start and end dates
            Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('created_at', 'desc')->update(['ts_status_id' => '15']);

            $ts_date_desc = date("F", mktime(0, 0, 0, $month, 1)) . ' ' . $year;

            if($total_work_hours < $totalHours){
                Session::flash('failed', "Insufficient Total Work Hours This Month! We've noticed that your total work hours for the current month are below the required threshold. Please be informed that this may lead to a deduction in your allowances. To ensure you receive your full allowances, make sure to meet the required number of work hours. If you have any concerns or questions, please reach out to your supervisor or the HR department.");
            }
            // return response()->json($activities);
            Session::flash('success', "Your Timereport $ts_date_desc has been submitted!");
            return redirect()->back();
        } else {
            Session::flash('failed', '403 - Timesheet submission is not allowed for this date or closed for this month');
            return redirect()->back();
        }
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

        $userId = Auth::user()->id;

        $subquery = DB::table('timesheet')
            ->select('ts_task', 'ts_location', 'ts_user_id', DB::raw('CAST(incentive AS DECIMAL(10, 2)) AS incentive'), 'ts_task_id', 'ts_id_date', 'allowance')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY ts_id_date ORDER BY CAST(incentive AS DECIMAL(10, 2)) DESC) AS rn')
            ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('ts_user_id', $userId)
            ->groupBy('ts_id_date', 'ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id');

        $sql = '(' . $subquery->toSql() . ') as subquery';

        $results = DB::table(DB::raw($sql))
            ->mergeBindings($subquery)
            ->select('ts_task', 'ts_location', 'ts_user_id', 'incentive as incentive', 'ts_task_id', 'allowance as max_allowance', DB::raw('COUNT(*) as total_rows'))
            ->where('rn', 1)
            ->groupBy('ts_task', 'ts_location', 'ts_user_id', 'incentive', 'ts_task_id')
            ->get();

        $pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'), ['timesheet' => $activities, 'results' => $results,  'user_info' => $user_info]);
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
        $userSelected = Null;

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNull('resignation_date');
		})->get();

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        // var_dump($Year.intval($Month));
        $approvals = Timesheet_detail::groupBy('user_timesheet', 'ts_task', 'activity', 'RequestTo')->orderBy('user_timesheet', 'asc')->orderBy('ts_task', 'asc')->orderBy('priority', 'desc');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $userSelected = $request->showOpt;

            if($userSelected == 1){
                $approvals->where('month_periode', $Year . intval($Month));
            } else {
                $approvals->where('month_periode', $Year . intval($Month))->where('user_timesheet', $userSelected);
            }
        } else {
            $approvals->where('month_periode', $Year . intval($Month));
        }

        $approvals = $approvals->get();
        // dd($approvals);
        return view('timereport.summary', compact('approvals', 'userSelected', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function remind($id, $year, $month)
    {
        $employees = User::where('id', $id)->get();

        foreach ($employees as $employee) {
            dispatch(new SendTimesheetReminderSummary($employee, $year, $month));
        }
        return redirect()->back()->with('success', "An email has been sent!");
    }

    public function download_surat($timesheet_id,$userId)
    {
        $getFile = Surat_penugasan::where('timesheet_id', $timesheet_id)->where('user_id', $userId)->first();
        $filePath = public_path($getFile->file_path);

        // Check if the file exists
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        // File not found
        abort(404);
    }

    public function getLocationProject($task_id)
    {
        $itemData = Project_assignment_user::where('project_assignment_id', $task_id)->pluck('company_project_id')->toArray();

            if (empty($itemData)) {
                $getLoc = Project_location::all(); // Change $getLoc to $getLocations
            } else {
                $getLocations = Company_project::whereIn('id', $itemData)->pluck('alias')->toArray();

                // Initialize an array to store distinct locations
                $distinctLocations = [];

                // Loop through the locations
                foreach ($getLocations as $locations) {
                    // Split the comma-separated values into an array
                    $locationArray = explode(', ', $locations);

                    // Add each distinct location to the result array
                    $distinctLocations = array_merge($distinctLocations, $locationArray);
                }

                // Remove duplicates
                $distinctLocations = array_unique($distinctLocations);

                // You now have an array of distinct locations
                $getLoc = Project_location::whereIn('location_code', $distinctLocations)->get();
            }

        return response()->json($getLoc);
    }


    public function checkIsHoliday()
    {
        $getData = Surat_penugasan::all();
        foreach($getData as $wr){
            $dateString = $wr->ts_date;
            $getDayStat = null;
            $originalDate = Carbon::createFromFormat('Y-m-d', $dateString);
            $threeMonthsLater = $originalDate->addMonths(3)->format('Y-m-d');

            // Check if conversion was successful
            if ($originalDate !== false) {
                $getDayStat = $this->getDayStatus($originalDate);
            }
            if($getDayStat == "red"){
                $wr->isWeekend = TRUE;
            } else {
                $wr->isWeekend = FALSE;
            }
            if(!$wr->isTaken){
                $wr->isTaken = FALSE;
            }
            $wr->expiration = $threeMonthsLater;
            $wr->save();
        }
    }
}
