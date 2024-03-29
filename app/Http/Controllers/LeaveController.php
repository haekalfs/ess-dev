<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyCancelLeaveRequest;
use App\Jobs\NotifyLeaveApproval;
use App\Mail\ApprovalLeave;
use App\Models\Approval_status;
use App\Models\Emp_leave_quota;
use App\Models\Leave;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Leave_request_history;
use App\Models\Notification_alert;
use App\Models\Position;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Surat_penugasan;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function history($yearSelected = null)
	{
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if($yearSelected){
            $currentYear = $yearSelected;
        }

        $leaveType = Leave::all();

        if (!$leaveType->isEmpty()) {
            $leaveType = $leaveType->reject(function ($leaveType) {
                return $leaveType->id === 100; // Replace 'WFH' with the specific value you want to remove
            });
        }

        $leaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->whereIn('leave_id', [10, 20])->orderBy('expiration', 'asc')->get();

        $leaveQuotaUsage = Leave_request_history::where('req_by', Auth::user()->id)->get();

        $weekendReplacementQuota = Surat_penugasan::where('isWeekend', TRUE)->where('user_id', Auth::id())->orderBy('ts_date', 'asc')->get();
        $empLeaveQuotaWeekendReplacement = Surat_penugasan::where('isWeekend', TRUE)->where('user_id', Auth::id())->where('isTaken', FALSE)->count();

        $empLeaveQuotaAnnualSum = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', 10)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))->where('user_id', Auth::user()->id)->where('leave_id', 20)->pluck('quota_left')->first();
        $totalQuota = $empLeaveQuotaAnnualSum + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }
        $leaveRequests = Leave_request::where('req_by', Auth::user()->id)->get();
        foreach ($leaveRequests as $lr) {
            $dates = explode(',', $lr->leave_dates);
            $currentMonth = null;
            $dateGroups = [];
            $group = [];

            foreach ($dates as $date) {
                $formattedDate = date('d', strtotime($date));
                $monthYear = date('F Y', strtotime($date));

                if ($currentMonth !== $monthYear) {
                    if (!empty($group)) {
                        $dateGroups[] = $group;
                        $group = [];
                    }
                    $group['monthYear'] = $monthYear;
                    $group['dates'] = [$formattedDate];
                    $currentMonth = $monthYear;
                } else {
                    $group['dates'][] = $formattedDate;
                }
            }

            if (!empty($group)) {
                $dateGroups[] = $group;
            }

            $lr->dateGroups = $dateGroups;

            $approved = false;

            foreach ($lr->leave_request_approval as $stat) {
                if ($stat->status == 29) {
                    $lr->approvalStatus = "<span class='m-0 text-primary'><i class='fas fa-check-circle' style='color: #005eff;'></i> Approved</span>";
                    $approved = true;
                    break;
                } elseif($stat->status == 404) {
                    $lr->approvalStatus = "<span class='m-0 text-danger'><i class='fas fa-times-circle' style='color: #ff0000;'></i> Rejected</span>";
                    $approved = true;
                    break;
                }
            }

            if (!$approved) {
                $lr->approvalStatus = "<span class='m-0 text-secondary'><i class='fas fa-spinner fa-spin'></i> Waiting for Approval</span>";
            }
        }
        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::whereIn('project_assignment_id', $findAssignment)->where('role', 'PM')->get();

        return view('leave.history', compact('yearsBefore', 'leaveType', 'usersWithPMRole', 'weekendReplacementQuota', 'empLeaveQuotaWeekendReplacement', 'leaveQuotaUsage', 'leaveQuotaAnnual','leaveRequests', 'empLeaveQuotaAnnualSum', 'empLeaveQuotaFiveYearTerm', 'totalQuota'));
	}

    public function leave_request_entry(Request $request)
    {
        $this->validate($request,[
            'datepickLeave' => 'required',
    		'quota_used' => 'required',
            'cp_number' => 'sometimes',
            'total_days' => 'required',
            'reason' => 'required'
    	]);
        $totalDays = $request->total_days;
        $quotaUsed = $request->quota_used;

        $checkQuotaLeft = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', $quotaUsed)
        ->where('expiration', '>=', date('Y-m-d'))
        ->sum('quota_left');

        $UserDept = Auth::user()->users_detail->department;

        //for validation
        $isManager = Timesheet_approver::where('group_id', 2)->pluck('approver')->toArray();

        $checkUserPost = Auth::user()->users_detail->position->id;
        $getHighPosition = Position::where('position_level', 1)->pluck('id')->toArray();

        $statusId = in_array($checkUserPost, $getHighPosition) ? 29 : 15;

        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->where('periode_end', '>=', date('Y-m-d'))->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::whereIn('project_assignment_id', $findAssignment)->where('role', 'PM')->get();
        // Retrieve the relevant Emp_leave_quota rows ordered by active_periode in ascending order
        $checkQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', $request->quota_used)
        ->where('expiration', '>=', date('Y-m-d'))
        ->orderBy('expiration', 'asc')
        ->get();

        $countQuota = $request->total_days;

        $uniqueId = Str::uuid()->toString();
        $userToApprove = [];

        $checkIfOnce = Emp_leave_quota::where('leave_id', $quotaUsed)->where('user_id', Auth::user()->id)->pluck('once_in_service_years')->first();

        switch($quotaUsed){
            case 10:
            case 20:
            case 100:
                if (intval($totalDays) > $checkQuotaLeft) {
                    return redirect()->back()->with('failed', "Your Leave Request Exceeds Your Leave Quota or Your Leave Quota is Expired, Ask the HR Dept for solutions");
                }
                break;
            default:
            break;
        }

        //HARDCODE FOR ROLES
        $getFM = Usr_role::where('role_id', 7)->pluck('user_id')->toArray();
        $getHR = Usr_role::where('role_id', 3)->pluck('user_id')->toArray();

        switch ($checkIfOnce) {
            case false:
            case NULL:
                switch (true) {
                    case ($UserDept->approvers->isNotEmpty()): // Check if there are any department approvers
                        Leave_request::create([
                            'id' => $uniqueId,
                            'req_date' => date('Y-m-d'),
                            'req_by' => Auth::user()->id,
                            'leave_dates' => $request->datepickLeave,
                            'total_days' => $totalDays,
                            'reason' => $request->reason,
                            'leave_id' => $request->quota_used,
                            'contact_number' => $request->cp_number,
                        ]);
                        if(!$usersWithPMRole->isEmpty()){
                            foreach($usersWithPMRole as $approverPM){
                                if(in_array(Auth::id(), $isManager) || in_array($checkUserPost, $getHighPosition) || Auth::id() == $approverPM->user_id){
                                    break;
                                }
                                Leave_request_approval::create([
                                    'status' => $statusId,
                                    'RequestTo' => $approverPM->user_id,
                                    'leave_request_id' => $uniqueId
                                ]);
                                $userToApprove[] = $approverPM->user_id;
                            }
                        }
                        foreach ($UserDept->approvers as $approver) {
                            // Skip if the user is a manager and the approver is also a manager
                            if (in_array(Auth::id(), $isManager) && in_array($approver->approver, $isManager)) {
                                continue;
                            }

                            // Skip if the user is finance staff and the approver is 'desy'
                            if (in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray()) && in_array($approver->approver, $getHR)) {
                                continue;
                            }

                            // Skip if the user is not finance staff and the approver is 'suryadi'
                            if (!in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray()) && in_array($approver->approver, $getFM)) {
                                continue;
                            }
                            Leave_request_approval::create([
                                'status' => $statusId,
                                'RequestTo' => $approver->approver,
                                'leave_request_id' => $uniqueId
                            ]);
                            $userToApprove[] = $approver->approver;
                        }
                        break;
                    default:
                        return redirect()->back()->with('failed', "You haven't been assigned to any department! Please contact the HR Department to correct your account details.");
                }
                break;
            default:
                return redirect()->back()->with('failed', "You have already used your quota, or the requested days exceed your quota!");
        }

        // $getUsedQuota = Emp_leave_quota::where
        //set quota for leaves
        switch($quotaUsed) {
            case 10:
            case 20:
            case 100:
                foreach ($checkQuota as $quota) {
                    if ($countQuota > 0) {
                        $remainingQuota = $quota->quota_left;
                        $deductedQuota = min($countQuota, $remainingQuota);
                        $countQuota -= $deductedQuota;
                        $quota->quota_used += $deductedQuota;
                        $quota->quota_left -= $deductedQuota;
                        $quota->save();

                        $history = New Leave_request_history;
                        $history->req_date = date('Y-m-d');
                        $history->req_by = Auth::user()->id;
                        $history->requested_days = $request->total_days;
                        $history->quota_used = $deductedQuota;
                        $history->quota_left = $quota->quota_left;
                        $history->description = "Leave Request";
                        $history->leave_id = $request->quota_used;
                        $history->emp_leave_quota_id = $quota->id;
                        $history->leave_request_id = $uniqueId;
                        $history->save();
                    } else {
                        break; // Stop deducting if countQuota reaches zero
                    }
                }
                break;
            default:
                Emp_leave_quota::updateOrCreate([
                    'user_id' => Auth::user()->id,
                    'leave_id' => $request->quota_used
                ], [
                    'once_in_service_years' => TRUE
                ]);
            break;
        }

        $employees = User::whereIn('id', $userToApprove)->get();
        $leaveRequestForm = Leave_request::find($uniqueId);

        foreach ($employees as $employee) {
            if(in_array($employee->id, $isManager)){
                dispatch(new NotifyLeaveApproval($employee, $leaveRequestForm));
            }
        }

        $checkUserPost = Auth::user()->users_detail->position->id;

        if (in_array($checkUserPost, $getHighPosition)) {
            //truncate
            Leave_request_approval::where('leave_request_id', $uniqueId)->delete();
            //recreate
            Leave_request_approval::create([
                'status' => 29,
                'RequestTo' => Auth::id(),
                'leave_request_id' => $uniqueId
            ]);
        }

        return redirect("/leave/history")->with('success', "Leave Request Submitted Successfully");
    }

    public function leave_request_details($id)
    {
        $leaveRequest = Leave_request_approval::where('leave_request_id', $id)->orderBy('updated_at', 'desc')->get();

        // Initialize an empty array to store the data
        $data = [];

        $status = '';

        foreach ($leaveRequest as $lr) {
            if($lr->status == 29 || $lr->status == 20){
                $status = '<i class="fas fa-check-circle" style="color: #005eff;"></i> Approved';
            } elseif ($lr->status == 404){
                $status = '<i class="fas fa-times-circle" style="color: #ff0000;"></i> Rejected';
            } else {
                $status = '<i class="fas fa-spinner fa-spin"></i> Waiting for Approval';
            }
            $reqDateFormatted = Carbon::parse($lr->leave_request->req_date)->format('d-M-Y');
            $lastUpdated = Carbon::parse($lr->updated_at)->format('d-M-Y H:i');
            $data[] = [
                'requestBy' => $lr->leave_request->user->name,
                'requestDate' => $reqDateFormatted,
                'quotaUsed' => $lr->leave_request->leave->description,
                'leaveDates' => $lr->leave_request->leave_dates,
                'totalDays' => $lr->leave_request->total_days,
                'reason' => $lr->leave_request->reason,
                'status' => $status,
                'RequestTo' => $lr->user->name,
                'notes' => $lr->notes,
                'last_updated' => $lastUpdated,
            ];
        }

        $leaveApproverCount = $leaveRequest->count();
        $leaveApproverApprovedCount = $leaveRequest->whereIn('status', [29, 20])->count();

        if ($leaveApproverCount > 0) {
            $approvalPercentage = ($leaveApproverApprovedCount / $leaveApproverCount) * 100;
        } else {
            $approvalPercentage = 100;
        }

        $data[0]['approvalPercentage'] = $approvalPercentage;
        // Get the name of the leave approver who has status 15
        $checkIfRejected = $leaveRequest->where('status', 404)->first();
        $leaveRunningApprover = $leaveRequest->where('status', 15)->first();
        if($leaveRunningApprover){
            $leaveRunningApproverName = $leaveRunningApprover->user->name;
            if(!empty($checkIfRejected)){
                $leaveRunningApproverName = "Rejected";
            }
        } else {
            $leaveRunningApproverName = "Approved";
        }

        $data[0]['leaveRunningApprover'] = $leaveRunningApproverName;

        return json_encode($data);
    }

    public function cancel_request($id)
    {
        // Find the leave request by ID
        $leaveRequest = Leave_request::findOrFail($id);

        // Retrieve and process leave request history records
        $addQuotas = Leave_request_history::where('leave_request_id', $id)
            ->where('req_by', Auth::user()->id)
            ->get();

        foreach ($addQuotas as $addQuota) {
            $returnQuota = Emp_leave_quota::findOrFail($addQuota->emp_leave_quota_id);

            // Update quota values
            $returnQuota->quota_used -= $addQuota->quota_used;
            $returnQuota->quota_left += $addQuota->quota_used;
            $returnQuota->save();
        }

        // Get user IDs associated with the leave request approvals
        $approvalIds = Leave_request_approval::where('leave_request_id', $leaveRequest->id)->pluck('RequestTo')->toArray();

        // Get users who are approvers
        $approvers = User::whereIn('id', $approvalIds)->get();

        // Dispatch notification jobs to users
        foreach ($approvers as $approver) {
            dispatch(new NotifyCancelLeaveRequest($approver, $leaveRequest->user->name));
        }

        // Delete leave request history records
        Leave_request_history::where('leave_request_id', $id)
            ->where('req_by', Auth::user()->id)
            ->delete();

        // Delete the leave request
        Leave_request::where('id', $id)
            ->where('req_by', Auth::user()->id)
            ->delete();

        // Delete associated leave request approvals
        Leave_request_approval::where('leave_request_id', $id)->delete();

        return redirect()->back()->with('success', "Leave Request has been canceled!");
    }

    public function manage(Request $request)
	{
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 3, $nowYear + 3);

        $limitShown = ["All",10,20,30];
        $employees = User::all();
        $validator = Validator::make($request->all(), [
            'showOpt' => 'sometimes',
            'yearOpt' => 'required',
            'limitOpt' => 'required'
        ]);

        $emp_leave_quota = Emp_leave_quota::with('users_detail')
        ->whereHas('users_detail', function ($query) {
            $query->where('status_active', 'Active');
        })
        ->orderBy('user_id', 'asc');

        $limit = intval($request->limitOpt);
        $showName = $request->showOpt;
        if ($validator->passes()) {
            $Year = $request->yearOpt;
            if($showName == 1){
                $emp_leave_quota->whereYear('active_periode', $Year)->take($limit);
            } else {
                $emp_leave_quota->where('user_id', $showName);
            }
        }

        $emp_leave_quota = $emp_leave_quota->get();
        // dd($approvals);
		return view('leave.manage_leave', compact('emp_leave_quota', 'Year', 'showName', 'limitShown', 'limit', 'yearsBefore' ,'employees'));
	}

    public function manage_leave_emp($id)
	{
        $user_info = User::find($id);
        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 10)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 100)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
            ->where('user_id', $user_info->id)
            ->where('leave_id', 20)
            ->sum('quota_left');
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }

        $leaveType = Leave::all();
        $empLeaves = Emp_leave_quota::where('user_id', $user_info->id)->orderBy('expiration', 'asc')->get();
		return view('leave.manage_leave_user', compact('empLeaveQuotaAnnual', 'leaveType', 'empLeaveQuotaFiveYearTerm', 'empLeaveQuotaWeekendReplacement', 'totalQuota', 'user_info', 'empLeaves'));
	}

    public function get_leave_emp($id)
	{
        $data = Emp_leave_quota::find($id);
        // Return the data as a JSON response
        return response()->json($data);
    }

    public function update_leave_emp(Request $request, $id)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'active_periode' => 'required',
            'expiration' => 'required',
            'quota_left' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect()->back();
        }

        $row = Emp_leave_quota::find($id);
        $row->active_periode = $request->active_periode;
        $row->expiration = $request->expiration;
        $row->quota_left = $request->quota_left;
        $row->save();

        return response()->json(['success' => 'Employee Leave updated successfully.']);
    }

    // public function edit_leave_emp($usrId, $id)
	// {
    //     $user_info = User::find($usrId);
    //     $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', $user_info->id)
    //         ->where('leave_id', 10)
    //         ->where('expiration', '>=', date('Y-m-d'))
    //         ->sum('quota_left');
    //     $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', $user_info->id)
    //         ->where('leave_id', 100)
    //         ->where('expiration', '>=', date('Y-m-d'))
    //         ->sum('quota_left');
    //     $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
    //         ->where('user_id', $user_info->id)
    //         ->where('leave_id', 20)
    //         ->sum('quota_left');
    //     $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
    //     if($empLeaveQuotaFiveYearTerm == NULL){
    //         $empLeaveQuotaFiveYearTerm = "-";
    //     }

    //     $empLeaves = Emp_leave_quota::where('user_id', $user_info->id)->get();
	// 	return view('leave.edit_leave_user', compact('empLeaveQuotaAnnual', 'empLeaveQuotaFiveYearTerm', 'empLeaveQuotaWeekendReplacement', 'totalQuota', 'user_info', 'empLeaves'));
	// }

    public function add_leave_quota(Request $request, $emp)
    {
        date_default_timezone_set("Asia/Jakarta");
        $validator = Validator::make($request->all(), [
            'leaveStatus' => 'required',
            'addLeaveQuotaType' => 'required',
            'addLeaveActivePeriode' => 'required',
            'addLeaveExpiration' => 'required',
            'addLeaveQuota' => 'required',
        ]);

        $empLeave = new Emp_leave_quota;
        $empLeave->user_id = $emp;
        $empLeave->quota_used = 0;
        $empLeave->leave_id = $request->addLeaveQuotaType;
        $onceInServiceYears = $request->leaveStatus; // Get the value of the switch button
        if ($onceInServiceYears) {
            $empLeave->once_in_service_years = 1;
        } else {
            $empLeave->once_in_service_years = 0;
        }
        $empLeave->active_periode = $request->addLeaveActivePeriode;
        $empLeave->expiration = $request->addLeaveExpiration;
        $empLeave->quota_left = $request->addLeaveQuota;
        $empLeave->save();

        $entry = new Notification_alert;
        $entry->user_id = $emp;
        $entry->message = "A new quota has been added to your Leave Balance!";
        $entry->importance = 1;
        $entry->save();

        return redirect()->back()->with('success', "Leave Quota has been added!");
    }

    public function manage_request(Request $request)
    {
        $Month = date('m');
        $Year = date('Y');

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNull('resignation_date');
		})->get();

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        $approvals = Leave_request::orderBy('req_by', 'asc')->orderBy('req_date', 'asc');

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $approvals->whereYear('req_date', $Year);
            $approvals->whereMonth('req_date', intval($Month));
        } else {
            $approvals->whereYear('req_date', $Year);
            $approvals->whereMonth('req_date', intval($Month));
        }

        $approvals = $approvals->get();
        // dd($approvals);
        return view('leave.manage_request', compact('approvals', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function emp_leave_request($id, $month, $year)
	{
        $user_info = User::find($id);
        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 10)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 100)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
            ->where('user_id', $user_info->id)
            ->where('leave_id', 20)
            ->sum('quota_left');
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }

        $leaveType = Leave::all();
        $leaveRequests = Leave_request::where('req_by', $user_info->id)->whereMonth('req_date', $month)->whereYear('req_date', $year)->get();
        foreach ($leaveRequests as $lr) {
            $dates = explode(',', $lr->leave_dates);
            $currentMonth = null;
            $dateGroups = [];
            $group = [];

            foreach ($dates as $date) {
                $formattedDate = date('d', strtotime($date));
                $monthYear = date('F Y', strtotime($date));

                if ($currentMonth !== $monthYear) {
                    if (!empty($group)) {
                        $dateGroups[] = $group;
                        $group = [];
                    }
                    $group['monthYear'] = $monthYear;
                    $group['dates'] = [$formattedDate];
                    $currentMonth = $monthYear;
                } else {
                    $group['dates'][] = $formattedDate;
                }
            }

            if (!empty($group)) {
                $dateGroups[] = $group;
            }

            $lr->dateGroups = $dateGroups;

            $approved = false;

            foreach ($lr->leave_request_approval as $stat) {
                if ($stat->status == 29) {
                    $lr->approvalStatus = "<span class='m-0 text-primary'>Approved</span>";
                    $approved = true;
                    break;
                } elseif($stat->status == 404) {
                    $lr->approvalStatus = "<span class='m-0 text-danger'>Rejected</span>";
                    $approved = true;
                    break;
                }
            }

            if (!$approved) {
                $lr->approvalStatus = "<span class='m-0 text-secondary'>Waiting for Approval</span>";
            }
        }
		return view('leave.emp_leave_request', compact('empLeaveQuotaAnnual', 'month','year', 'leaveType', 'empLeaveQuotaFiveYearTerm', 'empLeaveQuotaWeekendReplacement', 'totalQuota', 'user_info', 'leaveRequests'));
	}

    public function get_leave_request_data($usrid, $month, $year, $id)
    {
        $user_info = User::find($usrid);
        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 10)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', $user_info->id)
            ->where('leave_id', 100)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
            ->where('user_id', $user_info->id)
            ->where('leave_id', 20)
            ->sum('quota_left');
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }

        $leaveType = Leave::all();
        $leaveRequests = Leave_request::find($id);
        $usersList = User::all();
        $statusDesc = Approval_status::all();

		return view('leave.emp_leave_request_edit', compact('id','empLeaveQuotaAnnual', 'statusDesc','usersList', 'month','year', 'leaveType', 'empLeaveQuotaFiveYearTerm', 'empLeaveQuotaWeekendReplacement', 'totalQuota', 'user_info', 'leaveRequests'));
	}

    public function approve_by_admin($usrId, $month, $year, $id)
    {
        date_default_timezone_set("Asia/Jakarta");
        //update stat
        Leave_request_approval::where('leave_request_id', $id)->update(['status' => 20, 'notes' => 'Approved by Administrator']);

        $getLeaveReq = Leave_request::find($id);

        switch($getLeaveReq->leave_id){
            case 10:
            case 20:

                break;
            default:
                Emp_leave_quota::updateOrCreate([
                    'user_id' => $getLeaveReq->req_by,
                    'leave_id' => $getLeaveReq->leave_id
                ], [
                    'once_in_service_years' => TRUE
                ]);
            break;
        }

        //create 29 stat by admin
        Leave_request_approval::updateOrCreate(
            [
                'leave_request_id' => $id,
                'RequestTo' => "admin"
            ],
            [
                'status' => 29,
                'notes' => "Approved by Administrator"
            ]
        );

        $entry = new Notification_alert();
        $entry->user_id = $getLeaveReq->req_by;
        $entry->message = "Your Leave Request has been Approved by Administrator!";
        $entry->importance = 1;
        $entry->save();

        return redirect()->back()->with('success',"You approved the leave request!");
    }

    public function reject_by_admin($usrId, $month, $year, $id)
    {
        date_default_timezone_set("Asia/Jakarta");
        //update stat
        Leave_request_approval::where('leave_request_id', $id)->update(['status' => 404, 'notes' => 'Rejected by Administrator']);

        $getLeaveReq = Leave_request::find($id);

        switch($getLeaveReq->leave_id){
            case 10:
            case 20:

                break;
            default:
                Emp_leave_quota::updateOrCreate([
                    'user_id' => $getLeaveReq->req_by,
                    'leave_id' => $getLeaveReq->leave_id
                ], [
                    'once_in_service_years' => TRUE
                ]);
            break;
        }

        //create 29 stat by admin
        Leave_request_approval::updateOrCreate(
            [
                'leave_request_id' => $id,
                'RequestTo' => "admin"
            ],
            [
                'status' => 404,
                'notes' => "Rejected by Administrator"
            ]
        );

        $getIdLeaveReq = Leave_request_approval::where('leave_request_id', $id)->pluck('leave_request_id')->first();
        $getLeaveReq = Leave_request::where('id', $getIdLeaveReq)->first();

        $addQuota = Leave_request_history::where('leave_request_id', $getIdLeaveReq)->where('req_by', $getLeaveReq->req_by)->get();
        foreach ($addQuota as $aq) {
            $returnQuota = Emp_leave_quota::find($aq->emp_leave_quota_id);
            $totalQuotaUsed = $aq->quota_used;

            $returnQuota->quota_used -= $totalQuotaUsed;
            $returnQuota->quota_left += $totalQuotaUsed;
            $returnQuota->save();
        }

        //delete
        Leave_request_history::where('leave_request_id', $getIdLeaveReq)->where('req_by', $getLeaveReq->req_by)->delete();

        $entry = new Notification_alert();
        $entry->user_id = $getLeaveReq->req_by;
        $entry->message = "Your Leave Request has been Rejected by Administrator!";
        $entry->importance = 1;
        $entry->save();

        return redirect()->back()->with('failed',"You Rejected the leave request!");
    }

    public function delete_by_admin($id)
    {
        Leave_request_approval::where('id', $id)->delete();

        return redirect()->back()->with('failed',"You Deleted the leave request approval!");
    }

    public function delete_leave_emp($id)
    {
        Emp_leave_quota::where('id', $id)->delete();

        return redirect()->back()->with('failed',"You Deleted the leave request approval!");
    }

    public function update_by_admin(Request $request, $id)
    {
        foreach ($request->input('items') as $itemId => $itemData) {
            $approver = $itemData['approver'];
            $status = $itemData['status'];
            // Do something with $description and $quantity
            // You can use the $itemId to find the corresponding PoItem object
            $poItem = Leave_request_approval::find($itemId);
            $poItem->RequestTo = $approver;
            $poItem->status = $status;
            $poItem->save();
        }

        return redirect()->back()->with('success',"You Updated the Leave Request Approver!");
    }

    public function add_leave_employee (Request $request)
    {
        $this->validate($request, [
            'input_emp_id' => 'required',
            'input_leave_id' => 'required',
            'input_quota_used' => 'required',
            'input_quota_left' => 'required',
            'input_active_periode' => 'required',
            'input_expiration' => 'required',
        ]);

        $leaveAdd = new Emp_leave_quota();
        $leaveAdd->user_id = $request->input_emp_id;
        $leaveAdd->leave_id = $request->input_leave_id;
        $leaveAdd->quota_used = $request->input_quota_used;
        $leaveAdd->quota_left = $request->input_quota_left;
        $leaveAdd->active_periode = $request->input_active_periode;
        $leaveAdd->expiration = $request->input_expiration;
        $leaveAdd->save();

        return redirect()->back()->with('success', "You Add New Leave For Employee");
    }

    public function weekend_replacement_entry(Request $request)
    {
        $this->validate($request, [
            'datepickLeave' => 'sometimes',
            'quota_used' => 'required',
            'total_days' => 'required|numeric|min:1', // Ensure total days is numeric and at least 1
        ]);

        $pickedDates = explode(',', $request->datepickLeave);
        $totalDays = count($pickedDates);

        // Fetch weekend replacement records that are not taken yet and have expiration dates greater than or equal to today
        $weekendReplacements = Surat_penugasan::where('isTaken', FALSE)
            ->where('isWeekend', TRUE)
            ->where('user_id', Auth::id())
            ->where('expiration', '>=', now()->toDateString())
            ->orderBy('ts_date', 'asc')
            ->get();

        // Validate if enough weekend replacements are available
        if ($weekendReplacements->count() < $totalDays) {
            Session::flash('failed',"400 - Not enough available weekend replacements!");
        }

        // Take the required number of weekend replacements
        foreach ($pickedDates as $pickedDate) {
            $pickedDate = \Carbon\Carbon::createFromFormat('m/d/Y', $pickedDate)->toDateString();

            // Find the suitable weekend replacement
            $suitableReplacement = $weekendReplacements->first(function ($wr) use ($pickedDate) {
                return $wr->expiration >= $pickedDate;
            });

            if ($suitableReplacement) {
                $suitableReplacement->date_to_replace = $pickedDate;
                $suitableReplacement->isTaken = TRUE;
                $suitableReplacement->save();
                Session::flash('success','200 - Weekend replacements taken successfully');
            } else {
                Session::flash('failed','400 - No suitable weekend replacement found for the picked date: ' . $pickedDate);
            }
        }

        // Return success response
        return redirect('/leave/history');
    }
}
