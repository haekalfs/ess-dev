<?php

namespace App\Http\Controllers;

use App\Exports\TimesheetExport;
use App\Mail\ApprovalTimesheet;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Timesheet;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Usr_role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApprovalController extends Controller
{
    public function index()
	{
        $accessController = new AccessController();
        $result = $accessController->usr_acc(202);

        $tsCount = Timesheet_detail::whereIn('ts_status_id', ['20', '30', '40'])
             ->where(function($query) {
                 $query->where('RequestTo', Auth::user()->id)
                       ->orWhere('RequestTo', 'hr')
                       ->orWhere('RequestTo', 'pa')
                       ->orWhere('RequestTo', 'fin_ga_dir')
                       ->orWhere('RequestTo', 'service_dir')
                       ->orWhere('RequestTo', 'fm')
                       ->orWhereNull('RequestTo');
             })
             ->count();

        $pCount = Project_assignment::where('approval_status', 40)->count();

		return view('approval.main', ['tsCount' => $tsCount, 'pCount' => $pCount]);
	}

    public function timesheet_approval()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $userRoles = Auth::user()->role_id()->pluck('role_name')->toArray();

        switch (true) {
            case in_array('hr', $userRoles):
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 20)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', 'hr')
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'hr';
                if ($approvals->isEmpty()) {
                    $button = 'pm';
                    $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 20)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', Auth::user()->id)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                }
                break;
            case in_array('fm', $userRoles):
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 20)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', 'fm')
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'fm';
                if ($approvals->isEmpty()) {
                    $button = 'pm';
                    $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 20)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', Auth::user()->id)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                }
                break;
            case in_array('pa', $userRoles):
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 30)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', 'pa')
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'pa';
                if ($approvals->isEmpty()) {
                    $button = 'pm';
                    $approvals = DB::table('timesheet_details')
                        ->select('*')
                        ->where('ts_status_id', 20)
                        ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                    break;
                }
                break;
            case in_array('service_dir', $userRoles):
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 40)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', 'service_dir')
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'service_dir';
                if ($approvals->isEmpty()) {
                    $button = 'pm';
                    $approvals = DB::table('timesheet_details')
                        ->select('*')
                        ->where('ts_status_id', 20)
                        ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                    break;
                }
                break;
            case in_array('fin_ga_dir', $userRoles):
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 30)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', 'fin_ga_dir')
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'fin_ga_dir';
                if ($approvals->isEmpty()) {
                    $button = 'pm';
                    $approvals = DB::table('timesheet_details')
                        ->select('*')
                        ->where('ts_status_id', 20)
                        ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->groupBy('user_timesheet', 'month_periode')
                        ->get();
                    break;
                }
                break;
            default:
                $approvals = DB::table('timesheet_details')
                    ->select('*')
                    ->where('ts_status_id', 20)
                    ->whereYear('date_submitted', $currentYear)
                    ->where('RequestTo', Auth::user()->id)
                    ->groupBy('user_timesheet', 'month_periode')
                    ->get();
                $button = 'pm';
                break;
        }
        return view('approval.timesheet_approval', ['approvals' => $approvals, 'button' => $button]);
    }

    public function approve_pm($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)->where('ts_user_id', $user_timesheet)->update(['ts_status_id' => '30']);

        $countRows = Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();
        // Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->update(['ts_status_id' => '30', 'activity' => 'Approved']);
        // var_dump($countRows);
        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '30', 'RequestTo' => "pa", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', Auth::user()->id)
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '30']);
        }

        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_pa($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '40']);

        $countRows = Timesheet_detail::where('RequestTo', "PA")->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '40', 'RequestTo' => "service_dir", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', "PA")
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '40']);
        }

        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_fm_testing($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        $activities = Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '30']);

        Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id,'activity' => 'Approved', 'month_periode' => $year.$month, 'RequestTo' => 'dir_fin_ga'],['date_approved' => date('Y-m-d'), 'ts_status_id' => '30', 'note' => '', 'user_timesheet' => $user_timesheet]);
        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_hr($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '30']);

        $countRows = Timesheet_detail::where('RequestTo', 'hr')->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();
        // Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->update(['ts_status_id' => '30', 'activity' => 'Approved']);
        // var_dump($countRows);
        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '30', 'RequestTo' => "fin_ga_dir", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', 'hr')
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '30']);
        }

        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_fm($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '30']);

        $countRows = Timesheet_detail::where('RequestTo', 'fm')->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();
        // Timesheet_detail::where('RequestTo', Auth::user()->id)->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->update(['ts_status_id' => '30', 'activity' => 'Approved']);
        // var_dump($countRows);
        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '30', 'RequestTo' => "fin_ga_dir", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', 'fm')
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '30']);
        }

        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_service_dir($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '29']);

        $countRows = Timesheet_detail::where('RequestTo', "service_dir")->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '29', 'RequestTo' => "-", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', "service_dir")
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '29']);
        }
        
        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function approve_fin_ga_dir($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        $activities = Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '29']);

        $countRows = Timesheet_detail::where('RequestTo', "fin_ga_dir")->where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->get();

        foreach($countRows as $row) {
            Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Approved', 'month_periode' => $row->month_periode, 'ts_status_id' => '29', 'RequestTo' => "-", 'ts_task' => $row->ts_task, 'ts_location' => $row->ts_location, 'user_timesheet' => $row->user_timesheet],
            ['ts_mandays' => $row->ts_mandays, 'roleAs' => $row->roleAs, 'date_submitted' => date('Y-m-d'), 'workhours' => $row->workhours, 'note' => '', 'ts_task_id' => $row->ts_task_id]);
        }
        foreach($countRows as $row) { ///test buat dihapus nnti karna double loops
            Timesheet_detail::where('month_periode', $year.$month)
            ->where('user_timesheet', $user_timesheet)
            ->where('RequestTo', "fin_ga_dir")
            ->where('ts_task_id', $row->ts_task_id)
            ->update(['ts_status_id' => '29']);
        }
        
        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('success',"You approved $user_timesheet $yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function reject_director($user_timesheet,$year,$month)
    {
        date_default_timezone_set("Asia/Jakarta");
        Timesheet::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '404']);

        // Timesheet_detail::where('user_timesheet', $user_timesheet)->where('month_periode', $year.$month)->delete();
        // Timesheet_detail::updateOrCreate(['user_id' => Auth::user()->id, 'activity' => 'Saved', 'month_periode' => date("Yn", strtotime($request->clickedDate))],['date_submitted' => date('Y-m-d'),'ts_status_id' => '10', 'note' => '', 'user_timesheet' => Auth::user()->id]);

        Timesheet_detail::whereYear('ts_date', $year)->whereMonth('ts_date',$month)
        ->where('ts_user_id', $user_timesheet)
        ->update(['ts_status_id' => '29']);

        $yearA = substr($year, 4, 2);
        $monthA = substr($month, 0, 4);
        Session::flash('failed',"You rejected $user_timesheet #$yearA - $monthA timereport!");
        return redirect()->back();
    }

    public function review(Request $request)
	{
		$Month = date('m');
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::all();
        
        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        // var_dump($Year.intval($Month));
        $approvals = Timesheet_detail::where('ts_status_id', 29)
            ->where('RequestTo', '-')
            ->groupBy('user_timesheet', 'ts_task');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Year.intval($Month));
        } else {
            $approvals->whereYear('date_submitted', $Year);
            $approvals->where('month_periode', $Month);
        }

        $approvals = $approvals->get();
		return view('review.finance', compact('approvals', 'yearsBefore', 'Month', 'Year','employees'));
	}

    public function ts_preview($id, $year, $month)
	{
		// $year = Crypt::decrypt($year);
        // $month = Crypt::decrypt($month);
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get the Timesheet records between the start and end dates
        $activities = Timesheet::whereBetween('ts_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->orderBy('ts_date', 'asc')->where('ts_user_id', $id)->get();
        
        $user_info = User::find($id);

        $workflow = Timesheet_detail::where('user_id', $id)->where('month_periode', $year.$month)->get();

        $info = [];
        $lastUpdate = DB::table('timesheet')
                ->whereMonth('ts_date', $month)
                ->whereYear('ts_date', $year)
                ->orderBy('updated_at', 'desc')
                ->where('ts_user_id', $id)
                ->first();
        if ($lastUpdate) {
            if($lastUpdate->ts_status_id == '10'){
                $status = "Saved";
            }elseif($lastUpdate->ts_status_id == '20'){
                $status = "Submitted";
            }elseif($lastUpdate->ts_status_id == '29'){
                $status = "Approved";
            }elseif($lastUpdate->ts_status_id == '404'){
                $status = "Rejected";
            }else{
                $status = "404";
            }
            $lastUpdatedAt = $lastUpdate->updated_at;
        } else {
            $status = 'None';
            $lastUpdatedAt = 'None';
        }
        $info[] = compact('status', 'lastUpdatedAt');
        // return response()->json($activities);
        return view('approval.ts_preview', compact('year', 'month','info', 'id'), ['timesheet' => $activities, 'user_info' => $user_info, 'workflow' => $workflow]);
	}
}
