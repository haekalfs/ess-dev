<?php

namespace App\Http\Controllers;

use App\Models\User_access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Mail\EssMailer;
use App\Mail\TimesheetReminderEmployee;
use App\Models\Approval_status;
use App\Models\Company_project;
use App\Models\Cutoffdate;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Timesheet;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Crypt;
use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{
    public function usr_acc($usr_acc)
    {
        $allowedRole = User_access::where('page_id', $usr_acc)
            ->join('roles', 'user_access.role_id', '=', 'roles.id')
            ->pluck('roles.role')
            ->toArray();
            
        $allowedRolesInSession = session('allowed_roles');
        
        $intersection = array_intersect($allowedRole, $allowedRolesInSession);
        
        if (empty($intersection)) {
            abort(403, 'Unauthorized');
        }
        
        return $intersection;
    }

    public function submit_timesheet($year, $month)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        $checkisSubmitted = DB::table('timesheet_details')
        ->select('*')
        ->whereYear('date_submitted', $year)
        ->where('user_timesheet', Auth::user()->id)
        ->whereNotIn('ts_status_id', [10,404])
        ->where('month_periode', $year.intval($month))
        ->whereNotExists(function ($query) use ($year, $month) {
            $query->select(DB::raw(1))
                ->from('timesheet_details')
                ->where('month_periode', $year.intval($month))
                ->where('ts_status_id', [404]);
        })
        ->groupBy('user_timesheet', 'month_periode')
        ->get();

        if (!$checkisSubmitted->isEmpty()) {
            Session::flash('failed', 'Your Timesheet has already been submitted!');
            return redirect()->back();
        }

        // Get the current date
        $currentDate = Carbon::now();

        $dateCut = Cutoffdate::first();
        // Get the cutoff date for submitting timesheets (7th of the next month)
        $cutoffDate = Carbon::create($year, $month)->addMonths(1)->startOfMonth()->addDays(($dateCut->date - 1));
        $cutoffDate = Carbon::createFromFormat('Y-m-d', "2023-07-$dateCut->date");

        // Check if the current date is on or after the cutoff date
        if ($currentDate->gte($cutoffDate)) {
            Session::flash('failed', 'Timesheet submission is closed for this month.');
            return redirect()->back();
        }

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $tsOfTheMonth = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', Auth::user()->id)->get();

        if ($tsOfTheMonth->isEmpty()) {
            Session::flash('failed', "You have to fill your timesheet first!");
            return redirect(url()->previous());
        }
        $total_work_hours = 0;
        foreach($tsOfTheMonth as $sum){
            $start_time = strtotime($sum->ts_from_time);
            $end_time = strtotime($sum->ts_to_time);
            $time_diff_seconds = $end_time - $start_time;
            $time_diff_hours = gmdate('H', $time_diff_seconds);
            $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
            $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60)); echo $time_diff_hours.':'.$time_diff_minutes;
        }

        $countRows = DB::table('timesheet')
        ->selectRaw('ts_task, ts_location, ts_user_id, MAX(ts_task_id) AS ts_task_id, COUNT(*) AS total_rows')
        ->whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
        ->where('ts_user_id', Auth::user()->id)
        ->groupBy('ts_task', 'ts_location', 'ts_user_id')
        ->get();

        // echo "Total days: " . $totalDays;
        $checkUserDept = Auth::user()->users_detail->department->id;

        $approvalGA = Timesheet_approver::whereIn('id', [10, 45])->get();
        $approvalFinance = Timesheet_approver::whereIn('id', [15, 45])->get();
        $approvalSales = Timesheet_approver::whereIn('id', [50, 55])->get();
        $approvalHCM = Timesheet_approver::whereIn('id', [10, 60])->get();
        $approvalService = Timesheet_approver::whereIn('id', [20, 40])->get();

        $empApproval = [];
        // var_dump($countRows);
        foreach($countRows as $row) {
            $test = Project_assignment::where('id', $row->ts_task_id)->pluck('company_project_id')->first();
            $test2 = Project_assignment_user::where('role', "PM")->where('company_project_id', $test)->pluck('user_id')->first();
            $pa = Project_assignment_user::where('role', "PA")->where('company_project_id', $test)->pluck('user_id')->first();
            $checkRole = Project_assignment_user::where('user_id', Auth::user()->id)->where('project_assignment_id', $row->ts_task_id)->pluck('role')->first();

            if(!$test2->isEmpty()){
                $newArrayPM = [
                    'name' => $test2,
                    'task' => $row->ts_task_id,
                    'location' => $row->ts_location,
                    'mandays' => $$row->total_rows,
                    'role' => $checkRole,
                    'task_id' => $row->ts_task_id,
                ];
                $empApproval[] = $newArrayPM;
            }
            if(!$pa->isEmpty()){
                $newArrayPA = [
                    'name' => $pa,
                    'task' => $row->ts_task_id,
                    'location' => $row->ts_location,
                    'mandays' => $$row->total_rows,
                    'role' => $checkRole,
                    'task_id' => $row->ts_task_id,
                ];
                $empApproval[] = $newArrayPA;
            }

            switch ($row->ts_task) {
                case "HO":
                    switch($checkUserDept){
                        case 4:
                            if(in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray())){
                                foreach($approvalFinance as $approverFinance){
                                    $newArrayFm = [
                                        'name' => $approverFinance->approver,
                                        'task' => $row->ts_task_id,
                                        'location' => $row->ts_location,
                                        'mandays' => $row->total_rows,
                                        'role' => $checkRole,
                                        'task_id' => $row->ts_task_id,
                                    ];
                                    $empApproval[] = $newArrayFm;
                                }
                            } else {
                                foreach($approvalGA as $approverGa){
                                    $newArrayHO = [
                                        'name' => $approverGa->approver,
                                        'task' => $row->ts_task_id,
                                        'location' => $row->ts_location,
                                        'mandays' => $row->total_rows,
                                        'role' => $checkRole,
                                        'task_id' => $row->ts_task_id,
                                    ];
                                    $empApproval[] = $newArrayHO;
                                }
                            }
                        break;
                        case 2:
                            foreach($approvalService as $approverService){
                                $newArrayService = [
                                    'name' => $approverService->approver,
                                    'task' => $row->ts_task_id,
                                    'location' => $row->ts_location,
                                    'mandays' => $row->total_rows,
                                    'role' => $checkRole,
                                    'task_id' => $row->ts_task_id,
                                ];
                                $empApproval[] = $newArrayService;
                            }
                        break;
                    }
                case "Sick":
                case "Standby":
                case "Other":
                    foreach($approvalHCM as $approverHCM){
                        $newArrayHO = [
                            'name' => $approverHCM->approver,
                            'task' => $row->ts_task_id,
                            'location' => $row->ts_location,
                            'mandays' => $row->total_rows,
                            'role' => $checkRole,
                            'task_id' => $row->ts_task_id,
                        ];
                        $empApproval[] = $newArrayHO;
                    }
                break;
                case "Training":
                case "Trainer":
                case "Presales":
                    foreach($approvalSales as $approverSales){
                        $newArrayPresales = [
                            'name' => $approverSales->approver,
                            'task' => $row->ts_task_id,
                            'location' => $row->ts_location,
                            'mandays' => $row->total_rows,
                            'role' => $checkRole,
                            'task_id' => $row->ts_task_id,
                        ];
                        $empApproval[] = $newArrayPresales;
                    }
                break;
                default:
                break;
            }
        }

        foreach ($empApproval as $test) {
            Timesheet_detail::updateOrCreate([
                'user_id' => Auth::user()->id,
                'workhours' => intval($total_work_hours),
                'month_periode' => $year.$month,
                'RequestTo' => $test['name'],
                'ts_task' => $test['task'],
                'ts_location' => $test['location']
            ], [
                'ts_mandays' => $test['mandays'],
                'activity' => 'Waiting for Approval',
                'roleAs' => $checkRole,
                'date_submitted' => date('Y-m-d'),
                'ts_status_id' => 20,
                'note' => '',
                'ts_task_id' => $test['task_id'],
                'user_timesheet' => Auth::user()->id
            ]);
        }
        // Update Timesheet records between the start and end dates
        Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->where('ts_user_id', Auth::user()->id)->orderBy('created_at', 'desc')->update(['ts_status_id' => '20']);
      
        $ts_date_desc = date("F", mktime(0, 0, 0, $month, 1)).' '.$year;
        // return response()->json($activities);
        Session::flash('success',"Your Timereport $ts_date_desc has been submitted!");
        return redirect()->back();
    }
}
