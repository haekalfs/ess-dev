<?php

namespace App\Http\Controllers;

use App\Models\Emp_leave_quota;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Notification_alert;
use App\Models\Timesheet_approver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaveApprovalController extends Controller
{
    public function leave_approval()
    {
        $currentYear = date('Y');

        // Get the current day of the month
        $currentDay = date('j');

        $checkUserPost = Auth::user()->users_detail->position->id;

        //code above is to check if those with id 40,45,55,60 are able to approve or not
        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60])->pluck('approver')->toArray();
        $ts_approver[] = Auth::user()->id;
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 5 && $currentDay <= 30) {
                if (in_array($checkUserPost, [7, 8, 12])) {
                    $Check = DB::table('leave_request_approval')
                        ->select('leave_request_id')
                        ->whereNotIn('RequestTo', $ts_approver)
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 20 THEN 1 WHEN status = 404 THEN 0 ELSE 0 END)')
                        ->groupBy('leave_request_id', 'RequestTo')
                        ->pluck('leave_request_id')
                        ->toArray();
                        if (!empty($Check)) {
                            $approvals = Leave_request_approval::select('*')
                            // ->whereYear('date_submitted', $currentYear)
                            ->where('RequestTo', Auth::user()->id)
                            ->whereNotIn('status', [29, 404, 20])
                            ->whereIn('leave_request_id', $Check)
                            ->groupBy('leave_request_id')
                            ->get();
                        } else {
                            $approvals = DB::table('leave_request_approval')
                                ->select('*')
                                ->where('RequestTo', 'xxhaekalsastraxx')
                                ->get();
                        }
                } else {
                    $Check = DB::table('leave_request_approval')
                        ->select('leave_request_id')
                        ->whereNotIn('RequestTo', $ts_approver)
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 404 THEN 0 ELSE 1 END)')
                        ->groupBy('leave_request_id', 'RequestTo')
                        ->pluck('leave_request_id')
                        ->toArray();
                        if (!empty($Check)) {
                            $approvals = Leave_request_approval::select('*')
                            // ->whereYear('date_submitted', $currentYear)
                            ->where('RequestTo', Auth::user()->id)
                            ->whereNotIn('status', [29, 404, 20])
                            ->whereIn('leave_request_id', $Check)
                            ->groupBy('leave_request_id')
                            ->get();
                        } else {
                            $approvals = DB::table('leave_request_approval')
                                ->select('*')
                                ->where('RequestTo', 'xxhaekalsastraxx')
                                ->get();
                        }
                }
            return view('approval.leave_approval', ['approvals' => $approvals]);
        } else {
            // Handle the case when the date is not within the range
            return redirect()->back()->with('failed', 'Today this page has been restricted, try again later...');
        }
    }

    public function approve(Request $request, $id)
    {
        date_default_timezone_set("Asia/Jakarta");

        $validator = Validator::make($request->all(), [
            'approval_notes' => 'sometimes'
        ]);

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [40,45,55,60])->pluck('approver');
        $checkUserDir = $timesheetApproversDir->toArray();

        $tsStatusId = '20';
        $activity = 'Approved';
    
        switch (true) {
            case in_array(Auth::user()->id, $checkUserDir):
                $tsStatusId = '29';
                $activity = 'All Approved';
                break;
            default:
                $tsStatusId = '20';
                break;
        }

        $approve = Leave_request_approval::where('id', $id);

        if ($validator->passes()) {
            $notes = $request->approval_notes;
            $approve->update(['status' => $tsStatusId, 'notes' => $notes]);
        } else {
            $approve->update(['status' => $tsStatusId]);
        }

        // $getIdLeaveReq = Leave_request_approval::where('id', $id)->pluck('leave_request_id')->groupBy('leave_request_id')->first();
        // $getLeaveReq = Leave_request::where('id', $getIdLeaveReq)->get();

        // // dd($getIdLeaveReq, $getLeaveReq);
        // foreach($getLeaveReq as $gl){
        //     $req_by = $gl->req_by;
        //     $leave_id = $gl->leave_id;
        //     $totalDays = $gl->total_days;
        // }
        
        // // Retrieve the relevant Emp_leave_quota rows ordered by active_periode in ascending order
        // $checkQuota = Emp_leave_quota::where('user_id', $req_by)
        // ->where('leave_id', $leave_id)
        // ->where('active_periode', '>=', date('Y-m-d'))
        // ->orderBy('active_periode', 'desc')
        // ->get();

        // $countQuota = $totalDays; // Initialize the count

        // foreach ($checkQuota as $quota) {
        //     if ($countQuota > 0) {
        //         $remainingQuota = $quota->quota_left;
        //         $deductedQuota = min($countQuota, $remainingQuota);
        //         $countQuota -= $deductedQuota;
        
        //         $quota->quota_left -= $deductedQuota;
        //         $quota->save();
        //     } else {
        //         break; // Stop deducting if countQuota reaches zero
        //     }
        // }
        return redirect('/approval/leave')->with('success',"You approved the leave request!");
    }

    public function reject(Request $request, $id)
    {
        date_default_timezone_set("Asia/Jakarta");

        $validator = Validator::make($request->all(), [
            'reject_notes' => 'sometimes'
        ]);

        $tsStatusId = '404';

        $approve = Leave_request_approval::where('id', $id);
        
        if ($validator->passes()) {
            $notes = $request->reject_notes;
            $approve->update(['status' => $tsStatusId, 'notes' => $notes]);
        } else {
            $approve->update(['status' => $tsStatusId]);
        }

        $getIdLeaveReq = Leave_request_approval::where('id', $id)->pluck('leave_request_id')->groupBy('leave_request_id')->first();
        $getLeaveReq = Leave_request::where('id', $getIdLeaveReq)->first();
        
        $totalDays = intval($getLeaveReq->total_days);
        
        // Retrieve the relevant Emp_leave_quota rows ordered by active_periode in ascending order
        $checkQuota = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', $getLeaveReq->leave_id)
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
        // $employees = User::where('id', $user_timesheet)->get();

        // //add notes to reject notification email
        // foreach ($employees as $employee) {
        //     $notification = new RejectedTimesheet($employee, $year, $month);
        //     Mail::send('mailer.rejected_timesheet', $notification->data(), function ($message) use ($notification) {
        //         $message->to($notification->emailTo())
        //                 ->subject($notification->emailSubject());
        //     });
        // }
        $entry = new Notification_alert();
        $entry->user_id = $getLeaveReq->req_by;
        $entry->message = "Your Leave Request has been rejected!";
        $entry->importance = 1;
        $entry->save();

        return redirect('/approval/leave')->with('failed',"You rejected the leave request!");
    }
}
