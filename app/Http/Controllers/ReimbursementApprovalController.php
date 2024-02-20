<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyReimbursementApproved;
use App\Jobs\NotifyReimbursementCreation;
use App\Jobs\NotifyReimbursementPartiallyApproved;
use App\Jobs\NotifyReimbursementPriorApproval;
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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReimbursementApprovalController extends Controller
{
    public function reimbursement_approval($yearSelected = null)
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

        //code above is to check if those with id 40,45,55,60 are able to approve or not
        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60,28])->pluck('approver')->toArray();
        // var_dump($checkUserPost);
        // Check if the current day is within the range 5-8
        if ($currentDay >= 1 && $currentDay <= 31) {
            //should add more position
                if (in_array($checkUserPost, [8, 12, 6, 22])) {
                    $Check = DB::table('reimbursement_approval')
                    ->select('reimb_item_id')
                    ->whereNotIn('RequestTo', $ts_approver)
                    ->whereNotIn('RequestTo', [Auth::id()])
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 30 THEN 1 ELSE 0 END)')
                    ->groupBy('reimb_item_id')
                    ->pluck('reimb_item_id')
                    ->toArray();
                    if (!empty($Check)) {
                        $approvals = Reimbursement_approval::select('*')
                        ->whereYear('created_at', $currentYear)
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
                        ->whereYear('created_at', $currentYear)
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
            return view('approval.reimbursement.reimbursement_approval', ['approvals' => $approvals, 'yearsBefore' => $yearsBefore, 'yearSelected' => $yearSelected]);
        } else {
            // Handle the case when the date is not within the range
            return redirect()->back()->with('failed', 'Today this page has been restricted, try again later...');
        }
    }

    public function view_details($id)
    {
        $reimbursement = Reimbursement::find($id);
        // if($reimbursement->status_id == 29){
        //     return redirect('approval/reimburse/');
        // }
        $checkUserPost = Auth::user()->users_detail->position->id;
        $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60,28])->pluck('approver')->toArray();

        if (in_array($checkUserPost, [8, 12, 6, 22])) {
            $Check = DB::table('reimbursement_approval')
            ->select('reimb_item_id')
            ->whereNotIn('RequestTo', $ts_approver)
            ->whereNotIn('RequestTo', [Auth::id()])
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 30 THEN 1 ELSE 0 END)')
            ->groupBy('reimb_item_id')
            ->pluck('reimb_item_id')
            ->toArray();
            if (!empty($Check)) {
                $reimbursement_items = Reimbursement_approval::whereIn('reimb_item_id', $Check)->where('reimbursement_id', $id)->where('RequestTo', Auth::id())->groupBy('reimb_item_id')->get();
            } else {
                $reimbursement_items = DB::table('reimbursement_approval')
                    ->select('*')
                    ->where('RequestTo', 'xxhaekalsastraxx')
                    ->get();
                Session::flash('warning',"The reimbursement items in this request is not approved yet by initial approver, please wait!");
            }
        } else {
            $Check = DB::table('reimbursement_approval')
            ->select('reimb_item_id')
            ->whereNotIn('RequestTo', [Auth::id()])
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 404 THEN 0 ELSE 1 END)')
            ->groupBy('reimb_item_id')
            ->pluck('reimb_item_id')
            ->toArray();
            if($Check){
                $reimbursement_items = Reimbursement_approval::whereIn('reimb_item_id', $Check)->where('reimbursement_id', $id)->where('RequestTo', Auth::id())->groupBy('reimb_item_id')->get();
            } else {
                $reimbursement_items = DB::table('reimbursement_approval')
                    ->select('*')
                    ->where('RequestTo', 'xxhaekalsastraxx')
                    ->get();
            }
        }

        $f_id = $reimbursement->f_id;
        $reimbursement_items_count = Reimbursement_approval::where('reimbursement_id', $id)
            ->where('status', 20)
            ->where('RequestTo', Auth::id())
            ->groupBy('reimb_item_id')
            ->count();

        $emp = User::all();
        $financeManager = Timesheet_approver::find(15);
        $reimbursement_approval = Reimbursement_approval::where('reimbursement_id', $id)->groupBy('RequestTo')->get();

        return view('approval.reimbursement.view_details', ['reimbursement' => $reimbursement, 'reimbursement_items_count' => $reimbursement_items_count, 'reimbursement_approval' => $reimbursement_approval,'fm' => $financeManager, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
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
                case 29:
                case 30:
                    $status = '<i class="fas fa-check-circle" style="color: #005eff;"></i> Approved';
                    break;
                case 403;
                    $status = '—';
                    break;
                default:
                    $status = '<i class="fas fa-spinner fa-spin"></i> Waiting for Approval';
                break;
            }
            $data = [
                'RequestTo' => $as->user->name,
                'status' => $status,
                'approved_amount' => $as->approved_amount,
                'notes' => $as->notes,
                'updated_at' => $as->updated_at->format('d-m-Y')
            ];
            $array[] = $data;
        }

        return response()->json($array);
    }

    public function approvalFlow($id)
    {
        $approverList = Reimbursement_approval::where('reimb_item_id', $id)->whereIn('status', [30,29,404])->groupBy('RequestTo')->get();

        $array = [];

        foreach ($approverList as $as) {
            switch ($as->status){
                case 404:
                    $status = '<i class="fas fa-times-circle" style="color: #ff0000;"></i> Rejected';
                    break;
                case 29:
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
                'approved_amount' => $as->approved_amount,
                'notes' => $as->notes,
                'updated_at' => $as->updated_at
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

        if (empty($item)) {
            return redirect()->back()->with('error', 'Item not found');
        }

        $reimbursement = Reimbursement::find($item->reimbursement_id);
        $userName = Auth::user()->name;
        $employees = User::where('id', $reimbursement->f_req_by)->get();

        // Check if the formId is all or not // whereNotIn('reimb_item_id', [$item->id])
        $checkRowsLeft = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)
            ->whereIn('status', [20])
            ->count();

        $checkEveryApprover = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)
            ->where('RequestTo', Auth::id())
            ->whereIn('status', [20])
            ->count();

        $changeStatus = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)
            ->whereNotIn('RequestTo', [Auth::id()])
            ->whereIn('status', [20])
            ->count();

        $checkItemLeft = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)
            ->where('reimb_item_id', $item_id)
            ->whereIn('status', [20])
            ->count();

        $grantedFunds = Reimbursement_approval::where('reimb_item_id', $item_id)
            ->whereNotNull('approved_amount')
            ->whereNotIn('approved_amount', [0])
            ->orderBy('updated_at', 'desc')
            ->first();


        $rawAmount = $request->approved_amount;
        if (!empty($request->approved_amount)) {
            $numericAmount = (float) preg_replace('/[^0-9]/', '', $rawAmount);
            $approved_amount = number_format($numericAmount);
        } elseif (!empty($grantedFunds) && $grantedFunds->approved_amount !== 0) {
            $approved_amount = $grantedFunds->approved_amount;
        } else {
            $approved_amount = $item->amount;
        }

        $item->approved_amount = $approved_amount;

        if ($checkItemLeft == 1) {
            $item->save();
        }
        $statusToUpdate = $changeStatus ? 30 : 29;

        $updateApprovals = Reimbursement_approval::where('reimb_item_id', $item->id)
            ->where('RequestTo', Auth::user()->id);

        $updateApprovals->update([
            'status' => $statusToUpdate,
            'notes' => $request->approval_notes,
            'approved_amount' => $approved_amount
        ]);

        $formApproval = Reimbursement_approval::where('reimb_item_id', $item->id)
        ->where('RequestTo', Auth::user()->id)->first();

        $totalApprovedAmount = 0;
        $reimbIds = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)->whereIn('status', [404, 20, 403])->groupBy('reimb_item_id')->pluck('reimb_item_id')->toArray();
        $result = Reimbursement_item::where('reimbursement_id', $item->reimbursement_id)->whereNotIn('id', $reimbIds)->get();
        foreach ($result as $item) {
            // Remove the comma and convert the string to a float
            $approvedAmount = floatval(str_replace([',','.'], '', $item->approved_amount));

            // Add the numeric value to the totalApprovedAmount
            $totalApprovedAmount += $approvedAmount;
        }

        if ($reimbursement && $item) {
            // Send mail
            foreach ($employees as $employee) {
                if ($checkRowsLeft == 1) {
                    Reimbursement::where('id', $item->reimbursement_id)->update(['status_id' => $statusToUpdate, 'f_granted_funds' => $totalApprovedAmount, 'f_sign_prior_approver' => Auth::user()->name, 'f_sign_prior_approver_date' => date('Y-m-d')]);
                    //send email
                    $notification = new NotifyReimbursementApproved($employee, $reimbursement);
                    dispatch($notification);
                } else if ($checkEveryApprover == 1){
                    //dispatch to user
                    $notification = new NotifyReimbursementPartiallyApproved($employee, $formApproval);
                    dispatch($notification);
                }
            }
        }
        //Send to next Approver
        if($checkEveryApprover == 1){
            //dispatch to approver
            $checkApprovalRowsLeft = Reimbursement_approval::whereNotIn('RequestTo', [Auth::id()])
            ->where('reimbursement_id', $item->reimbursement_id)
            ->whereIn('status', [20])
            ->groupBy('RequestTo')
            ->pluck('RequestTo')->toArray();

            $employees = User::whereIn('id', $checkApprovalRowsLeft)->get();

            foreach ($employees as $employee) {
                dispatch(new NotifyReimbursementPriorApproval($employee, $formApproval));
            }
        }


        // Notification
        $entry = new Notification_alert();
        $entry->user_id = $reimbursement->f_req_by;
        $entry->message = "-";
        $entry->importance = 1;
        list($year, $month) = explode('-', substr($reimbursement->created_at, 0, 7));
        $yearAndMonth = $year . intval($month);
        $entry->month_periode = $yearAndMonth;
        $entry->type = 5;
        $entry->save();

        return redirect()->back()->with('success', 'You approved the leave request!');
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

        // Check if all items are rejected
        $reimbItem = Reimbursement_item::find($item_id);

        // Update reimbursement approvals
        Reimbursement_approval::where('reimb_item_id', $item_id)->where('RequestTo', Auth::id())
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->update(['status' => 404, 'notes' => $request->approval_notes]);
        //Set the other to 403
        Reimbursement_approval::where('reimb_item_id', $item_id)->whereNotIn('RequestTo', [Auth::id()])->where('status', 20)
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->update(['status' => 403]);

        $checkRowsLeft = Reimbursement_approval::whereNotIn('reimb_item_id', [$reimbItem->id])
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->whereIn('status', [20, 30, 29])
            ->count();

        // Check if the formId is all or not // whereNotIn('reimb_item_id', [$item->id])
        $checkRowsLefttoCompleting = Reimbursement_approval::whereNotIn('reimb_item_id', [$reimbItem->id])
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->whereIn('status', [404, 20, 403])
            ->count();

        $totalApprovedAmount = 0;
        $reimbIds = Reimbursement_approval::where('reimbursement_id', $reimbItem->reimbursement_id)->whereIn('status', [404, 20, 403])->groupBy('reimb_item_id')->pluck('reimb_item_id')->toArray();
        $result = Reimbursement_item::where('reimbursement_id', $reimbItem->reimbursement_id)->whereNotIn('id', $reimbIds)->get();
        foreach ($result as $item) {
            // Remove the comma and convert the string to a float
            $approvedAmount = floatval(str_replace([',','.'], '', $item->approved_amount));

            // Add the numeric value to the totalApprovedAmount
            $totalApprovedAmount += $approvedAmount;
        }

        // Update reimbursement request status if all items are rejected
        if ($checkRowsLeft === 0) {
            Reimbursement::where('id', function ($query) use ($item_id) {
                $query->select('reimbursement_id')->from('reimbursement_items')->where('id', $item_id);
            })->update(['status_id' => 404]);
        } elseif ($checkRowsLefttoCompleting === 0) {
            Reimbursement::where('id', function ($query) use ($item_id) {
                $query->select('reimbursement_id')->from('reimbursement_items')->where('id', $item_id);
            })->update(['status_id' => 29, 'f_granted_funds' => $totalApprovedAmount, 'f_sign_prior_approver' => Auth::user()->name, 'f_sign_prior_approver_date' => date('Y-m-d')]);
        }

        // Notify the user who requested the reimbursement
        $reimbReq = Reimbursement::whereHas('items', function ($query) use ($item_id) {
            $query->where('id', $item_id);
        })->first();

        //Mailer
        $employee = User::find($reimbReq->f_req_by);
        $formApproval = Reimbursement_approval::where('reimb_item_id', $item_id)->where('RequestTo', Auth::id())->first();
        dispatch(new NotifyReimbursementRejected($employee, $formApproval));

        return response()->json(['success' => 'Items rejected successfully.']);
    }
}
