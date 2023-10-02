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
            'date_prepared' => 'required',
    		'po_req_number' => 'required',
            'req_by' => 'required',
            'ppnSel' => 'required',
            'buyer' => 'required',
            'assignment_cost' => 'required',
            'vendor' => 'required',
            'vendor_address' => 'required',
            'vendor_email' => 'required',
            'vendor_phone' => 'required',
            'purpose_of_purchase' => 'required',
            'top' => 'required',
            'unitType' => 'required',
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
            'f_int_order_num' => '99',
            'f_requisition_num' => $request->po_req_number,
            'f_purpose_of_purchase' => $request->purpose_of_purchase,
            'f_buyer' => $request->buyer,
            'f_vendor' => $request->vendor,
            'f_vendor_address' => $request->vendor_address,
            'f_vendor_phone' => $request->vendor_phone,
            'f_vendor_email' => $request->vendor_email,
            'f_assignment_cost' => $request->assignment_cost,
            'ppn_status' => $request->ppnSel,
            'status_id' => 29,
            'f_unit_type' => $request->unitType,
            'f_top' => $request->top,
            'f_type' => 'PO',
            'f_date_created_at' => $request->date_prepared,
            'created_by' => Auth::user()->email,
            'important_notes' => $request->notes
    	]);

        $item_to_purchase = $request->input('item_to_purchase');
        $unit = $request->input('unit');
        $price = $request->input('price');
        $result = $request->input('result');

        // Validate the form data
        $data = $request->validate([
            'item_to_purchase.*' => 'required',
            'unit.*' => 'required',
        ]);

        // Count the number of items in the arrays
        $num_items = count($data['item_to_purchase']);
        $num_units = count($data['unit']);

        if ($num_items == $num_units) {
            for ($i = 0; $i < count($item_to_purchase); $i++) {
                $data = new Reimbursement_item;
                $data->no_item = $i+1;
                $data->description = $item_to_purchase[$i];
                $data->amount = $unit[$i];
                $data->price = $price[$i];
                $data->total = $result[$i];
                $data->po_form_id = $uniqueId;
                $data->save();
            }
            $po_number = Po_form::whereNull('deleted_at')->orderBy('f_requisition_num', 'desc')->pluck('f_id')->first();

            Session::flash('success',"Purchase Order #$po_number Has Been Created!");
            return redirect('/myform');
        } else {
            Session::flash('failed',"Error Database has Occured! Failed to create purchase order!");
            return redirect('/myform');
        }    
    }
>>>>>>> d332f228c6cdc499c51ce82d36f4c4e81fb5347f
}
