<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function index()
    {
        return view('timereport.timesheet');
    }

    public function timesheet_entry($id)
    {
        $startOfMonth = Carbon::now()->setMonth($id)->startOfMonth();
        $endOfMonth = Carbon::now()->setMonth($id)->endOfMonth();
        $firstDayOfWeek = $startOfMonth->startOfWeek();


        $timesheets = Timesheet::whereBetween('ts_date', [$startOfMonth, $endOfMonth])->get();
        
        $dates = [];
        for ($date = $firstDayOfWeek; $date <= $endOfMonth; $date->addDay()) {
            $dates[] = $date->copy();
        }
        return view('timereport.timesheet_entry', ['entry' => $id, 'dates' => $dates, 'savedActivities' => $timesheets]);
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
        return redirect()->back();
    }
}
