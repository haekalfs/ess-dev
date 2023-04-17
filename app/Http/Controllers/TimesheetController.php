<?php

namespace App\Http\Controllers;

use App\Mail\EssMailer;
use App\Models\Company_project;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Timesheet;
use App\Models\Timesheet_workflow;
use App\Models\User;
use App\Models\Users_detail;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TimesheetController extends Controller
{
    public function index()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
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
                if($lastUpdate->ts_status_id == '10'){
                    $status = "Saved";
                }elseif($lastUpdate->ts_status_id == '20'){
                    $status = "Submitted";
                }elseif($lastUpdate->ts_status_id == '29'){
                    $status = "Approved";
                }else{
                    $status = "Rejected";
                }
                $encryptYear = Crypt::encrypt($currentYear);
                $encryptMonth = Crypt::encrypt($entry);
                $isSubmitted = ($lastUpdate->ts_status_id == '20' || $lastUpdate->ts_status_id == '29');
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
        return view('timereport.timesheet', compact('entries'));
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
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");
    
        // Get the number of days in the selected month
        $numDays = Carbon::create($year, $month)->daysInMonth;
    
        // Create an empty array to store the calendar data
        $calendar = [];
    
        // Add the days of the week as the first row
        $calendar[] = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
        // Get the last day of the previous month
        $lastDayPrevMonth = Carbon::create($year, $month, 1)->subDay()->day;
    
        // Get the day of the week that the 1st of the month falls on
        $dayOfWeek = Carbon::create($year, $month, 1)->dayOfWeek;
    
        // Create a counter to keep track of the day we're on
        $dayCounter = 1;
    
        // Create a flag to indicate whether we've added the first day of the month
        $firstDayAdded = false;
    
        // Loop through each day of the month and check if it is a holiday
        for ($i = 0; $i < 6; $i++) {
            // Create an empty array to represent the current week
            $week = [];
    
            // Add the days from the previous month for the first week
            if (!$firstDayAdded) {
                for ($j = $dayOfWeek - 1; $j >= 0; $j--) {
                    $week[] = $lastDayPrevMonth - $j;
                }
    
                // Add the first day of the month
                $week[] = $dayCounter;
                $dayCounter++;
                $firstDayAdded = true;
            }
    
            // Add the rest of the days of the week
            for ($j = count($week); $j < 7 && $dayCounter <= $numDays; $j++) {
                $week[] = $dayCounter;
                $dayCounter++;
            }
    
            // Pad out the end of the last week with empty cells
            while (count($week) < 7) {
                $week[] = '';
            }
    
            // Add the week to the calendar array
            $calendar[] = $week;
    
            // If we've added all the days in the month, we're done
            if ($dayCounter > $numDays) {
                break;
            }
        }
        // Company_project::all();
        $userId = Auth::user()->id;
        $assignment = DB::table('project_assignment_users')
            ->join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.*', 'project_assignments.*')
            ->where('project_assignment_users.user_id', '=', $userId)
            ->get();
        if($lastUpdate){
            if($lastUpdate->ts_status_id == '20' || $lastUpdate->ts_status_id == '29') {
                Session::flash('failed',"You've already submitted your timereport!");
                return redirect()->route('timesheet');
            }else{
                $encryptYear = Crypt::encrypt($year);
                $encryptMonth = Crypt::encrypt($month);
                $previewButton = "/timesheet/entry/preview/".$encryptYear."/".$encryptMonth;
                return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment'));
            }
        } else {
            $encryptYear = Crypt::encrypt($year);
            $encryptMonth = Crypt::encrypt($month);
            $previewButton = "/timesheet/entry/preview/".$encryptYear."/".$encryptMonth;
            return view('timereport.timesheet_entry', compact('calendar', 'year', 'month', 'previewButton', 'assignment'));
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

    public function tanggalMerah($value) {
        $array = json_decode(file_get_contents("https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json"),true);

        //check tanggal merah berdasarkan libur nasional
        if(isset($array[$value])) {
            return "tanggal merah ".$array[$value]["deskripsi"];
        }

        //check tanggal merah berdasarkan hari minggu
        elseif(date("D",strtotime($value))==="Sun") {
            return "tanggal merah hari minggu";
        }

        //bukan tanggal merah
        else {
            return "bukan tanggal merah";
        }
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
    
        $inputFromTime = $request->from;
        $inputToTime = $request->to;
        // check if time is in 24-hour format
        if (preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $inputFromTime)) {
            $formattedFromTime = $inputFromTime;
        } else {
            // convert time from 12-hour format to 24-hour format
            $formattedFromTime = date('H:i', strtotime($inputFromTime));
        }
        if (preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $inputToTime)) {
            $formattedToTime = $inputToTime;
        } else {
            // convert time from 12-hour format to 24-hour format
            $formattedToTime = date('H:i', strtotime($inputToTime));
        }

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
    
        Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => date("Yn", strtotime($request->clickedDate))],['date_submitted' => date('Y-m-d'),'ts_status_id' => '10', 'note' => '', 'user_timesheet' => Auth::user()->id]);

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
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('created_at', 'desc')->where('ts_user_id', Auth::user()->id)->get();
        
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

        $workflow = Timesheet_workflow::where('user_id', Auth::user()->id)->where('month_periode', $year.$month)->get();

        $info = [];
        $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $month)
                ->whereYear('ts_date', $year)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', Auth::user()->id)
                ->first();
        if ($lastUpdate) {
            if($lastUpdate->ts_status_id == '10'){
                $status = "Saved";
            }elseif($lastUpdate->ts_status_id == '20'){
                $status = "Submitted";
            }elseif($lastUpdate->ts_status_id == '29'){
                $status = "Approved";
            }elseif($lastUpdate->ts_status_id == '404'){
                $status = "Rejected";
            }else{
                $status = "404";
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

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $countRows = DB::table('timesheet')
        ->selectRaw('ts_task, ts_location, max(ts_task_id) as ts_task_id, count(*) as total_rows')
        ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->groupBy('ts_task', 'ts_location') // ts_task_id is calculated with max() function
        ->get();
    
                // var_dump($countRows);
        foreach($countRows as $row) {
            if (in_array($row->ts_task, ["HO", "Sick", "StandBy"])) {
                Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Submitted', 'RequestTo' => 'hr', 'month_periode' => $year.$month, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                ['ts_mandays' => $row->total_rows, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '20', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
            } else {
                Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Submitted', 'month_periode' => $year.$month, 'RequestTo' => 'PM', 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],
                ['ts_mandays' => $row->total_rows, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '20', 'note' => '', 'ts_task_id' => $row->ts_task_id, 'user_timesheet' => Auth::user()->id]);
            }
            
            // Timesheet_workflow::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Submitted', 'month_periode' => $year.$month, 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location],['ts_mandays' => $row->total_rows, 'date_submitted' => date('Y-m-d'),'ts_status_id' => '20', 'note' => '', 'user_timesheet' => Auth::user()->id]);
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
        $entry->ts_task = $request->update_task;
        $entry->ts_location = $request->update_location;
        $entry->ts_from_time = date('H:i', strtotime($inputFromTimeUpdate));
        $entry->ts_to_time = date('H:i', strtotime($inputToTimeUpdate));
        $entry->ts_activity = $request->update_activity;
        $entry->ts_status_id = '10';
        $entry->save();

        return response()->json(['success' => 'Entry updated successfully.']);
    }
}
