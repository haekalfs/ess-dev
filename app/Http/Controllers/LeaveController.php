<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyLeaveApproval;
use App\Mail\ApprovalLeave;
use App\Models\Emp_leave_quota;
use App\Models\Leave;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Leave_request_history;
use App\Models\Notification_alert;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
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

        $leaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->whereIn('leave_id', [10, 20])->orderBy('expiration', 'asc')->get();

        $leaveQuotaUsage = Leave_request_history::where('req_by', Auth::user()->id)->get();

        $weekendReplacementQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', 100)
        ->where('expiration', '>=', date('Y-m-d'))
        ->get();
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', 100)
        ->where('expiration', '>=', date('Y-m-d'))
        ->sum('quota_left');
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

        $checkUserDept = Auth::user()->users_detail->department->id;

        $approvalFinance_GA = Timesheet_approver::whereIn('id', [10, 45])
            ->get();
        $approvalSales = Timesheet_approver::whereIn('id', [50, 45])
            ->get();
        $approvalHCM = Timesheet_approver::whereIn('id', [10, 60])
            ->get();
        $approvalService = Timesheet_approver::whereIn('id', [20, 40])
            ->get();

        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
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

        switch ($checkIfOnce) {
            case false:
            case NULL:
                switch (true) {
                    case ($checkUserDept == 4):
                        Leave_request::create([
                            'id' => $uniqueId,
                            'req_date' => date('Y-m-d'),
                            'req_by' => Auth::user()->id,
                            'leave_dates' => $request->datepickLeave,
                            'total_days' => $request->total_days,
                            'reason' => $request->reason,
                            'leave_id' => $request->quota_used,
                            'contact_number' => $request->cp_number,
                        ]);
                        foreach($approvalFinance_GA as $approverGa){
                            Leave_request_approval::create([
                                'status' => 15,
                                'RequestTo' => $approverGa->approver,
                                'leave_request_id' => $uniqueId
                            ]);
                            $userToApprove[] = $approverGa->approver;

                        }
                        break;
                        // No break statement here, so it will continue to the next case
                    case ($checkUserDept == 2):
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
                        foreach($approvalService as $approverService){
                            Leave_request_approval::create([
                                'status' => 15,
                                'RequestTo' => $approverService->approver,
                                'leave_request_id' => $uniqueId
                            ]);
                            $userToApprove[] = $approverService->approver; 
                        }
                        if(!$usersWithPMRole->isEmpty()){
                            foreach($usersWithPMRole as $approverPM){
                                Leave_request_approval::create([
                                    'status' => 15,
                                    'RequestTo' => $approverPM->approver,
                                    'leave_request_id' => $uniqueId
                                ]);
                                $userToApprove[] = $approverPM->approver; 
                            }
                        }
                        break;
                    case ($checkUserDept == 3):
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
                        foreach($approvalHCM as $approverHCM){
                            Leave_request_approval::create([
                                'status' => 15,
                                'RequestTo' => $approverHCM->approver,
                                'leave_request_id' => $uniqueId
                            ]);
                            $userToApprove[] = $approverHCM->approver; 
                        }
                        break;
                    case ($checkUserDept == 1):
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
                        foreach($approvalSales as $approverSales){
                            Leave_request_approval::create([
                                'status' => 15,
                                'RequestTo' => $approverSales->approver,
                                'leave_request_id' => $uniqueId
                            ]);
                            $userToApprove[] = $approverSales->approver; 
                        }
                        break; // Add break statement here to exit the switch block after executing the case
                    default:
                        return redirect()->back()->with('failed', "You haven't assigned to any department! Ask HR Dept to correcting your account details");
                    break;
                }
                break;
            default:
                return redirect()->back()->with('failed', "You already used the qouta, or requested days is exceed from quota!");
            break;
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
        $userName = Auth::user()->name;

        foreach ($employees as $employee) {
            dispatch(new NotifyLeaveApproval($employee, $userName));
        }

        Leave_request::where('RequestTo', Auth::user()->id)->delete();

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
                $status = "Approved";
            } elseif ($lr->status == 404){
                $status = "Rejected";
            } else {
                $status = "Waiting for Approval";
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
        $leaveRequest = Leave_request::where('id', $id)->first();

        $addQuota = Leave_request_history::where('leave_request_id', $id)->where('req_by', Auth::user()->id)->get();
        foreach ($addQuota as $aq) {
            $returnQuota = Emp_leave_quota::find($aq->emp_leave_quota_id);
            $totalQuotaUsed = $aq->quota_used;

            $returnQuota->quota_used -= $totalQuotaUsed;
            $returnQuota->quota_left += $totalQuotaUsed;
            $returnQuota->save();
        }

        //delete
        Leave_request_history::where('leave_request_id', $id)->where('req_by', Auth::user()->id)->delete();

        //delete Leave Request
        Leave_request::where('id', $id)->where('req_by', Auth::user()->id)->where('req_date', $leaveRequest->req_date)->where('leave_id', $leaveRequest->leave_id)->delete();
        $deleteLRA = Leave_request_approval::where('leave_request_id', $id)->get();
        foreach ($deleteLRA as $del) {
            $del->delete();
        }

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

        $emp_leave_quota = Emp_leave_quota::orderBy('user_id', 'asc');
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
        $empLeaves = Emp_leave_quota::where('user_id', $user_info->id)->get();
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

    public function edit_leave_emp($usrId, $id)
	{
        $user_info = User::find($usrId);
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

        $empLeaves = Emp_leave_quota::where('user_id', $user_info->id)->get();
		return view('leave.edit_leave_user', compact('empLeaveQuotaAnnual', 'empLeaveQuotaFiveYearTerm', 'empLeaveQuotaWeekendReplacement', 'totalQuota', 'user_info', 'empLeaves'));
	}

    public function add_leave_quota(Request $request, $emp){
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
}
