<?php

namespace App\Http\Controllers;

use App\Mail\EssMailer;
use App\Models\Approval_status;
use App\Models\Company_project;
use App\Models\Cutoffdate;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
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
        if($yearSelected){
            $currentMonth = 12;
            if($yearSelected == date('Y')){
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
                if(!$status){
                    $status = "Unknown Status";
                }
                $encryptYear = Crypt::encrypt($currentYear);
                $encryptMonth = Crypt::encrypt($entry);
                $validStatusIDs = ['20', '29', '30', '40'];
                $isSubmitted = in_array($lastUpdate->ts_status_id, $validStatusIDs);
                $lastUpdatedAt = $lastUpdate->updated_at;
                $editUrl = "/timesheet/entry/".$encryptYear."/".$encryptMonth;
            } else {
                $encryptYear = Crypt::encrypt($currentYear);
                $encryptMonth = Crypt::encrypt($entry);
                $isSubmitted = false;
                $status = 'None';
                $lastUpdatedAt = 'None';
                $editUrl = "/timesheet/entry/".$encryptYear."/".$encryptMonth;
            }
            $encryptYear = Crypt::encrypt($currentYear);
            $encryptMonth = Crypt::encrypt($entry);
            $previewUrl = "/timesheet/entry/preview/".$encryptYear."/".$encryptMonth;
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


        // Check tanggal merah berdasarkan libur nasional
        if (isset($array[$date->format('Ymd')])) {
            return "red";
        }
        // Check tanggal merah berdasarkan hari minggu
        elseif ($date->format('D') === "Sun") {
            return "red";
        }
        elseif ($date->format('D') === "Sat") {
            return "red";
        }
        // Bukan tanggal merah
        else {
            return "";
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

        $assignment = DB::table('project_assignment_users')
            ->join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.*', 'project_assignments.*')
            ->where('project_assignment_users.user_id', '=', $userId)
            // ->where('project_assignment_users.periode_end', '>', date('Y-m-d'))
            ->where('project_assignments.approval_status', 29)
            ->get();
        $validStatusIDs = ['20', '29', '30', '40'];
        if($lastUpdate){
            if(in_array($lastUpdate->ts_status_id, $validStatusIDs)) {
                Session::flash('failed',"You've already submitted your timereport!");
                return redirect()->route('timesheet');
            }else{
                $encryptYear = Crypt::encrypt($year);
                $encryptMonth = Crypt::encrypt($month);
                $previewButton = "/timesheet/entry/preview/".$encryptYear."/".$encryptMonth;
                return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment', 'pLocations'));
            }
        } else {
            $encryptYear = Crypt::encrypt($year);
            $encryptMonth = Crypt::encrypt($month);
            $previewButton = "/timesheet/entry/preview/".$encryptYear."/".$encryptMonth;
            return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment', 'pLocations'));
        }
        // // Return the calendar view with the calendar data
        // return view('timereport.timesheet_entry', compact('calendar', 'year', 'month'));
    }
    

    public function save(Request $request)
    {
        foreach ($request->activities as $date => $activities) {
            Timesheet::updateOrCreate(
                ['ts_user_id' => 'haekals', 'ts_date' => $date],
                [
                    'ts_from_time' => $activities['from'],
                    'ts_to_time' => $activities['to'],
                    'ts_activity' => $activities['activity'],
                    // Add more activity columns as needed
                ]
            );
        }
        Session::flash('success',"Timesheet has been saved!");
        return redirect()->back();
    }

    public function showCalendar($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the number of days in the specified month and year
        $numDays = Carbon::create($year, $month)->daysInMonth;

        // Create an empty array to store the calendar data
        $calendar = [];

        // Loop through each day of the month and check if it is a holiday
        for ($i = 1; $i <= $numDays; $i++) {
            $date = Carbon::create($year, $month, $i)->format('Ymd');
            $holiday = $this->tanggalMerah($date);
            $calendar[$i] = [
                'date' => $i,
                'holiday' => $holiday,
            ];
        }

        // Return the calendar view with the calendar data
        return view('timereport.testing', compact('calendar'));
    }

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
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $entry = new Timesheet;
        $ts_task_id = $request->task;
        $task_project = Project_assignment::where('id', $ts_task_id)->get(); 
        while (Project_assignment::where('id', $ts_task_id)->exists()){
            foreach($task_project as $tp){
                $ts_task_id = $tp->company_project->project_name;
            }
        }
        $entry->ts_user_id = Auth::user()->id;
        $entry->ts_id_date = str_replace('-','',$request->clickedDate);
        $entry->ts_date = $request->clickedDate;
        $entry->ts_task = $ts_task_id;
        $entry->ts_task_id = $request->task;
        $entry->ts_location = $request->location;
        $entry->ts_from_time = date('H:i', strtotime($request->from));;
        $entry->ts_to_time = date('H:i', strtotime($request->to));
        $entry->ts_activity = $request->activity;
        $entry->ts_status_id = '10';
        $entry->save();
    
        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => date("Yn", strtotime($request->clickedDate))],['date_submitted' => date('Y-m-d'),'ts_status_id' => '10', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        return response()->json(['success' => 'Entry saved successfully.']);
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
        $activity = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if ($activity) {
            $activity->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Activity not found']);
        }
    }

    public function preview($year, $month)
    {
        $year = Crypt::decrypt($year);
        $month = Crypt::decrypt($month);
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();
        
        $user_info = User::find(Auth::user()->id);

        $workflow = Timesheet_detail::where('user_timesheet', Auth::user()->id)->where('month_periode', $year.$month)->get();

        $info = [];
        $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $month)
                ->whereYear('ts_date', $year)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', Auth::user()->id)
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
        return view('timereport.preview', compact('year', 'month','info'), ['timesheet' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
    }

    public function submit_timesheet($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the current date
        $currentDate = Carbon::now();

        $dateCut = Cutoffdate::first();
        // Get the cutoff date for submitting timesheets (7th of the next month)
        // $cutoffDate = Carbon::create($year, $month)->addMonths(1)->startOfMonth()->addDays(($dateCut->date - 1));
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
        $tsOfTheMonth = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

        if ($tsOfTheMonth->isEmpty()) {
            Session::flash('failed', "You have to fill your timesheet first!");
            return redirect(url()->previous());
        }
        $total_work_hours = 0;
        foreach($tsOfTheMonth as $sum){
            $start_time = strtotime($sum->ts_from_time);
            $end_time = strtotime($sum->ts_to_time);
            $time_diff_seconds = $end_time - $start_time;
            $time_diff_hours = gmdate('H', $time_diff_seconds);
            $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
            $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60)); echo $time_diff_hours.':'.$time_diff_minutes;
        }

        $countRows = DB::table('timesheet')
        ->selectRaw('ts_task, ts_location, ts_user_id, MAX(ts_task_id) AS ts_task_id, COUNT(*) AS total_rows')
        ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->where('ts_user_id', Auth::user()->id)
        ->groupBy('ts_task', 'ts_location', 'ts_user_id')
        ->get();
    
        $l10 = Timesheet_approver::where('id', 10)->pluck('approver')->first();
        $l15 = Timesheet_approver::where('id', 15)->pluck('approver')->first();
        $l20 = Timesheet_approver::where('id', 20)->pluck('approver')->first();
        $l45 = Timesheet_approver::where('id', 45)->pluck('approver')->first();
        $l40 = Timesheet_approver::where('id', 40)->pluck('approver')->first();
        
        
        // var_dump($countRows);
        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate([
                'user_id' => Auth::user()->id,
                'workhours' => intval($total_work_hours),
                'activity' => 'Submitted',
                'month_periode' => $year.$month,
            ], [
                'date_submitted' => date('Y-m-d'),
                'note' => '',
                'user_timesheet' => Auth::user()->id
            ]);
            $test = Project_assignment::where('id', $row->ts_task_id)->pluck('company_project_id')->first();
            $test2 = Project_assignment_user::where('role', "PM")->where('company_project_id', $test)->pluck('user_id')->first();
            $checkRole = Project_assignment_user::where('user_id', Auth::user()->id)->where('project_assignment_id', $row->ts_task_id)->pluck('role')->first();
            $pa = Project_assignment_user::where('role', "PA")->where('company_project_id', $test)->pluck('user_id')->first();
            switch ($row->ts_task) {
                case "HO":
                case "Sick":
                case "Standby":
                case "Other":
                case "Idle":
                case "Training":
                case "Trainer":
                    $requestTo = in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray()) ? $l15 : $l10;
                    Timesheet_detail::updateOrCreate([
                        'user_id' => Auth::user()->id,
                        'workhours' => intval($total_work_hours),
                        'RequestTo' => $requestTo,
                        'month_periode' => $year.$month,
                        'ts_task' => $row->ts_task,
                        'ts_location' => $row->ts_location
                    ], [
                        'ts_mandays' => $row->total_rows,
                        'date_submitted' => date('Y-m-d'),
                        'ts_status_id' => '20',
                        'activity' => 'Waiting for Approval',
                        'note' => '',
                        'ts_task_id' => $row->ts_task_id,
                        'user_timesheet' => Auth::user()->id
                    ]);
        
                    Timesheet_detail::updateOrCreate([
                        'user_id' => Auth::user()->id,
                        'workhours' => intval($total_work_hours),
                        'RequestTo' => $l45,
                        'month_periode' => $year.$month,
                        'ts_task' => $row->ts_task,
                        'ts_location' => $row->ts_location
                    ], [
                        'ts_mandays' => $row->total_rows,
                        'date_submitted' => date('Y-m-d'),
                        'ts_status_id' => '30',
                        'activity' => 'Waiting for Approval',
                        'note' => '',
                        'ts_task_id' => $row->ts_task_id,
                        'user_timesheet' => Auth::user()->id
                    ]);
                    break;
        
                default:
                
                if($test2 == NULL){
                    if($pa == NULL){
                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l20, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '30', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l40, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '40', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
                    } else {
                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $pa, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '30', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
                        
                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l20, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '30', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l40, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '40', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
                    }
                } else {
                    if($pa == NULL){
                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $test2, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '20', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l20, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '30', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l40, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '40', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
                    } else {
                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $test2, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '20', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $pa, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '25', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l20, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '30', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);

                        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'workhours' => intval($total_work_hours), 'month_periode' => $year.$month, 'RequestTo' => $l40, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                        ['ts_mandays' => $row->total_rows, 'activity' => 'Waiting for Approval', 'roleAs' => $checkRole, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '40', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
                    }
                }
                break;
            }
        }
        // Update Timesheet records between the start and end dates
        Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('created_at', 'desc')->update(['ts_status_id' => '20']);
      
        // return response()->json($activities);
        Session::flash('success',"Timereport $year - 0$month has been submitted!");
        return redirect()->back();
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
 
    	$pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'),['timesheet' => $activities,  'user_info' => $user_info,]);
    	return $pdf->download('timesheet - '. $year . $month.'.pdf');
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
 
    	$pdf = PDF::loadview('timereport.timereport_pdf', compact('year', 'month', 'user_info_emp_id'),['timesheet' => $activities,  'user_info' => $user_info,]);
    	return $pdf->download('timesheet #'.$user_timesheet . '-' . $year . $month.'.pdf');
    }

    public function getActivitiesEntry($year, $month, $id)
    {
        // Use the $year, $month, and $id parameters to fetch data from your database or other data source
        $data = Timesheet::find($id);

        // Return the data as a JSON response
        return response()->json($data);
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

        $entry = Timesheet::find($id);
        $ts_task_id = $request->update_task;
        $task_project = Project_assignment::where('id', $ts_task_id)->get(); 
        while (Project_assignment::where('id', $ts_task_id)->exists()){
            foreach($task_project as $tp){
                $ts_task_id = $tp->company_project->project_name;
            }
        }
        $entry->ts_task = $ts_task_id;
        $entry->ts_task_id = $request->update_task;
        $entry->ts_location = $request->update_location;
        $entry->ts_from_time = date('H:i', strtotime($inputFromTimeUpdate));
        $entry->ts_to_time = date('H:i', strtotime($inputToTimeUpdate));
        $entry->ts_activity = $request->update_activity;
        $entry->save();

        return response()->json(['success' => 'Entry updated successfully.']);
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

        $dateString = $request->daterange;
        list($startDateString, $endDateString) = explode(' - ', $dateString);
        $startDate = DateTime::createFromFormat('m/d/Y', $startDateString);
        $endDate = DateTime::createFromFormat('m/d/Y', $endDateString);
        $interval = new DateInterval('P1D'); // Interval of 1 day

        $month_periode = $startDate->format('Yn');

        // Loop through each day between start and end dates
        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->format('N');
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $startDate->add($interval);
                continue;
            }

            // Insert the entry to the database for this day
            $entry = new Timesheet;
            $ts_task_id = $request->task;
            $task_project = Project_assignment::where('id', $ts_task_id)->get(); 
            while (Project_assignment::where('id', $ts_task_id)->exists()){
                foreach($task_project as $tp){
                    $ts_task_id = $tp->company_project->project_name;
                }
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
            $entry->save();
        
            // Move to the next day
            $startDate->add($interval);
        }
        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => $month_periode],['date_submitted' => date('Y-m-d'),'ts_status_id' => '10', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        return response()->json(['success' => "Entry saved successfully. $request->daterange"]);
    }
}
