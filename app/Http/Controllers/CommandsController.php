<?php

namespace App\Http\Controllers;

use App\Models\Checkinout;
use App\Models\Commands;
use Illuminate\Http\Request;
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
}
