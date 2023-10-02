<?php

namespace App\Http\Controllers;

use App\Models\Company_project;
use App\Models\Department;
use App\Models\Reimbursement;
use App\Models\Reimbursement_item;
use App\Models\Timesheet_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReimburseController extends Controller
{
    public function history($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if($yearSelected){
            $currentYear = $yearSelected;
        }

        $reimbursement = Reimbursement::where('f_req_by', Auth::id())->get();
        return view('reimbursement.history', compact('reimbursement', 'yearsBefore', 'yearSelected'));
    }

    public function create_request($yearSelected = null)
    {
        $projects = Company_project::all();
        $approver = Department::all();
        
        return view('reimbursement.request', compact('projects', 'approver'));
    }

    public function submit_request(Request $request)
    {
        $this->validate($request,[
            'type_reimburse' => 'required',
    		'payment_method' => 'required',
            'project' => 'required',
            'approver' => 'required',
            'purposes' => 'required',
            'notes' => 'required'
    	]);

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (Reimbursement::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        Reimbursement::create([
            'id' => $uniqueId,
    		'f_id' => 450000000 + intval($request->po_req_number),
    		'f_req_by' => $request->req_by,
            'f_purpose_of_purchase' => $request->purposes,
            'status_id' => 29,
            'f_top' => $request->top,
            'f_type' => 'Reimbursement'
    	]);

        $receipt = $request->input('receipt');
        $description = $request->input('description');
        $amount = $request->input('amount');

        // Validate the form data
        $data = $request->validate([
            'receipt.*' => 'required',
            'description.*' => 'required',
        ]);

        // Count the number of items in the arrays
        $num_items = count($data['receipt']);
        $num_units = count($data['description']);

        if ($num_items == $num_units) {
            for ($i = 0; $i < count($receipt); $i++) {
                $data = new Reimbursement_item;
                $data->receipt_file = $receipt[$i];
                $data->description = $description[$i];
                $data->amount = $amount[$i];
                $data->reimbursement_id = $uniqueId;
                $data->save();
            }
            $form_number = Reimbursement::whereNull('deleted_at')->orderBy('f_requisition_num', 'desc')->pluck('f_id')->first();

            Session::flash('success',"Purchase Order #$form_number Has Been Created!");
            return redirect('/myform');
        } else {
            Session::flash('failed',"Error Database has Occured! Failed to create purchase order!");
            return redirect('/myform');
        }    
    }
}
