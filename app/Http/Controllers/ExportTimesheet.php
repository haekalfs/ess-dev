<?php

namespace App\Http\Controllers;
use App\Exports\TimesheetExport;
use App\Models\Project_assignment_user;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_workflow;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportTimesheet extends Controller
{
    public function export_excel($Month, $Year)
	{
        $templatePath = public_path('template_fm.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $month = date('m') - 1;
        $year = date('Y');
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");
        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $result = DB::table('timesheet_details')
        ->select('timesheet_details.*', 'users.name', 'users_details.employee_id')
        ->join('users', 'timesheet_details.user_timesheet', '=', 'users.id')
        ->join('users_details', 'timesheet_details.user_timesheet', '=', 'users_details.user_id')
        ->where('timesheet_details.ts_status_id', 29)
        ->whereYear('timesheet_details.date_submitted', $Year)
        ->where('timesheet_details.RequestTo', '-')
        ->groupBy('timesheet_details.user_timesheet', 'timesheet_details.ts_task')
        ->get();
            
        $getTotalMandays = DB::table('timesheet_details')
        ->select('user_timesheet', DB::raw('SUM(ts_mandays) as total_mandays'))
        ->where('ts_status_id', 29)
        ->whereYear('date_submitted', $Year)
        ->where('RequestTo', '-')
        ->groupBy('user_timesheet')
        ->get(); 


       // Set up the starting row and column for the data
        $startRow = 8;
        $startCol = 2;

        // Initialize the last printed user name
        $lastUser = '';
        $lastWorkhours = '';
        $lastTotalMandays = 0;
        $firstRow = true; // Flag to check if it's the first row for each user
        
        foreach ($result as $row) {
            // Calculate the total mandays for each user
            if ($row->user_timesheet !== $lastUser) {
                // Find the total mandays for the current user
                $totalMandays = 0;
                foreach ($getTotalMandays as $userMandays) {
                    if ($userMandays->user_timesheet === $row->user_timesheet) {
                        $totalMandays = $userMandays->total_mandays;
                        break;
                    }
                }
                $firstRow = true; // Reset the firstRow flag for a new user
            }
            
            // Print the user name if it is different from the last printed user name
            if ($row->user_timesheet !== $lastUser) {
                $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->name);
                $sheet->setCellValueByColumnAndRow($startCol + 5, $startRow, $totalMandays);
                $sheet->setCellValueByColumnAndRow(1, $startRow, $row->employee_id);
                $lastUser = $row->user_timesheet;
            }
            
            $sheet->setCellValueByColumnAndRow($startCol + 1, $startRow, $row->ts_task);
            $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, $row->ts_location);
            $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow, $row->ts_mandays);
            
            if ($row->workhours !== $lastWorkhours) {
                $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->workhours.' Hours');
                $lastWorkhours = $row->workhours;
            }
            
            
            $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, $row->roleAs);
            
            $startRow++;
            $firstRow = false; // Set the firstRow flag to false after the first row for each user
        }
        



        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(storage_path('app/public/output.xlsx'));
        // Download the file
        $filePath = storage_path('app/public/output.xlsx');

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return response()->download($filePath, 'output.xlsx', $headers);
    }
}
