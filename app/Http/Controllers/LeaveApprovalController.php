<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyLeaveApproval;
use App\Jobs\NotifyLeaveApproved;
use App\Jobs\NotifyRejectedLeaveRequest;
use App\Models\Emp_leave_quota;
use App\Models\Leave_request;
use App\Models\Leave_request_approval;
use App\Models\Leave_request_history;
use App\Models\Notification_alert;
use App\Models\Position;
use App\Models\Timesheet_approver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaveApprovalController extends Controller
{
    public function leave_approval($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if ($yearSelected) {
            $currentYear = $yearSelected;
        }

        // Get the current day of the month
        $currentDay = date('j');

        $checkUserPost = Auth::user()->users_detail->position->id;
        $getHighPosition = Position::where('position_level', 1)->pluck('id')->toArray();

        //code above is to check if those with id 40,45,55,60 are able to approve or not
        $ts_approver = Timesheet_approver::where('group_id', 1)->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 1 && $currentDay <= 31) {
                if (in_array($checkUserPost, $getHighPosition)) {
                    $Check = DB::table('leave_request_approval')
                        ->select('leave_request_id')
                        ->whereNotIn('RequestTo', $ts_approver)
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 20 THEN 1 WHEN status = 404 THEN 0 ELSE 0 END)')
                        ->groupBy('leave_request_id', 'RequestTo')
                        ->pluck('leave_request_id')
                        ->toArray();
                        if (!empty($Check)) {
                            $approvals = Leave_request_approval::select('*')
                            ->whereYear('created_at', $currentYear)
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
                        ->whereYear('created_at', $currentYear)
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
            return view('approval.leave_approval', ['approvals' => $approvals, 'yearsBefore' => $yearsBefore, 'yearSelected' => $yearSelected]);
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

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect('/approval/leave')->with('success', "You approved the leave request!");
        }

        $timesheetApproversDir = Timesheet_approver::whereIn('id', [40, 45, 55, 60])->pluck('approver')->toArray();

        $tsStatusId = in_array(Auth::user()->id, $timesheetApproversDir) ? '29' : '20';
        $activity = in_array(Auth::user()->id, $timesheetApproversDir) ? 'All Approved' : 'Approved';

        $approve = Leave_request_approval::findOrFail($id);

        $notes = $request->input('approval_notes', '');

        $approve->update([
            'status' => $tsStatusId,
            'notes' => $notes
        ]);

        $leaveRequestId = $approve->leave_request_id;

        $getLeaveReq = Leave_request::findOrFail($leaveRequestId);

        if (!in_array($getLeaveReq->leave_id, [10, 20])) {
            Emp_leave_quota::updateOrCreate([
                'user_id' => $getLeaveReq->req_by,
                'leave_id' => $getLeaveReq->leave_id
            ], [
                'once_in_service_years' => true
            ]);
        }

        Notification_alert::create([
            'user_id' => $getLeaveReq->req_by,
            'message' => "Your Leave Request has been Approved!",
            'importance' => 1
        ]);

        $userName = Auth::user()->name;

        if (in_array(Auth::user()->id, $timesheetApproversDir)) {
            $requestor = User::where('id', $getLeaveReq->req_by)->get();

            foreach ($requestor as $req) {
                dispatch(new NotifyLeaveApproved($req, $userName));
            }
        } else {
            $getIdApprovers = Leave_request_approval::where('leave_request_id', $leaveRequestId)
                ->where('status', 15)
                ->groupBy('RequestTo')
                ->pluck('RequestTo')
                ->toArray();

            $employees = User::whereIn('id', $getIdApprovers)->get();

            foreach ($employees as $employee) {
                NotifyLeaveApproval::dispatch($employee, $getLeaveReq);
            }
        }

        return redirect('/approval/leave')->with('success', "You approved the leave request!");
    }

    public function reject(Request $request, $id)
    {
        // Set the default timezone to Asia/Jakarta
        date_default_timezone_set("Asia/Jakarta");

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'reject_notes' => 'sometimes'
        ]);

        // Set the status ID for rejection
        $tsStatusId = '404';

        // Find the leave request approval by ID
        $approve = Leave_request_approval::findOrFail($id);

        //Mailer
        $employee = User::find($approve->leave_request->req_by);
        dispatch(new NotifyRejectedLeaveRequest($employee, $approve));

        // Update the status and rejection notes if present
        $approve->status = $tsStatusId;
        if ($validator->passes()) {
            $approve->notes = $request->reject_notes;
        }
        $approve->save();

        // Get the leave request associated with the approval
        $leaveRequestId = $approve->leave_request_id;
        $leaveRequest = Leave_request::findOrFail($leaveRequestId);

        // Retrieve and process leave request history records
        $addQuotas = Leave_request_history::where('leave_request_id', $leaveRequestId)
            ->where('req_by', $leaveRequest->req_by)
            ->get();

        foreach ($addQuotas as $addQuota) {
            $returnQuota = Emp_leave_quota::findOrFail($addQuota->emp_leave_quota_id);

            // Update quota values
            $returnQuota->quota_used -= $addQuota->quota_used;
            $returnQuota->quota_left += $addQuota->quota_used;
            $returnQuota->save();
        }

        // Delete leave request history records
        Leave_request_history::where('leave_request_id', $leaveRequestId)
            ->where('req_by', $leaveRequest->req_by)
            ->delete();

        // Create a notification entry for the user
        $entry = new Notification_alert();
        $entry->user_id = $leaveRequest->req_by;
        $entry->message = "Your Leave Request has been rejected!";
        $entry->importance = 404;
        $entry->save();

        // Redirect back with a message
        return redirect('/approval/leave')->with('failed', "You rejected the leave request!");
    }
}
