<?php

namespace App\Http\Controllers;

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
        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60,15])->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 1 && $currentDay <= 31) {
                if (in_array($checkUserPost, [7, 8, 12, 10, 6, 22])) {
                    $Check = DB::table('reimbursement_approval')
                        ->select('reimbursement_id')
                        ->whereNotIn('RequestTo', $ts_approver)
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 30 THEN 1 WHEN status = 404 THEN 0 ELSE 0 END)')
                        ->groupBy('reimbursement_id', 'RequestTo')
                        ->pluck('reimbursement_id')
                        ->toArray();
                        if (!empty($Check)) {
                            $approvals = Reimbursement_approval::select('*')
                            // ->whereYear('date_submitted', $currentYear)
                            ->where('RequestTo', Auth::user()->id)
                            ->whereNotIn('status', [29, 404])
                            ->whereIn('reimbursement_id', $Check)
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
                        ->select('reimbursement_id')
                        ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 404 THEN 0 ELSE 1 END)')
                        ->groupBy('reimbursement_id', 'RequestTo')
                        ->pluck('reimbursement_id')
                        ->toArray();
                    if (!empty($Check)) {
                        $approvals = reimbursement_approval::select('*')
                        // ->whereYear('date_submitted', $currentYear)
                        ->where('RequestTo', Auth::user()->id)
                        ->whereNotIn('status', [29, 404])
                        ->whereIn('reimbursement_id', $Check)
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
            if ($as->status_id == (40||20)) {
                $btnApprove = '<div class="col-auto">
                    <a href="/approval/project/assignment/approve/' . $id . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Submit
                    </a>
                </div>';
                $status = "Waiting for Approval";
            } elseif ($as->status_id == 29) {
                $btnApprove = "";
                $status = "Approved";
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

        return view('approval.reimbursement.view_details', ['reimbursement' => $reimbursement, 'reimbursement_approval' => $reimbursement_approval, 'btnApprove' => $btnApprove, 'stat' => $status, 'fm' => $financeManager, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }

    public function listApprover($id)
    {
        $approverList = Reimbursement_approval::where('reimb_item_id', $id)->groupBy('RequestTo')->get();
        return response()->json($approverList);
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

        $financeManager = Timesheet_approver::find(15);
        $item = Reimbursement_item::find($item_id);
        $statNum = 30;

        if(Auth::id() == $financeManager->approver){
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

        return response()->json(['success' => 'Items rejected successfully.']);
    }
}
