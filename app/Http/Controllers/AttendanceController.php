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
        ProcessAttendanceData::dispatch();

        return redirect()->back()->with('success', "Attendance Data has been gathered to temporary table! Go to the next step, send the data to the Timesheet Table!");
    }

    public function sendData()
    {
        SendDataAttendance::dispatch();

        return redirect()->back()->with('success', "Employee's Attendance Data has been successfully sent to Timesheet Table!");
    }
}
