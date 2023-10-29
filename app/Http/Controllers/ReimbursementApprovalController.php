<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyReimbursementApproved;
use App\Jobs\NotifyReimbursementRejected;
use App\Models\Notification_alert;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\Timesheet_approver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReimbursementApprovalController extends Controller
{
    public function reimbursement_approval()
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
                if (in_array($checkUserPost, [7, 8, 12, 10, 6, 22])) {
                    $Check = DB::table('reimbursement_approval')
                    ->select('reimb_item_id')
                    ->whereNotIn('RequestTo', $ts_approver)
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 30 THEN 1 WHEN status = 404 THEN 0 ELSE 0 END)')
                    ->groupBy('reimb_item_id')
                    ->pluck('reimb_item_id')
                    ->toArray();
                    if (!empty($Check)) {
                        $approvals = Reimbursement_approval::select('*')
                        // ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->whereNotIn('status', [29, 30, 404])
                        ->whereIn('reimb_item_id', $Check)
                        ->groupBy('reimbursement_id')
                        ->get();
                    } else {
                        $approvals = DB::table('reimbursement_approval')
                            ->select('*')
                            ->where('RequestTo', 'xxhaekalsastraxx')
                            ->get();
                    }
                } else {
                    $Check = DB::table('reimbursement_approval')
                        ->select('reimb_item_id')
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 404 THEN 0 ELSE 1 END)')
                        ->groupBy('reimb_item_id')
                        ->pluck('reimb_item_id')
                        ->toArray();
                    if (!empty($Check)) {
                        $approvals = reimbursement_approval::select('*')
                        // ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->whereNotIn('status', [29, 30, 404])
                        ->whereIn('reimb_item_id', $Check)
                        ->groupBy('reimbursement_id')
                        ->get();
                    } else {
                        $approvals = DB::table('reimbursement_approval')
                            ->select('*')
                            ->where('RequestTo', 'xxhaekalsastraxx')
                            ->get();
                    }
                }
            return view('approval.reimbursement.reimbursement_approval', ['approvals' => $approvals]);
        } else {
            // Handle the case when the date is not within the range
            return redirect()->back()->with('failed', 'Today this page has been restricted, try again later...');
        }
    }

    public function view_details($id)
    {
        $reimbursement = Reimbursement::where('id', $id)->get();

        foreach ($reimbursement as $as) {
            if ($as->status_id == 20) {
                $status = "Waiting for Approval";
            } elseif ($as->status_id == 29) {
                $btnApprove = "";
                $status = "Approved";
            } elseif ($as->status_id == 2002) {
                $status = "Paid";
            } elseif ($as->status_id == 404) {
                $btnApprove = "";
                $status = "Rejected";
            } else {
                $btnApprove = "";
                $status = "Unknown Status";
            }
            $f_id = $as->f_id;
        }
        
        $emp = User::all();
        $financeManager = Timesheet_approver::find(15);

        $reimbursement_items = Reimbursement_approval::where('reimbursement_id', $id)->where('RequestTo', Auth::id())->groupBy('reimb_item_id')->get();
        $reimbursement_approval = Reimbursement_approval::where('reimbursement_id', $id)->groupBy('RequestTo')->get();

        return view('approval.reimbursement.view_details', ['reimbursement' => $reimbursement, 'reimbursement_approval' => $reimbursement_approval, 'stat' => $status, 'fm' => $financeManager, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }

    public function listApprover($id)
    {
        $approverList = Reimbursement_approval::where('reimb_item_id', $id)->groupBy('RequestTo')->get();

        $array = [];

        foreach ($approverList as $as) {
            switch ($as->status){
                case 404:
                    $status = '<i class="fas fa-times-circle" style="color: #ff0000;"></i> Rejected';
                    break;
                case 30:
                    $status = '<i class="fas fa-check-circle" style="color: #005eff;"></i> Approved';
                    break;
                default:
                    $status = '<i class="fas fa-spinner fa-spin"></i> Waiting for Approval';
                break;
            }
            $data = [
                'RequestTo' => $as->user->name,
                'status' => $status,
                'notes' => $as->notes,
            ];
            $array[] = $data;
        }

        return response()->json($array);
    }

    public function approve(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'approved_amount' => 'sometimes',
            'approval_notes' => 'sometimes',
            'current_amount' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Reimbursement_item::find($item_id);
        $statNum = 30;
        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60])->pluck('approver')->toArray();

        if(in_array(Auth::id(), $ts_approver)){
            $statNum = 29;
            if (!empty($request->approved_amount)) {
                //formatting amount
                $rawAmount = $request->approved_amount;
                $numericAmount = preg_replace('/[^0-9]/', '', $rawAmount);
                $formattedAmount = number_format($numericAmount);

                $item->approved_amount = $formattedAmount;
            } else {
                $item->approved_amount = $request->current_amount;
            }
            $reimb_req = Reimbursement::find($item->reimbursement_id);
            $reimb_req->status_id = 29;
            $reimb_req->save();

            $entry = new Notification_alert();
            $entry->user_id = $reimb_req->f_req_by;
            $entry->message = "-";
            $entry->importance = 1;
            list($year, $month) = explode('-', substr($reimb_req->created_at, 0, 7));
            $yearAndMonth = $year . intval($month);
            $entry->month_periode = $yearAndMonth;
            $entry->type = 5;
            $entry->save();
            
            $employees = User::where('id', $reimb_req->f_req_by)->get();
            $userName = Auth::user()->name;

            foreach ($employees as $employee) {
                dispatch(new NotifyReimbursementApproved($employee, $userName));
            }
        }
        $item->save();

        $approvalItem = Reimbursement_approval::where('reimb_item_id', $item_id)
                        ->where('RequestTo', Auth::user()->id)
                        ->update(['status' => $statNum, 'notes' => $request->approval_notes]);

        return redirect()->back()->with('success',"You approved the leave request!");
    }

    public function reject(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'approved_amount' => 'sometimes',
            'approval_notes' => 'sometimes',
            'current_amount' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $approvalItem = Reimbursement_approval::where('reimb_item_id', $item_id)
                        ->where('RequestTo', Auth::user()->id)
                        ->update(['status' => '404', 'notes' => $request->approval_notes]);

        $reimbItem = Reimbursement_item::find($item_id);
        $reimbReq = Reimbursement::find($reimbItem->reimbursement_id);
        $reimbReq->status_id = 404;
        $reimbReq->save();
        $employees = User::where('id',$reimbReq->f_req_by)->get();
        $reimbId = $reimbReq->id;

        foreach($employees as $employee){
            dispatch(new NotifyReimbursementRejected($employee, $reimbId));
        }
        
        return response()->json(['success' => 'Items rejected successfully.']);
    }
}
