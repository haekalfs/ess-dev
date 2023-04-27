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
    public function export_excel()
	{
        // $date = date('Y-m');
		// return Excel::download(new TimesheetExport, $date.'TimesheetEmployees_PerdanaConsulting.xlsx');
        // $path = public_path('template_fm.xlsx');
        // $excel = Excel::load($path);

        // $worksheet = $excel->getActiveSheet();
        // $worksheet->each(function($row, $index) {
        //     $row->setCellValue('A' . $index, 'New Value');
        // });

        // $excel->store('xlsx', $path);
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
        $result = DB::table('timesheet')
        ->select('ts_user_id as User', 'ts_task as Type', 'ts_location as Area', 'ts_from_time', DB::raw('count(*) as Count'))
        ->groupBy('User', 'Type', 'Area', 'ts_from_time')
        ->orderBy('User')
        ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->where('ts_status_id', '29')
        ->get();


       // Set up the starting row and column for the data
        $startRow = 8;
        $startCol = 2;

        // Initialize the last printed user name
        $lastUser = '';

        // Loop through the query results and populate the values
        foreach ($result as $row) {
            // Print the user name if it is different from the last printed user name
            if ($row->User !== $lastUser) {
                $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->User);
                $lastUser = $row->User;
            }
            $sheet->setCellValueByColumnAndRow($startCol + 1, $startRow, $row->Type);
            $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, $row->Area);
            $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, $row->Count);
            $sheet->setCellValueByColumnAndRow($startCol + 5, $startRow, $monthName.' - '.$year);
        
            $startRow++;
        
            $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('ts_user_id', $row->User)
                ->where('ts_task', $row->Type)
                ->where('ts_location', $row->Area)
                ->orderBy('ts_date', 'asc')
                ->get();
        
            $total_work_hours = 0;
        
            foreach ($activities as $timesheets) {
                $start_time = strtotime($timesheets->ts_from_time);
                $end_time = strtotime($timesheets->ts_to_time);
                $time_diff_seconds = $end_time - $start_time;
                $time_diff_hours = gmdate('H', $time_diff_seconds);
                $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
            }
        
            $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow - 1, intval($total_work_hours)." Hours");
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
