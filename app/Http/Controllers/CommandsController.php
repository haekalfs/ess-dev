<?php

namespace App\Http\Controllers;

use App\Jobs\CheckEmploymentStatus;
use App\Jobs\CutLeaveBasedOnHolidaysJob;
use App\Jobs\SendTimesheetApprovalNotification;
use App\Jobs\SendTimesheetReminderJob;
use App\Models\Checkinout;
use App\Models\Commands;
use App\Models\Notification_alert;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CommandsController extends Controller
{
    public function index()
    {
        $data = Commands::all();
        //get Data
        $attendanceData = Checkinout::select('user_id', 'date', DB::raw('MIN(time) as earliest_time'), DB::raw('MAX(time) as latest_time'))
        ->groupBy('user_id', 'date')
        ->get();

        return view('commands.index', ['data' => $data, 'attendanceData' => $attendanceData]);
    }

    public function cut_leave_based_on_joint_holidays()
    {
        dispatch(new CutLeaveBasedOnHolidaysJob());
        return redirect()->back()->with('success', "All Employees Leave Quota has been Deducted!");
    }

    public function send_reminder_timesheet_entry()
    {
        // Retrieve user IDs to be reminded
        $userIds = Users_detail::groupBy('user_id')->pluck('user_id')->toArray();

        // Get the current date
        $currentDate = Carbon::now();

        // Calculate the previous month
        $previousMonth = $currentDate->subMonth();

        // Obtain the previous month and year separately
        $year = $previousMonth->year;
        $month = $previousMonth->month;

        // If you need the month in a specific format (e.g., with leading zeros), you can use the format method
        $monthFormatted = $previousMonth->format('m');

        // Dispatch a job for each user to be reminded
        foreach ($userIds as $userId) {
            dispatch(new SendTimesheetReminderJob($userId, $year, $monthFormatted));
        }

        return redirect()->back()->with('success', "Reminder has bent sent to all employees!");
    }

    public function send_approval_timesheet_entry()
    {
        // $users = User::where('id', 'haekals')->get();
        $userToApprove = Timesheet_detail::where('ts_status_id', 20)->groupBy('RequestTo')->pluck('RequestTo')->toArray();
        $users = User::whereIn('id', $userToApprove)->get();

        foreach ($users as $user) {
            dispatch(new SendTimesheetApprovalNotification($user));
        }

        $notification = Timesheet_detail::where('ts_status_id', 20)
        ->whereIn('RequestTo', $userToApprove)
        ->groupBy('month_periode', 'user_timesheet', 'RequestTo')
        ->get();

        foreach($notification as $data){
            $entry = new Notification_alert;
            $entry->user_id = $data->RequestTo;
            $entry->message = "Notification by System";
            $entry->importance = 1;
            $entry->month_periode = $data->month_periode;
            $entry->type = 2;
            $entry->save();
        }

        return redirect()->back()->with('success', "Reminder has bent sent to all approvers!");
    }

    public function notify_hr_employment_status()
    {
        // dispatch(new CutLeaveBasedOnHolidaysJob());
        // return redirect()->back()->with('success', "All Employees Leave Quota has been Deducted!");
        // Dispatch the job
        CheckEmploymentStatus::dispatch();

        // Optionally, you can flash a message to indicate successful dispatch
        return redirect()->back()->with('success', 'Employment status check job dispatched successfully!');
    }

    public function testDatabaseConnection()
    {
        // Database configuration
        $dbHost = '203.161.184.119'; // Replace with your database host
        $dbPort = '3306'; // Replace with your database port (typically 3306 for MySQL)
        $dbName = 'perdanac_web2022'; // Replace with your database name
        $dbUsername = 'perdanac_admin24'; // Replace with your database username
        $dbPassword = 'Predana@2024!'; // Replace with your database password

        // Database connection
        try {
            $connection = new \PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUsername, $dbPassword);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Execute a sample query to test the connection
            $statement = $connection->query('SELECT * FROM wpx3_posts LIMIT 1');
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            // Close the database connection
            $connection = null;

            // Return the result
            return response()->json(['success' => true, 'message' => 'Database connection successful.', 'data' => $result]);
        } catch (\PDOException $e) {
            // Return error message if connection fails
            return response()->json(['success' => false, 'message' => 'Database connection failed.', 'error' => $e->getMessage()]);
        }
    }
}
