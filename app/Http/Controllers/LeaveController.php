<?php

namespace App\Http\Controllers;

use App\Mail\ApprovalLeave;
use App\Models\Emp_leave_quota;
use App\Models\Leave;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\User;
use App\Models\Usr_role;
use DateTime;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', 10)
            ->where('active_periode', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('active_periode', '>=', date('Y-m-d'))->where('user_id', Auth::user()->id)->where('leave_id', 20)->pluck('quota_left')->first();
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm;
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
                    $lr->approvalStatus = "<span class='m-0 font-weight-bold text-primary'>Approved</span>";
                    $approved = true;
                    break;
                } elseif($stat->status == 404) {
                    $lr->approvalStatus = "<span class='m-0 font-weight-bold text-danger'>Rejected</span>";
                    $approved = true;
                    break;
                }
            }
            
            if (!$approved) {
                $lr->approvalStatus = "<span class='m-0 font-weight-bold text-secondary'>Waiting for Approval</span>";
            }
        }
        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::whereIn('project_assignment_id', $findAssignment)->where('role', 'PM')->get();

        return view('leave.history', compact('yearsBefore', 'leaveType', 'usersWithPMRole', 'leaveRequests', 'empLeaveQuotaAnnual', 'empLeaveQuotaFiveYearTerm', 'totalQuota'));
	}

    public function leave_request_entry(Request $request){
        $this->validate($request,[
            'datepickLeave' => 'required',
    		'quota_used' => 'required',
            'cp_number' => 'sometimes',
            'total_days' => 'required',
            'reason' => 'required'
    	]);
        $totalDays = $request->total_days;
        $quotaUsed = $request->quota_used;

        $checkQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', 10)
        ->where('active_periode', '>=', date('Y-m-d'))
        ->sum('quota_left');

        if (intval($totalDays) > $checkQuota) {
            return redirect()->back()->with('failed', "Your Leave Request Exceeds Your Leave Quota or Your Leave Quota is Expired, Ask the HR Dept for solutions");
        }

        // $dateString = $request->daterangeLeave;
        // list($startDateString, $endDateString) = explode(' - ', $dateString);
        // $startDate = DateTime::createFromFormat('m/d/Y', $startDateString);
        // $endDate = DateTime::createFromFormat('m/d/Y', $endDateString);

        // echo "Total days: " . $totalDays;
        $checkUserDept = Auth::user()->users_detail->department->id;

        $approvalFinance_GA = Usr_role::whereIn('role_name', ['hr', 'fin_ga_dir'])
            ->groupBy('role_name')
            ->get();
        $approvalSales = Usr_role::whereIn('role_name', ['hr', 'fin_ga_dir'])
            ->groupBy('role_name')
            ->get();
        $approvalHCM = Usr_role::whereIn('role_name', ['hr', 'fin_ga_dir'])
            ->groupBy('role_name')
            ->get();
        $approvalService = Usr_role::whereIn('role_name', ['pc', 'service_dir'])
            ->groupBy('role_name')
            ->get();

        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::whereIn('project_assignment_id', $findAssignment)->where('role', 'PM')->get();
        
        // Retrieve the relevant Emp_leave_quota rows ordered by active_periode in ascending order
        $checkQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', $request->quota_used)
        ->where('active_periode', '>=', date('Y-m-d'))
        ->orderBy('active_periode', 'asc')
        ->get();

        $countQuota = $request->total_days;

        $uniqueId = Str::uuid()->toString();
        $userToApprove = [];
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
                        'RequestTo' => $approverGa->user_id,
                        'leave_request_id' => $uniqueId
                    ]);
                    $userToApprove[] = $approverGa->user_id; 
                }
                foreach ($checkQuota as $quota) {
                    if ($countQuota > 0) {
                        $remainingQuota = $quota->quota_left;
                        $deductedQuota = min($countQuota, $remainingQuota);
                        $countQuota -= $deductedQuota;

                        $quota->quota_left -= $deductedQuota;
                        $quota->save();
                    } else {
                        break; // Stop deducting if countQuota reaches zero
                    }
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
                        'RequestTo' => $approverService->user_id,
                        'leave_request_id' => $uniqueId
                    ]);
                    $userToApprove[] = $approverService->user_id; 
                }
                if(!$usersWithPMRole->isEmpty()){
                    foreach($usersWithPMRole as $approverPM){
                        Leave_request_approval::create([
                            'status' => 15,
                            'RequestTo' => $approverPM->user_id,
                            'leave_request_id' => $uniqueId
                        ]);
                        $userToApprove[] = $approverPM->user_id; 
                    }
                }
                foreach ($checkQuota as $quota) {
                    if ($countQuota > 0) {
                        $remainingQuota = $quota->quota_left;
                        $deductedQuota = min($countQuota, $remainingQuota);
                        $countQuota -= $deductedQuota;
                
                        $quota->quota_left -= $deductedQuota;
                        $quota->save();
                    } else {
                        break; // Stop deducting if countQuota reaches zero
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
                        'RequestTo' => $approverHCM->user_id,
                        'leave_request_id' => $uniqueId
                    ]);
                    $userToApprove[] = $approverHCM->user_id; 
                }
                foreach ($checkQuota as $quota) {
                    if ($countQuota > 0) {
                        $remainingQuota = $quota->quota_left;
                        $deductedQuota = min($countQuota, $remainingQuota);
                        $countQuota -= $deductedQuota;
                
                        $quota->quota_left -= $deductedQuota;
                        $quota->save();
                    } else {
                        break; // Stop deducting if countQuota reaches zero
                    }
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
                        'RequestTo' => $approverSales->user_id,
                        'leave_request_id' => $uniqueId
                    ]);
                    $userToApprove[] = $approverSales->user_id; 
                }
                foreach ($checkQuota as $quota) {
                    if ($countQuota > 0) {
                        $remainingQuota = $quota->quota_left;
                        $deductedQuota = min($countQuota, $remainingQuota);
                        $countQuota -= $deductedQuota;
                
                        $quota->quota_left -= $deductedQuota;
                        $quota->save();
                    } else {
                        break; // Stop deducting if countQuota reaches zero
                    }
                }
                break; // Add break statement here to exit the switch block after executing the case
            default:
                return redirect()->back()->with('failed', "You haven't assigned to any department! Ask HR Dept to correcting your account details");
            break;
        }

        $employees = User::whereIn('id', $userToApprove)->get();
        $userName = Auth::user()->name;

        foreach ($employees as $employee) {
            $notification = new ApprovalLeave($employee, $userName);
            Mail::send('mailer.approval_leave', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                        ->subject($notification->emailSubject());
            });
        }

        // Leave_request::where('RequestTo', Auth::user()->id)->delete();

        return redirect("/leave/history")->with('success', "Leave Request Submitted Successfully");
    }

    public function cancel_request($id)
	{
        $leaveRequest = Leave_request::where('id', $id)->first();

        $sumQuotaLeft = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', $leaveRequest->leave_id)
            ->where('active_periode', '>=', date('Y-m-d'))
            ->sum('quota_left');

        $totalDays = intval($leaveRequest->total_days);
        
        // Retrieve the relevant Emp_leave_quota rows ordered by active_periode in ascending order
        $checkQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', $leaveRequest->leave_id)
        ->where('active_periode', '>=', date('Y-m-d'))
        ->orderBy('active_periode', 'desc')
        ->get();

        $countQuota = $totalDays; // Initialize the count

        foreach ($checkQuota as $quota) {
            if ($countQuota > 0) {
                $remainingQuota = $quota->quota_left;
                $addedQuota = min($countQuota, 12 - $remainingQuota); // Ensure quota_left doesn't exceed 12
                $countQuota -= $addedQuota;

                $quota->quota_left += $addedQuota;
                $quota->save();
            } else {
                break; // Stop adding if countQuota reaches zero
            }
        }

        //delete Leave Request
        Leave_request::where('id', $id)->where('req_by', Auth::user()->id)->where('req_date', $leaveRequest->req_date)->where('leave_id', $leaveRequest->leave_id)->delete();
        $deleteLRA = Leave_request_approval::where('leave_request_id', $id)->get();
        foreach ($deleteLRA as $del) {
            $del->delete();
        }

        return redirect()->back()->with('success', "Leave Request has been canceled!");
	}
}
