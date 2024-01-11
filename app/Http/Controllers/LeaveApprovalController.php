<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyLeaveApproved;
use App\Models\Emp_leave_quota;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Leave_request_history;
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
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 1 && $currentDay <= 31) {
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

        $getIdLeaveReq = Leave_request_approval::where('id', $id)->pluck('leave_request_id')->groupBy('leave_request_id')->first();
        $getLeaveReq = Leave_request::where('id', $getIdLeaveReq)->first();

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

        $entry = new Notification_alert();
        $entry->user_id = $getLeaveReq->req_by;
        $entry->message = "Your Leave Request has been Approved!";
        $entry->importance = 1;
        $entry->save();

        $employees = User::where('id', $getLeaveReq->req_by)->get();
        $userName = Auth::user()->name;

        ///only director send the emails
        switch (true) {
            case in_array(Auth::user()->id, $checkUserDir):
                foreach ($employees as $employee) {
                    dispatch(new NotifyLeaveApproved($employee, $userName));
                }
                break;
            default:
                break;
        }

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
        $entry->importance = 404;
        $entry->save();

        return redirect('/approval/leave')->with('failed',"You rejected the leave request!");
    }
}
