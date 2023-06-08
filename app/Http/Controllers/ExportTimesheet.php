<?php

namespace App\Http\Controllers;
use App\Exports\TimesheetExport;
use App\Models\Additional_fare;
use App\Models\Financial_password;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Project_role;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_detail;
use App\Models\Timesheet_workflow;
use App\Models\User;
use App\Models\Users_detail;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportTimesheet extends Controller
{
    public function export_excel(Request $request, $Month, $Year)
	{
        // Retrieve the hashed password from the query parameters
        $password = $request->query('password');

        $getPassword = Financial_password::find(1);
        $storedHashedPassword = $getPassword->password;

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($Year, $Month, 1)->startOfMonth();
        $endDate = Carbon::create($Year, $Month)->endOfMonth();

        // Compare the hashed passwords
        if (Hash::check($password, $storedHashedPassword)) {
            $templatePath = public_path('template_fm.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
    
            $month_periode = $Year.intval($Month);
            $result = DB::table('timesheet_details as td')
            ->join('users as u', 'td.user_timesheet', '=', 'u.id')
            ->join('users_details as ud', 'td.user_timesheet', '=', 'ud.user_id')
            ->join(DB::raw("(SELECT user_timesheet, MAX(created_at) AS latest_created_at
                            FROM timesheet_details
                            WHERE ts_status_id = 29 AND month_periode = '{$month_periode}'
                            GROUP BY user_timesheet) t"), function ($join) {
                $join->on('td.user_timesheet', '=', 't.user_timesheet')
                    ->on('td.created_at', '=', 't.latest_created_at');
            })
            ->where('td.ts_status_id', 29)
            ->groupBy('td.user_timesheet', 'td.ts_task', 'td.ts_location')
            ->select('td.*', 'u.name', 'ud.employee_id')
            ->get();
            
            $getTotalMandays = DB::table('timesheet_details')
                ->select('user_timesheet', DB::raw('SUM(ts_mandays) as total_mandays'))
                ->whereIn('created_at', function ($query) use ($Year, $Month) {
                    $query->select(DB::raw('MAX(created_at)'))
                        ->from('timesheet_details')
                        ->whereColumn('timesheet_details.user_timesheet', '=', 'user_timesheet')
                        ->where('ts_status_id', 29)
                        ->where('timesheet_details.month_periode', $Year.intval($Month))
                        ->groupBy('user_timesheet');
                })
                ->where('ts_status_id', 29)
                ->where('timesheet_details.month_periode', $Year.intval($Month))
                ->groupBy('user_timesheet')
                ->get();

    
           // Set up the starting row and column for the data
            $startRow = 8;
            $startCol = 2;

            // Set the default time zone to Jakarta
            date_default_timezone_set("Asia/Jakarta");

            // Create a DateTime object for the first day of the selected month
            $dateToCount = new DateTime("$Year-$Month-01");

            // Get the last day of the selected month
            $lastDay = $dateToCount->format('t');

            // Initialize a counter for weekdays
            $totalWeekdays = 0;

            // Loop through each day of the month and count weekdays
            for ($day = 1; $day <= $lastDay; $day++) {
                // Set the day of the month
                $dateToCount->setDate($Year, $Month, $day);
                
                // Check if the day is a weekday (Monday to Friday)
                if ($dateToCount->format('N') <= 5) {
                    $totalWeekdays++;
                }
            }

            $totalHours = $totalWeekdays * 8; 
    
            // Initialize the last printed user name
            $lastUser = '';
            $lastAllowance = 0;
            $lastWorkhours = '';
            $totalAllowances = 0;
            $totalIncentive = 0;
            $countIncentive = [];
            $lastTotalMandays = 0;
            $firstRow = true; // Flag to check if it's the first row for each user
            $incentiveArray = [];
            $allowanceArray = [];
            $totalSum = 0;
            $total = [];
    
            foreach ($result as $row) {
                $mandays = $row->ts_mandays;
                $totalSum += $mandays;
            }
    
            foreach ($result as $index => $row) {
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
    
                    $checkUser = User::find($row->user_timesheet);
                    $checkDepartment = $checkUser->users_detail->department->id;
                    
                    $countTotalRowsEachUser = Timesheet_detail::where('month_periode', $Year.intval($Month))
                    ->where('ts_status_id', 29);
                    $countUserRows = $countTotalRowsEachUser->where('user_timesheet', $row->user_timesheet)->count();

                    // Reset the total allowances for the new user
                    $allowanceArray = [];
                    $incentiveArray = [];
                    $totalAllowances = 0;
                    $totalIncentive = 0;
                    $total = [];
                    $countIncentive = [];
                    $lastUser = $row->user_timesheet;
                }
                
    
                if($row->ts_task_id == "HO"){
                    $sheet->setCellValueByColumnAndRow($startCol + 7, $startRow, $row->total_allowance);
                } else {
                    $sheet->setCellValueByColumnAndRow($startCol + 8, $startRow, $row->total_allowance);
                }
                if ($checkDepartment == 2) {
                    $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $row->total_incentive);
                } elseif($checkDepartment == 4) {
                    $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $row->total_incentive);
                } elseif($checkDepartment == 3) {
                    $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $row->total_incentive);
                } elseif($checkDepartment == 1) {
                    $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $row->total_incentive);
                }

                $total[] = $row->total_incentive;
                $total[] = $row->total_allowance;
                if (!$firstRow) {
                    if ($countUserRows === 1) {
                        $sheet->setCellValueByColumnAndRow($startCol + 11, $startRow, array_sum($total));
                    }
                }

                $sheet->setCellValueByColumnAndRow($startCol + 1, $startRow, $row->ts_task);
                $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, $row->ts_location);
                $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow, $row->ts_mandays);
                
                if ($row->workhours !== $lastWorkhours) {
                    $percentage = (intval($row->workhours) / $totalHours) * 100;
                    $percentage = intval($percentage);
                    $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->workhours.' Hours '."($percentage%)");
                    $lastWorkhours = $row->workhours;
                }
    
                
                $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, $row->roleAs);

                $startRow++;
                $firstRow = false; // Set the firstRow flag to false after the first row for each user
            
                if ($index === count($result) - 1 || $row->user_timesheet !== $result[$index + 1]->user_timesheet) {
                    // Print the totalBanget value in the last row for each user
                    $sheet->setCellValueByColumnAndRow($startCol + 11, $startRow - 1, array_sum($total));
                } // Set the firstRow flag to false after the first row for each user
            }
    
    
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('app/public/output.xlsx'));
            // Download the file
            $filePath = storage_path('app/public/output.xlsx');
    
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
    
            return response()->download($filePath, 'output.xlsx', $headers);
        } else {
            abort(403, 'Unauthorized');
        }        
        ///////////////////////////////////
        // $fare = Project_location::where('location_code', $row->ts_location)->pluck('fare')->first();
        // $countAllowances = $row->ts_mandays * $fare;

        // $checkUser = User::find($row->user_timesheet);
        // $checkDepartment = $checkUser->users_detail->department->id;
        // if ($checkDepartment == 2) {
        //     $sheet->setCellValueByColumnAndRow($startCol + 8, $startRow, $countAllowances);
        // } else {
        //     $sheet->setCellValueByColumnAndRow($startCol + 7,   $startRow, $countAllowances);
        // }
        
        // $countIncentive[] = $row->incentive;
        // $totalAllowances += $countAllowances;
        // $totalIncentive = array_sum($countIncentive);

        // if ($row->user_timesheet !== $lastUser) {
        //     $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $row->incentive);
        // }///////////////////////////
    }
}
