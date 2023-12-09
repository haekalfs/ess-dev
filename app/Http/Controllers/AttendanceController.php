<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAttendanceData;
use App\Jobs\SendDataAttendance;
use App\Models\Checkinout;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function downloadLogData(Request $request)
    {
        //dispatch
        Checkinout::truncate();
        ProcessAttendanceData::dispatch();

        //get data
        $data = Checkinout::select('user_id', 'date', DB::raw('MIN(time) as earliest_time'), DB::raw('MAX(time) as latest_time'))
        ->groupBy('user_id', 'date')
        ->get();

        sleep(5);

        return redirect()->back()->with('success', "Attendance Data has been gathered to temporary table! Go to the next step, send the data to the Timesheet Table!")->with('attendanceData', $data);
    }

    public function sendData()
    {
        $maxAttempts = 5;
        $attempts = 0;
        $success = false;

        while (!$success && $attempts < $maxAttempts) {
            try {
                dispatch(new ProcessAttendanceData());
                // If ProcessAttendanceData runs successfully without throwing exceptions,
                // it reaches here without encountering a catch block.
                dispatch(new SendDataAttendance());
                $success = true; // Mark the job as successful
            } catch (\Exception $e) {
                // Handle exception or log error
                sleep(5); // Wait for 5 seconds before retrying
                $attempts++;
            }
        }

        return redirect()->back()->with('success', "Employee's Attendance Data has been successfully sent to Timesheet Table!");
    }
}
