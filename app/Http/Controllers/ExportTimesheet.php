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
use App\Models\Setting;
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
    public function export_excel($Month, $Year)
	{
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        $checkUserPost = Auth::user()->users_detail->position->id;

        //Tabel Setting Export Role
        $settingExport = Setting::where('id', 1)->first();
        $checkSettingExport = $settingExport->position_id;

        // Compare the hashed passwords
        if (in_array($checkUserPost, [$checkSettingExport])) {
            $templatePath = public_path('template_fm.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getSheet(0);
            // Set up the starting row and column for the data
            $startRowData = 8;
            $startRow = 8;
            $startCol = 2;

            $timesheetSheet = $spreadsheet->getSheet(1);

            // Get the start and end dates for the selected month
            $startDate = Carbon::create($Year, $Month, 1)->startOfMonth();
            $endDate = Carbon::create($Year, $Month)->endOfMonth();

            $getUserIds = Timesheet_detail::where('month_periode', $Year.intval($Month))->where('ts_status_id', 29)->pluck('user_timesheet')->toArray();

            // Get the Timesheet records between the start and end dates
            $empActivities = Timesheet::where('ts_status_id', 29)->whereIn('ts_user_id', $getUserIds)->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_user_id', 'asc')->orderBy('ts_date', 'asc')->get();
            $lastUserData = '';

            foreach($empActivities as $data){
                if ($data->user->name !== $lastUserData) {
                    $timesheetSheet->setCellValueByColumnAndRow($startCol, $startRowData, $data->user->name);
                    $lastUserData = $data->user->name;
                }

                $timesheetSheet->setCellValueByColumnAndRow($startCol + 3, $startRowData, $data->ts_task);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 1, $startRowData, Carbon::createFromFormat('Y-m-d', $data->ts_date)->format('l'));
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 2, $startRowData, Carbon::createFromFormat('Y-m-d', $data->ts_date)->format('d-M-Y'));
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 4, $startRowData, $data->ts_location);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 6, $startRowData, $data->ts_from_time);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 7, $startRowData, $data->ts_to_time);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 5, $startRowData, $data->ts_activity);
                $work_hours = 0;
                $start_time = PHP_INT_MAX;
                $end_time = 0;
                $total_work_hours = 0;

                $current_start_time = strtotime($data->ts_from_time);
                $current_end_time = strtotime($data->ts_to_time);

                if ($current_start_time < $start_time) {
                    $start_time = $current_start_time;
                }

                if ($current_end_time > $end_time) {
                    $end_time = $current_end_time;
                }

                if ($end_time > $start_time) {

                    $time_diff_seconds = $end_time - $start_time;
                    if($time_diff_seconds > 3600){
                        $time_diff_seconds -= 3600;
                    } else {
                        $time_diff_seconds -= $time_diff_seconds;
                    }
                    $time_diff_hours = gmdate('H', $time_diff_seconds);
                    $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                    $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
                    $timesheetSheet->setCellValueByColumnAndRow($startCol + 8, $startRowData, $time_diff_hours.':'.$time_diff_minutes);
                }
                $startRowData++; // Increment row for the next data row

            }


            $month_periode = $Year.intval($Month);
            $result = DB::table('timesheet_details as td')
            ->join('users as u', 'td.user_timesheet', '=', 'u.id')
            ->join('users_details as ud', 'td.user_timesheet', '=', 'ud.user_id')
            ->join(DB::raw("(SELECT user_timesheet, MAX(created_at) AS latest_created_at
                            FROM timesheet_details
                            WHERE ts_status_id = 29 AND month_periode = '{$month_periode}'
                            GROUP BY user_timesheet, ts_task, ts_location) t"), function ($join) {
                $join->on('td.user_timesheet', '=', 't.user_timesheet')
                    ->on('td.created_at', '=', 't.latest_created_at');
            })
            ->where('td.ts_status_id', 29)
            ->whereNotIn('ts_task', ['Other', 'Sick'])
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
                        ->groupBy('user_timesheet', 'ts_mandays');
                })
                ->whereNotIn('ts_task', ['Other', 'Sick'])
                ->where('ts_status_id', 29)
                ->where('timesheet_details.month_periode', $Year.intval($Month))
                ->groupBy('user_timesheet')
                ->get();

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
            $lastWorkhours = '';
            $firstRow = true; // Flag to check if it's the first row for each user
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
                    $total = [];
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
                    $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->workhours);
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

            // create new sheet



            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('app/public/output.xlsx'));
            // Download the file
            $filePath = storage_path('app/public/output.xlsx');

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            // Create a DateTime object using the year and month value
            $dateTime = DateTime::createFromFormat('m', $Month);

            // Get the month name
            $monthName = $dateTime->format('F');
            return response()->download($filePath, "$monthName-$Year.xlsx", $headers);
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

    public function export_excel_allowance($Month, $Year)
	{
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        $checkUserPost = Auth::user()->users_detail->position->id;

        //Tabel Setting Export Role
        $settingExport = Setting::where('id', 1)->first();
        $checkSettingExport = $settingExport->position_id;

        // Compare the hashed passwords
        if (in_array($checkUserPost, [$checkSettingExport])) {
            $templatePath = public_path('template_fm_2024.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getSheet(0);
            // Set up the starting row and column for the data
            $startRowData = 8;
            $startRow = 8;
            $startCol = 2;

            $timesheetSheet = $spreadsheet->getSheet(1);

            // Get the start and end dates for the selected month
            $startDate = Carbon::create($Year, $Month, 1)->startOfMonth();
            $endDate = Carbon::create($Year, $Month)->endOfMonth();

            $getUserIds = Timesheet_detail::where('month_periode', $Year.intval($Month))->where('ts_status_id', 29)->pluck('user_timesheet')->toArray();

            // Get the Timesheet records between the start and end dates
            $empActivities = Timesheet::where('ts_status_id', 29)->whereIn('ts_user_id', $getUserIds)->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_user_id', 'asc')->orderBy('ts_date', 'asc')->get();
            $lastUserData = '';

            foreach($empActivities as $data){
                if ($data->user->name !== $lastUserData) {
                    $timesheetSheet->setCellValueByColumnAndRow($startCol, $startRowData, $data->user->name);
                    $lastUserData = $data->user->name;
                }

                $timesheetSheet->setCellValueByColumnAndRow($startCol + 3, $startRowData, $data->ts_task);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 1, $startRowData, Carbon::createFromFormat('Y-m-d', $data->ts_date)->format('l'));
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 2, $startRowData, Carbon::createFromFormat('Y-m-d', $data->ts_date)->format('d-M-Y'));
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 4, $startRowData, $data->ts_location);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 6, $startRowData, $data->ts_from_time);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 7, $startRowData, $data->ts_to_time);
                $timesheetSheet->setCellValueByColumnAndRow($startCol + 5, $startRowData, $data->ts_activity);
                $work_hours = 0;
                $start_time = PHP_INT_MAX;
                $end_time = 0;
                $total_work_hours = 0;

                $current_start_time = strtotime($data->ts_from_time);
                $current_end_time = strtotime($data->ts_to_time);

                if ($current_start_time < $start_time) {
                    $start_time = $current_start_time;
                }

                if ($current_end_time > $end_time) {
                    $end_time = $current_end_time;
                }

                if ($end_time > $start_time) {

                    $time_diff_seconds = $end_time - $start_time;
                    if($time_diff_seconds > 1800){
                        $time_diff_seconds -= 1800;
                    } else {
                        $time_diff_seconds -= $time_diff_seconds;
                    }
                    $time_diff_hours = gmdate('H', $time_diff_seconds);
                    $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                    $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
                    $timesheetSheet->setCellValueByColumnAndRow($startCol + 8, $startRowData, $time_diff_hours.':'.$time_diff_minutes);
                }
                $startRowData++; // Increment row for the next data row

            }


            $month_periode = $Year.intval($Month);
            $result = DB::table('timesheet_details as td')
            ->join('users as u', 'td.user_timesheet', '=', 'u.id')
            ->join('users_details as ud', 'td.user_timesheet', '=', 'ud.user_id')
            ->join(DB::raw("(SELECT user_timesheet, MAX(created_at) AS latest_created_at
                            FROM timesheet_details
                            WHERE ts_status_id = 29 AND month_periode = '{$month_periode}'
                            GROUP BY user_timesheet, ts_task, ts_location) t"), function ($join) {
                $join->on('td.user_timesheet', '=', 't.user_timesheet')
                    ->on('td.created_at', '=', 't.latest_created_at');
            })
            ->where('td.ts_status_id', 29)
            ->whereNotIn('ts_task', ['Other', 'Sick'])
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
                        ->groupBy('user_timesheet', 'ts_mandays');
                })
                ->whereNotIn('ts_task', ['Other', 'Sick'])
                ->where('ts_status_id', 29)
                ->where('timesheet_details.month_periode', $Year.intval($Month))
                ->groupBy('user_timesheet')
                ->get();

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
            $lastWorkhours = '';
            $firstRow = true; // Flag to check if it's the first row for each user
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

                $fare = Project_location::where('location_code', $row->ts_location)->value('fare');
                $sheet->setCellValueByColumnAndRow($startCol + 5, $startRow, $fare);

                // Print the user name if it is different from the last printed user name
                if ($row->user_timesheet !== $lastUser) {
                    $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->name);
                    $sheet->setCellValueByColumnAndRow($startCol + 9, $startRow, $totalMandays);
                    $sheet->setCellValueByColumnAndRow(1, $startRow, $row->employee_id);

                    $checkUser = User::find($row->user_timesheet);
                    $checkDepartment = $checkUser->users_detail->department->id;

                    $countTotalRowsEachUser = Timesheet_detail::where('month_periode', $Year.intval($Month))
                    ->where('ts_status_id', 29);
                    $countUserRows = $countTotalRowsEachUser->where('user_timesheet', $row->user_timesheet)->count();

                    // Reset the total allowances for the new user
                    $total = [];
                    $lastUser = $row->user_timesheet;
                }


                if($row->ts_task_id == "HO"){
                    $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->total_allowance);
                } else {
                    $sheet->setCellValueByColumnAndRow($startCol + 7, $startRow, $row->total_allowance);
                }

                $total[] = $row->total_allowance;
                if (!$firstRow) {
                    if ($countUserRows === 1) {
                        $sheet->setCellValueByColumnAndRow($startCol + 10, $startRow, array_sum($total));
                    }
                }

                $sheet->setCellValueByColumnAndRow($startCol + 1, $startRow, $row->ts_task);
                $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, $row->ts_location);
                $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow, $row->ts_mandays);


                $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, $row->roleAs);

                $startRow++;
                $firstRow = false; // Set the firstRow flag to false after the first row for each user

                if ($index === count($result) - 1 || $row->user_timesheet !== $result[$index + 1]->user_timesheet) {
                    // Print the totalBanget value in the last row for each user
                    $sheet->setCellValueByColumnAndRow($startCol + 10, $startRow - 1, array_sum($total));
                } // Set the firstRow flag to false after the first row for each user
            }

            // create new sheet



            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('app/public/output.xlsx'));
            // Download the file
            $filePath = storage_path('app/public/output.xlsx');

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            // Create a DateTime object using the year and month value
            $dateTime = DateTime::createFromFormat('m', $Month);

            // Get the month name
            $monthName = $dateTime->format('F');
            return response()->download($filePath, "$monthName-$Year.xlsx", $headers);
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
