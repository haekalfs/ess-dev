<?php

namespace App\Http\Controllers;

use App\Imports\CheckInOutImport;
use App\Jobs\ProcessAttendanceData;
use App\Jobs\SendDataAttendance;
use App\Models\Checkinout;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                // dispatch(new ProcessAttendanceData());
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

    public function import(Request $request)
    {
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file);

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        foreach ($rows as $key => $row) {
            if ($key === 0) continue; // Skip the header row

            $date = $row[0]; // Assuming the date is in the first column
            $payroll = $row[1]; // Assuming payroll is in the second column
            $tapIn = $row[3]; // Assuming Tap in is in the third column
            $tapOut = $row[4]; // Assuming Tap out is in the fourth column

            // Save Tap in entry to the CheckInOut model
            CheckInOut::create([
                'date' => $date,
                'user_id' => $payroll,
                'time' => $tapIn,
            ]);

            // Save Tap out entry to the CheckInOut model
            CheckInOut::create([
                'date' => $date,
                'user_id' => $payroll,
                'time' => $tapOut,
            ]);
        }

        return redirect()->back()->with('success', 'Data imported successfully.');
    }
}
