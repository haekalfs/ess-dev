<?php
namespace App\Exports;

use App\Models\Timesheet;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class TimesheetExport implements FromView
{
    use Exportable;
    
    public function view(): View
    {
        $month = date('m') - 1;
        $year = date('Y');
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");
        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();
        return view('review.timesheetEmployees', [
            'results' => DB::table('timesheet')
            ->select('ts_user_id as User', 'ts_task as Type', 'ts_location as Area', DB::raw('count(*) as Count'))
            ->groupBy('User', 'Type', 'Area')
            ->orderBy('User')
            ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('ts_status_id', '29')
            ->get()
        ]);
    }
}