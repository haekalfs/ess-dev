<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyChangesReimbursementbyFinance;
use App\Jobs\NotifyReimburseCancelRequest;
use App\Jobs\NotifyReimbursementCreation;
use App\Jobs\NotifyReimbursementPaid;
use App\Jobs\SendDisbursementOrder;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Company_project;
use App\Models\Department;
use App\Models\Financial_password;
use App\Models\Notification_alert;
use App\Models\Position;
use App\Models\Project_assignment_user;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\Timesheet_approver;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Setting;
use App\Models\Usr_role;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use ZipArchive;

use function PHPUnit\Framework\isEmpty;

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

        $reimbursement = Reimbursement::where('f_req_by', Auth::id())->whereYear('created_at', $currentYear)->get();
        return view('reimbursement.history', compact('reimbursement', 'yearsBefore', 'yearSelected'));
    }

    public function create_request($yearSelected = null)
    {
        $getProject = Project_assignment_user::where('user_id', Auth::id())->get();
        $approver = Timesheet_approver::whereIn('id', [40, 45, 55, 60, 28])->get();

        $existingID = Reimbursement::whereNull('deleted_at')->orderBy('f_id', 'desc')->pluck('f_id')->first();
        $nextID = $existingID + 1;

        return view('reimbursement.request', compact('getProject', 'approver', 'nextID'));
    }

    public function submit_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'project' => 'required',
            'notes' => 'sometimes'
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect('/reimbursement/create/request');
        }

        $nowYear = date('Y');
        $project = $request->project;

        $UserDept = Auth::user()->users_detail->department;

        //for validation
        $isManager = Timesheet_approver::where('group_id', 2)
            ->pluck('approver')->toArray();

        //for status
        $checkUserPost = Auth::user()->users_detail->position->id;
        $getHighPosition = Position::where('position_level', 1)->pluck('id')->toArray();

        $statusId = in_array($checkUserPost, $getHighPosition) ? 29 : 20;

        //PM roles approvals
        $companyProjectId = (empty(Company_project::find($project))) ? $project : Company_project::find($project)->id;
        $usersWithPMRole = Project_assignment_user::where('company_project_id', $companyProjectId)
        ->where('periode_end', '>=', date('Y-m-d'))
        ->where('role', 'PM')
        ->get();

        //form Type
        $typeOfReimbursement = (empty(Company_project::find($project))) ? $project : Company_project::find($project)->project_name;

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (Reimbursement::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        $existingID = Reimbursement::whereNull('deleted_at')->orderBy('f_id', 'desc')->whereYear('created_at', $nowYear)->pluck('f_id')->first();
        if($existingID){
            $nextID = $existingID + 1;
        } else {
            $nextID = 440000000;
        }

        Reimbursement::create([
            'id' => $uniqueId,
    		'f_id' => $nextID,
    		'f_req_by' => Auth::id(),
            'status_id' => $statusId,
            'f_payment_method' => $request->payment_method,
            'f_type' => $typeOfReimbursement,
            'f_approver' => $UserDept->department_name,
            'f_sign_employee' => Auth::user()->name,
            'f_sign_employee_date' => date('Y-m-d'),
            'notes' => $request->notes
    	]);

        $receipt = $request->input('receipt');
        $description = $request->input('description');
        $expiration = $request->input('expiration');
        $amount = $request->input('amount');

        // Validate the form data
        $data = $request->validate([
            'receipt.*' => 'required|file|mimes:pdf,doc,docx,jpg,png,jpeg|max:10048',
            'description.*' => 'required',
            'expiration.*' => 'required',
        ]);

        // Count the number of items in the arrays
        $num_items = count($data['receipt']);
        $num_units = count($data['description']);

        // Initialize an array to store file names
        $uploadedFileNames = [];
        $filePathArray = [];

        if ($request->hasFile('receipt')) {
            $files = $request->file('receipt');

            foreach ($files as $file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $upload_folder = public_path('reimbursement/');
                $filePath = 'reimbursement/' . $fileName;

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                // Add the generated file name to the array
                $uploadedFileNames[] = $fileName;
                $filePathArray[] = $filePath;
            }
        }

        if (count($uploadedFileNames) == count($description) && count($uploadedFileNames) == count($expiration) && count($uploadedFileNames) == count($amount)) {
            for ($i = 0; $i < count($uploadedFileNames); $i++) {

                $itemId = hexdec(substr(uniqid(), 0, 8));
                while (Reimbursement_item::where('id', $itemId)->exists()) {
                    $itemId = hexdec(substr(uniqid(), 0, 8));
                }

                $data = new Reimbursement_item;
                $data->id = $itemId;
                $data->receipt_file = $uploadedFileNames[$i]; // Use the generated file name
                $data->file_path = $filePathArray[$i];
                $data->description = $description[$i];
                $data->receipt_expiration = $expiration[$i];
                $data->amount = $amount[$i];
                $data->reimbursement_id = $uniqueId;
                $data->save();

                $userToApprove = [];

                if ($usersWithPMRole->isNotEmpty()) {
                    foreach ($usersWithPMRole as $approverPm) {
                        if(in_array(Auth::id(), $isManager) || in_array($checkUserPost, $getHighPosition) || Auth::id() == $approverPm->user_id){
                            break;
                        }
                        Reimbursement_approval::create([
                            'status' => $statusId,
                            'RequestTo' => $approverPm->user_id,
                            'reimb_item_id' => $itemId,
                            'reimbursement_id' => $uniqueId
                        ]);
                        $userToApprove[] = $approverPm->user_id;
                    }
                }
                //HARDCODE FOR ROLES
                $getFM = Usr_role::where('role_id', 7)->pluck('user_id')->toArray();
                $getHR = Usr_role::where('role_id', 3)->pluck('user_id')->toArray();
                $getPC = Usr_role::where('role_id', 12)->pluck('user_id')->first();

                if ($UserDept->id !== 2 && Company_project::where('id', $project)->exists()) {
                    // Create Reimbursement_approval record
                    Reimbursement_approval::create([
                        'status' => $statusId,
                        'RequestTo' => $getPC,
                        'reimb_item_id' => $itemId,
                        'reimbursement_id' => $uniqueId
                    ]);
                }
                switch (true) {
                    case ($UserDept->approvers->isNotEmpty()):
                        foreach ($UserDept->approvers as $approver) {
                            // Skip if the user is a manager and the approver is also a manager
                            if (in_array(Auth::id(), $isManager) && in_array($approver->approver, $isManager)) {
                                continue;
                            }

                            // Skip if the user is finance staff and the approver is HR
                            if (in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray()) && in_array($approver->approver, $getHR)) {
                                continue;
                            }

                            // Skip if the user is not finance staff and the approver is FM
                            if (!in_array('finance_staff', Auth::user()->role_id()->pluck('role_name')->toArray()) && in_array($approver->approver, $getFM)) {
                                continue;
                            }

                            // Create Reimbursement_approval record
                            Reimbursement_approval::create([
                                'status' => $statusId,
                                'RequestTo' => $approver->approver,
                                'reimb_item_id' => $itemId,
                                'reimbursement_id' => $uniqueId
                            ]);

                            $userToApprove[] = $approver->approver;
                        }
                        break; // Add break statement here to exit the switch block after executing the case
                    default:
                        return redirect()->back()->with('failed', "You haven't assigned to any department! Ask HR Dept to correcting your account details");
                    break;
                }
            }

            // //Delete Self-rows
            // Reimbursement_approval::where('RequestTo', Auth::id())->where('reimbursement_id', $uniqueId)->delete();
            $totalApprovedAmount = 0;

            if(in_array($checkUserPost, $getHighPosition)){
                Reimbursement_approval::whereNotIn('RequestTo', [Auth::id()])->where('reimbursement_id', $uniqueId)->delete();
                Reimbursement_approval::whereIn('RequestTo', [Auth::id()])->where('reimbursement_id', $uniqueId)->update(['status', 29]);
                $result = Reimbursement_item::where('reimbursement_id', $uniqueId)->get();
                foreach ($result as $item) {
                    // Remove the comma and convert the string to a float
                    $approvedAmount = floatval(str_replace([',','.'], '', $item->amount));

                    // Add the numeric value to the totalApprovedAmount
                    $totalApprovedAmount += $approvedAmount;
                }
                $findForm = Reimbursement::find($uniqueId)->update(['f_granted_funds' => $totalApprovedAmount]);
            }

            $employees = User::whereIn('id', $userToApprove)->get();
            $form = Reimbursement::find($uniqueId);

            foreach ($employees as $employee) {
                if(in_array($employee->id, $isManager)){
                    dispatch(new NotifyReimbursementCreation($employee, $form));
                }
            }
            Session::flash('success',"Request has been submitted! You have to give a hard copies of the receipts to the finance department within 2 weeks");
            return redirect('/reimbursement/view/'. $uniqueId);
        } else {
            Session::flash('failed',"Error Database has Occured! Failed to create request!");
            return redirect('/reimbursement/history');
        }
    }

    public function view_details($id)
    {
        $reimbursement = Reimbursement::find($id);
        $f_id = $reimbursement->f_id;
        $emp = User::all();

        $reimbursement_items = Reimbursement_item::where('reimbursement_id', $id)->get();
        $reimbursement_approval = Reimbursement_approval::where('reimbursement_id', $id)->groupBy('RequestTo')->get();

        $isReceived = Reimbursement_item::select('id')
                    ->where('reimbursement_id', $id)
                    ->havingRaw('COUNT(*) = SUM(CASE WHEN receivable_receipt = 1 THEN 1 ELSE 0 END)')
                    ->groupBy('reimbursement_id')
                    ->pluck('id')
                    ->toArray();

        $diffInDays = $reimbursement->created_at->diffInDays(now());

        if ($diffInDays <= 14 && empty($isReceived)) {
            Session::flash('failed', "You have to submit all hard copies of the receipts to the finance department within 2 weeks, otherwise the reimbursement will not proceed!");
        } elseif ($diffInDays > 14 && $reimbursement->status_id == 404) {
            Session::flash('failed', "Reimbursement request has been rejected due to failure to submit the receipts on time!");
        }

        return view('reimbursement.view_details', ['reimbursement' => $reimbursement,'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }

    public function retrieveReimburseData($id)
    {
        // Get the Timesheet records between the start and end dates
        $itemData = Reimbursement_item::find($id);

        return response()->json($itemData);
    }

    public function retrieveReimburseDataApproval($id)
    {
        // Get the Timesheet records between the start and end dates
        $itemData = Reimbursement_item::find($id);

        // Fetch granted funds
        $grantedFunds = Reimbursement_approval::where('reimb_item_id', $id)->whereNotNull('approved_amount')->orderBy('updated_at', 'desc')->first();

        // Prepare data for response
        $responseData = [
            'itemData' => $itemData,
            'grantedFunds' => $grantedFunds ? $grantedFunds->approved_amount : null
        ];

        return response()->json($responseData);
    }

    public function updateReimburseData(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'receipt' => 'sometimes',
            'description' => 'sometimes',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Reimbursement_item::find($item_id);
        if (!empty($request->description)) {
            $item->description = $request->description;
        }
        $item->amount = $request->amount;

        if ($request->hasFile('receipt')) {

            // Delete the file from the public folder if it exists
            if ($item->exists()) {
                $filePath = public_path($item->file_path);

                // Delete the file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $file = $request->file('receipt');
            $receipt = $request->file('receipt');
            $fileExtension = $receipt->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $filePath = 'reimbursement/' . $fileName;
            $upload_folder = public_path('reimbursement/');

            // Move the uploaded file to the storage folder
            $file->move($upload_folder, $fileName);

            $item->receipt_file = $fileName;
            $item->file_path = $filePath;
        }
        $item->save();

        return response()->json(['success' => 'Item updated successfully.']);
    }

    public function confirmReceivableReceipt($id)
    {
        $item = Reimbursement_item::find($id);
        $item->receivable_receipt = TRUE;
        $item->save();

        return response()->json(['success' => 'Item updated successfully.']);
    }

    public function approveReimburseDataFinance(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'notes' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Reimbursement_item::find($item_id);
        $item->edited_by_finance = TRUE;
        $item->approved_amount = $request->amount;
        $item->save();

        $totalApprovedAmount = 0;
        $reimbIds = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)->whereIn('status', [404, 20, 403])->groupBy('reimb_item_id')->pluck('reimb_item_id')->toArray();
        $result = Reimbursement_item::where('reimbursement_id', $item->reimbursement_id)->whereNotIn('id', $reimbIds)->get();
        foreach ($result as $item) {
            // Remove the comma and convert the string to a float
            $approvedAmount = floatval(str_replace([',','.'], '', $item->approved_amount));

            // Add the numeric value to the totalApprovedAmount
            $totalApprovedAmount += $approvedAmount;
        }

        Reimbursement_approval::create([
            'reimb_item_id' => $item_id,
            'RequestTo' => Auth::id(),
            'status' => 29,
            'approved_amount' => $request->amount,
            'reimbursement_id' => $item->reimbursement_id,
            'notes' => $request->notes
        ]);

        $mainForm = Reimbursement::where('id', $item->reimbursement_id);
        $mainForm->update(['f_granted_funds' => $totalApprovedAmount]);

        $employees = User::where('id', $item->request->f_req_by)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifyChangesReimbursementbyFinance($employee, $item));
        }

        return redirect()->back()->with('success', 'You approved the leave request!');
    }

    public function rejectReimburseDataFinance(Request $request, $item_id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes',
            'notes' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $item = Reimbursement_item::find($item_id);
        $item->edited_by_finance = TRUE;
        $item->approved_amount = 0;
        $item->save();

        // Check if all items are rejected
        $reimbItem = Reimbursement_item::find($item_id);
        //Set the other to 403
        Reimbursement_approval::where('reimb_item_id', $item_id)
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->update(['status' => 403]);

        $Check = DB::table('reimbursement_approval')
            ->select('reimb_item_id')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 404 THEN 0 ELSE 1 END)')
            ->groupBy('reimb_item_id')
            ->pluck('reimb_item_id')
            ->toArray();

        $checkRowsLeft = Reimbursement_approval::whereIn('reimb_item_id', $Check)
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->whereIn('status', [20])
            ->count();

        // Check if the formId is all or not // whereNotIn('reimb_item_id', [$item->id])
        $checkRowsLefttoCompleting = Reimbursement_approval::whereNotIn('reimb_item_id', [$reimbItem->id])
            ->where('reimbursement_id', $reimbItem->reimbursement_id)
            ->whereIn('status', [404, 20, 403])
            ->count();

        $totalApprovedAmount = 0;
        $reimbIds = Reimbursement_approval::where('reimbursement_id', $item->reimbursement_id)->whereIn('status', [404, 20, 403])->groupBy('reimb_item_id')->pluck('reimb_item_id')->toArray();
        $result = Reimbursement_item::where('reimbursement_id', $item->reimbursement_id)->whereNotIn('id', $reimbIds)->get();
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
            })->update(['status_id' => 29, 'f_granted_funds' => $totalApprovedAmount]);
        }

        Reimbursement_approval::create([
            'reimb_item_id' => $item_id,
            'RequestTo' => Auth::id(),
            'status' => 404,
            'approved_amount' => 0,
            'reimbursement_id' => $item->reimbursement_id,
            'notes' => $request->notes
        ]);

        $mainForm = Reimbursement::where('id', $item->reimbursement_id);
        $mainForm->update(['f_granted_funds' => $totalApprovedAmount]);

        $employees = User::where('id', $item->request->f_req_by)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifyChangesReimbursementbyFinance($employee, $item));
        }

        return redirect()->back()->with('danger', 'You rejected the item!');
    }

    public function cancel_request(Request $request, $id)
	{
        $reimbRequest = Reimbursement::find($id);

        // Fetching IDs of users associated with the reimbursements
        $approvalIds = Reimbursement_approval::where('reimbursement_id', $id)->pluck('RequestTo');

        // Dispatching notification jobs to users
        User::whereIn('id', $approvalIds)->get()->each(function ($employee) use ($reimbRequest) {
            dispatch(new NotifyReimburseCancelRequest($employee, $reimbRequest->user->name));
        });

        if ($reimbRequest) {
            $fileReceipt = Reimbursement_item::where("reimbursement_id", $reimbRequest->id);

            // Delete the file from the public folder if it exists
            if ($fileReceipt->exists()) {
                $fileEntry = $fileReceipt->get();

                foreach($fileEntry as $fe){
                    $filePath = public_path($fe->file_path);

                    // Delete the file
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // Delete the Surat_penugasan entry
                $fileReceipt->delete();
            }

            // Delete the activity entry
            $reimbRequest->delete();
        }

        // Deleting reimbursement approvals
        Reimbursement_approval::where('reimbursement_id', $id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
	}

    public function previewPdf($id)
    {
        $selectedItem = Reimbursement_item::find($id);
        $pdfPath = public_path($selectedItem->file_path);
        return response()->file($pdfPath);
    }

    public function manage(Request $request){

        $Month = date('m');
        $Year = date('Y');
        $userId = Null;

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $employees = User::all();

        $validator = Validator::make($request->all(), [
            'showOpt' => 'required',
            'yearOpt' => 'required',
            'monthOpt' => 'required'
        ]);

        $month_periode = $Year . intval($Month);

        if ($validator->passes()) {
            $Year = $request->yearOpt;
            $Month = $request->monthOpt;
            $month_periode = $Year . intval($Month);
            $userId = $request->showOpt;

            if($userId == 1){
                $approvals = Reimbursement::whereIn('status_id', [29, 2002])
                ->whereYear('created_at', $Year)
                ->whereMonth('created_at', $Month)
                ->get();
            } else {
                $approvals = Reimbursement::whereIn('status_id', [29, 2002])
                ->whereYear('created_at', $Year)
                ->whereMonth('created_at', $Month)
                ->where('f_req_by', $userId)
                ->get();
            }
        } else {
            $approvals = Reimbursement::whereIn('status_id', [29, 2002])
            ->whereYear('created_at', $Year)
            ->whereMonth('created_at', $Month)
            ->get();
        }

        $getNotification = Notification_alert::where('type', 5)->whereNull('read_stat')->first();
        if($getNotification){
            $notifyMonth = substr($getNotification->month_periode, 4);
            $notify = $getNotification->id;
        } else {
            $notifyMonth = false;
            $notify = false;
        }

        $setToRead = Notification_alert::where('type', 5)
            ->whereNull('read_stat')
            ->where('month_periode', $month_periode)
            ->get();

        if ($setToRead->count() > 0) {
            Notification_alert::where('type', 5)
                ->whereNull('read_stat')
                ->where('month_periode', $month_periode)
                ->update(['read_stat' => 1]);
        }

        return view('reimbursement.manage.history', compact('approvals', 'userId', 'notify', 'notifyMonth', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function disbursed_item($formId)
    {
        // Ensure both arrays are not empty before proceeding
        if (!empty($formId)) {
            // Update records in the Reimbursement table where id is in the $formId array
            Reimbursement::where('id', $formId)->update(['status_id' => 2002, 'f_paid_on' => date('Y-m-d')]);

            $getForm = Reimbursement::find($formId);

            if ($getForm) {
                $employees = User::where('id', $getForm->f_req_by)->get();

                // Send mail
                foreach ($employees as $employee) {
                    dispatch(new NotifyReimbursementPaid($employee, $getForm));
                }
            }
        }

        return redirect()->back()->with('success', "Reimbursement has been marked to Paid! and system begin to sent notification to users");
    }

    public function disbursed_all(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formId' => 'required'
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect()->back();
        }

        //id
        $formId = explode(',', $request->input('formId'));
        $formId = array_filter($formId);

        // Ensure both arrays are not empty before proceeding
        if (!empty($formId)) {
            //loop the formId passed
            foreach($formId as $form){
                // Update records in the Reimbursement table where id is in the $formId array
                Reimbursement::whereIn('id', $formId)->update(['status_id' => 2002, 'f_paid_on' => date('Y-m-d')]);

                $getForm = Reimbursement::find($form);

                if ($getForm) {
                    $employees = User::where('id', $getForm->f_req_by)->get();

                    // Send mail
                    foreach ($employees as $employee) {
                        dispatch(new NotifyReimbursementPaid($employee, $getForm));
                    }
                }
            }
        }

        return redirect('/reimbursement/manage')->with('success', "Reimbursement has been marked to Paid! and system begin to sent notification to each users");
    }

    public function export_excel($Month, $Year)
	{
        $checkUserPost = Auth::user()->users_detail->position->id;

        //Tabel Setting Export Role
        $settingExport = Setting::where('id', 2)->first();
        $checkSettingExport = $settingExport->position_id;

        // Compare the hashed passwords
        if (in_array($checkUserPost, [10,9,21,22,23,8])) {
            $templatePath = public_path('template_reimbursement.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            // Set up the starting row and column for the data
            $startRow = 8;
            $startCol = 2;

            $checkApprovals = Reimbursement_approval::whereNotIn('status', [404, 20])
                ->groupBy('reimbursement_id')
                ->pluck('reimbursement_id')
                ->toArray();

            $approvedReimb = Reimbursement::whereIn('status_id', [29, 30])
                ->whereIn('id', $checkApprovals)
                ->whereYear('created_at', $Year)
                ->whereMonth('created_at', $Month)
                ->pluck('id')
                ->toArray();

            $result = Reimbursement_item::whereIn('reimbursement_id', $approvedReimb)->get();

            // Initialize the last printed user name
            $lastUser = '';
            $lastId = '';
            $firstRow = true; // Flag to check if it's the first row for each user
            $total = [];

            $no = 1;
            foreach ($result as $index => $row) {
                // Calculate the total mandays for each user
                if ($row->request->f_req_by !== $lastUser) {
                    $firstRow = true; // Reset the firstRow flag for a new user
                }

                // Define the URL (you can change this to any URL or route you want)
                $attachedFileUrl = url($row->file_path);

                // Set the text for the hyperlink (e.g., 'Attachment')
                $sheet->setCellValueByColumnAndRow($startCol + 7, $startRow, 'View File');

                // Add a hyperlink to the specific cell (e.g., cell in $startCol and $startRow)
                $sheet->getCellByColumnAndRow($startCol + 7, $startRow)
                    ->getHyperlink()
                    ->setUrl($attachedFileUrl);
                $sheet->getCellByColumnAndRow($startCol + 7, $startRow)
                    ->getHyperlink()
                    ->setTooltip('Click to download attachment');

                // Apply formatting to make the text underlined and blue
                $style = [
                    'font' => ['color' => ['rgb' => '0000FF'], 'underline' => true],
                ];

                $sheet->getStyleByColumnAndRow($startCol + 7, $startRow)->applyFromArray($style);

                // Print the user name if it is different from the last printed user name
                if ($row->request->f_req_by !== $lastUser) {
                    $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->request->user->name);
                    $sheet->setCellValueByColumnAndRow(1, $startRow, $row->request->user->users_detail->employee_id);

                    $total = [];
                    $lastUser = $row->request->f_req_by;
                }

                if ($row->request->f_id !== $lastId) {
                    $sheet->setCellValueByColumnAndRow($startCol + 1, $startRow, $row->request->f_id);
                    $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, $row->request->f_type);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, $row->request->f_payment_method);
                    $lastId = $row->request->f_id;
                    $no = 1;
                }

                $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow, $no.'. '.$row->description);
                $sheet->setCellValueByColumnAndRow($startCol + 5, $startRow, $row->amount);
                $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->approved_amount);

                $startRow++;
                $no++;
                $firstRow = false; // Set the firstRow flag to false after the first row for each user
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('app/public/output.xlsx'));
            // Download the file
            $filePath = storage_path('app/public/output.xlsx');

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            // Create a DateTime object using the year and month value
            $dateTime = DateTime::createFromFormat('m', $Month);

            // Get the month name
            $monthName = $dateTime->format('F');
            return response()->download($filePath, "$monthName-$Year.xlsx", $headers);
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function manage_view_details($id)
    {
        $reimbursement = Reimbursement::find($id);
        $f_id = $reimbursement->f_id;

        $approverRows = Reimbursement_approval::where('reimbursement_id', $id)
            ->groupBy('RequestTo')
            ->select('RequestTo')
            ->get(); // Fetch the rows from the database


        $isReceived = Reimbursement_item::select('id')
            ->where('reimbursement_id', $id)
            ->havingRaw('COUNT(*) = SUM(CASE WHEN receivable_receipt = 1 THEN 1 ELSE 0 END)')
            ->groupBy('id')
            ->pluck('id')
            ->count();

        $approversArrayName = [];
        foreach ($approverRows as $approver) {
            $approversArrayName[] = $approver->user->name;
        }

        $emp = User::all();
        $financeManager = Timesheet_approver::find(15);

        $reimbIds = Reimbursement_approval::where('reimbursement_id', $id)->whereIn('status', [404, 20, 403])->groupBy('reimb_item_id')->select('reimb_item_id')->pluck('reimb_item_id')->toArray();
        $reimbursement_items = Reimbursement_item::where('reimbursement_id', $id)->whereNotIn('id', $reimbIds)->get();

        return view('reimbursement.manage.manage_view_details', [ 'isReceived' => $isReceived,'reimbursement' => $reimbursement, 'approversArrayName' => $approversArrayName,'fm' => $financeManager, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }

    public function downloadReceipt($Id)
    {
        $getFile = Reimbursement_item::find($Id);
        $filePath = public_path($getFile->file_path);

        // Check if the file exists
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        // File not found
        abort(404);
    }

    public function export($formId)
    {
        $checkUserPost = Auth::user()->users_detail->position->id;

        //Tabel Setting Export Role
        $settingExport = Setting::where('id', 2)->first();
        $checkSettingExport = $settingExport->position_id;
        $totalApprovedAmount = 0;

        $mainForm = Reimbursement::find($formId);
        $approvalForm = Reimbursement_approval::where('reimbursement_id', $formId)
            ->whereIn('status', [404,20,403])
            ->groupBy('reimb_item_id')
            ->select('reimb_item_id') // Select the grouped column
            ->pluck('reimb_item_id') // Pluck the grouped column
            ->toArray();

        $getProjectCode = Company_project::where('project_name', $mainForm->f_type)->first();
        if($getProjectCode){
            $projectCode = $getProjectCode->project_code;
            $client = $getProjectCode->client->client_name;
        } else {
            $projectCode = 'N/a';
            $client = 'KIP';
        }
        // Compare the hashed passwords
        if (in_array($checkUserPost, [10,9,21,22,23,8])) {
            $templatePath = public_path('template_reimbursement_item.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            // Set up the starting row and column for the data
            $startRow = 16;
            $startCol = 2;

            $result = Reimbursement_item::where('reimbursement_id', $formId)->whereNotIn('id', $approvalForm)->get();

            $latestReceipt = Reimbursement_item::where('reimbursement_id', $formId)->orderBy('receipt_expiration', 'desc')->first();
            $receiptExpiration = $latestReceipt->receipt_expiration;
            $dateAfterTwoMonths = date('Y-m-d', strtotime($receiptExpiration . ' +2 months'));

            $ts_approver = Timesheet_approver::whereIn('id', [40,45,55,60,28])->pluck('approver')->toArray();

            $no = 1;
            foreach ($result as $item) {
                // Remove the comma and convert the string to a float
                $approvedAmount = floatval(str_replace([',','.'], '', $item->approved_amount));

                // Add the numeric value to the totalApprovedAmount
                $totalApprovedAmount += $approvedAmount;
            }

            foreach ($result as $index => $row) {

                // Define the URL (you can change this to any URL or route you want)
                $attachedFileUrl = url($row->file_path);

                // Set the text for the hyperlink (e.g., 'Attachment')
                $sheet->setCellValueByColumnAndRow(1, $startRow, 'View File');

                // Add a hyperlink to the specific cell (e.g., cell in $startCol and $startRow)
                $sheet->getCellByColumnAndRow(1, $startRow)
                    ->getHyperlink()
                    ->setUrl($attachedFileUrl);
                $sheet->getCellByColumnAndRow(1, $startRow)
                    ->getHyperlink()
                    ->setTooltip('Click to download attachment');

                // Apply formatting to make the text underlined and blue
                $style = [
                    'font' => ['color' => ['rgb' => '0000FF'], 'underline' => true],
                ];

                $sheet->getStyleByColumnAndRow(1, $startRow)->applyFromArray($style);
                $sheet->setCellValueByColumnAndRow($startCol + 3, 6, $latestReceipt->receipt_expiration);
                $sheet->setCellValueByColumnAndRow($startCol + 3, 7, $dateAfterTwoMonths);

                $sheet->setCellValueByColumnAndRow(3, 3, $row->request->user->name);
                $sheet->setCellValueByColumnAndRow(3, 4, $row->request->user->users_detail->employee_id);

                $uniqueApprovers = array_unique($mainForm->approval->pluck('RequestTo')->toArray());
                $commaDelimitedApprovers = implode(', ', $uniqueApprovers);
                $commaDelimitedApprovers = ucwords($commaDelimitedApprovers);
                $sheet->setCellValueByColumnAndRow(3, 6, $commaDelimitedApprovers);
                $sheet->setCellValueByColumnAndRow(3, 7, $row->request->dept->department_name);
                $sheet->setCellValueByColumnAndRow(3, 10, $row->request->f_type);
                $sheet->setCellValueByColumnAndRow($startCol + 2, 10, $projectCode);
                $sheet->setCellValueByColumnAndRow($startCol + 2, 11, $client);
                $sheet->setCellValueByColumnAndRow(3, 13, $row->request->f_id);

                $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->description);
                $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, str_replace([',','.'], '', $row->amount));
                $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, str_replace([',','.'], '', $row->approved_amount));

                $sheet->setCellValueByColumnAndRow($startCol + 3, 30, $totalApprovedAmount);
                $sheet->setCellValueByColumnAndRow($startCol + 3, 32, str_replace([',','.'], '', $mainForm->f_granted_funds));
                if($mainForm->status_id == 29){
                    $status = "All Approved";
                } else {
                    $status = "Paid";
                }
                $sheet->setCellValueByColumnAndRow($startCol + 3, 34, $mainForm->user->users_detail->usr_bank_name . " : " . $mainForm->user->users_detail->usr_bank_account);
                $sheet->setCellValueByColumnAndRow($startCol + 3, 37, $status);
                $sheet->setCellValueByColumnAndRow($startCol + 3, 40, $mainForm->f_paid_on);
                // $sheet->setCellValueByColumnAndRow(1, 40, $mainForm->f_sign_prior_approver);
                // $sheet->setCellValueByColumnAndRow(1, 37, $mainForm->f_sign_employee);

                $startRow++;
                $no++; // Set the firstRow flag to false after the first row for each user
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(storage_path('app/public/output.xlsx'));
            // Download the file
            $filePath = storage_path('app/public/output.xlsx');

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
            $fileName = $mainForm->f_id . '_' . $mainForm->f_type . '_' . $mainForm->user->id;
            return response()->download($filePath, "$fileName.xlsx", $headers);
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function export_selected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formId2' => 'required'
        ]);

        if ($validator->fails()) {
            Session::flash('failed', 'Error Database has Occurred! Failed to create request! You need to fill all the required fields');
            return redirect()->back();
        }

        $formIds = explode(',', $request->input('formId2'));

        // Create a unique name for the zip file
        $zipFileName = 'reimbursements_' . time() . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // Initialize a new ZipArchive instance
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($formIds as $formId) {
                // Fetch the reimbursement details for the current formId
                $totalApprovedAmount = 0;
                $mainForm = Reimbursement::find($formId);
                $approvalForm = Reimbursement_approval::where('reimbursement_id', $formId)
                    ->whereIn('status', [404,20,403])
                    ->groupBy('reimb_item_id')
                    ->select('reimb_item_id')
                    ->pluck('reimb_item_id')
                    ->toArray();

                $getProjectCode = Company_project::where('project_name', $mainForm->f_type)->first();
                if($getProjectCode){
                    $projectCode = $getProjectCode->project_code;
                    $client = $getProjectCode->client->client_name;
                } else {
                    $projectCode = 'N/a';
                    $client = 'KIP';
                }

                $templatePath = public_path('template_reimbursement_item.xlsx');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();
                $startRow = 16;
                $startCol = 2;

                $result = Reimbursement_item::where('reimbursement_id', $formId)->whereNotIn('id', $approvalForm)->get();
                $latestReceipt = Reimbursement_item::where('reimbursement_id', $formId)->orderBy('receipt_expiration', 'desc')->first();
                $receiptExpiration = $latestReceipt->receipt_expiration;
                $dateAfterTwoMonths = date('Y-m-d', strtotime($receiptExpiration . ' +2 months'));

                foreach ($result as $item) {
                    $approvedAmount = floatval(str_replace([',','.'], '', $item->approved_amount));
                    $totalApprovedAmount += $approvedAmount;
                }

                foreach ($result as $index => $row) {
                    $attachedFileUrl = url($row->file_path);
                    $sheet->setCellValueByColumnAndRow(1, $startRow, 'View File');
                    $sheet->getCellByColumnAndRow(1, $startRow)->getHyperlink()->setUrl($attachedFileUrl);
                    $sheet->getCellByColumnAndRow(1, $startRow)->getHyperlink()->setTooltip('Click to download attachment');

                    $style = [
                        'font' => ['color' => ['rgb' => '0000FF'], 'underline' => true],
                    ];

                    $sheet->getStyleByColumnAndRow(1, $startRow)->applyFromArray($style);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 6, $latestReceipt->receipt_expiration);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 7, $dateAfterTwoMonths);

                    $sheet->setCellValueByColumnAndRow(3, 3, $row->request->user->name);
                    $sheet->setCellValueByColumnAndRow(3, 4, $row->request->user->users_detail->employee_id);

                    $uniqueApprovers = array_unique($mainForm->approval->pluck('RequestTo')->toArray());
                    $commaDelimitedApprovers = implode(', ', $uniqueApprovers);
                    $commaDelimitedApprovers = ucwords($commaDelimitedApprovers);
                    $sheet->setCellValueByColumnAndRow(3, 6, $commaDelimitedApprovers);
                    $sheet->setCellValueByColumnAndRow(3, 7, $row->request->dept->department_name);
                    $sheet->setCellValueByColumnAndRow(3, 10, $row->request->f_type);
                    $sheet->setCellValueByColumnAndRow($startCol + 2, 10, $projectCode);
                    $sheet->setCellValueByColumnAndRow($startCol + 2, 11, $client);
                    $sheet->setCellValueByColumnAndRow(3, 13, $row->request->f_id);

                    $sheet->setCellValueByColumnAndRow($startCol, $startRow, $row->description);
                    $sheet->setCellValueByColumnAndRow($startCol + 2, $startRow, str_replace([',','.'], '', $row->amount));
                    $sheet->setCellValueByColumnAndRow($startCol + 3, $startRow, str_replace([',','.'], '', $row->approved_amount));

                    $sheet->setCellValueByColumnAndRow($startCol + 3, 30, $totalApprovedAmount);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 32, str_replace([',','.'], '', $mainForm->f_granted_funds));
                    $status = ($mainForm->status_id == 29) ? "All Approved" : "Paid";
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 34, $mainForm->user->users_detail->usr_bank_name . " : " . $mainForm->user->users_detail->usr_bank_account);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 37, $status);
                    $sheet->setCellValueByColumnAndRow($startCol + 3, 40, $mainForm->f_paid_on);

                    $startRow++;
                }

                // Generate a unique filename for the Excel file
                $excelFileName = 'reimbursement_' . $formId . '.xlsx';
                $excelFilePath = storage_path('app/public/' . $excelFileName);

                // Save the spreadsheet to a temporary file
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($excelFilePath);

                // Add the Excel file to the zip archive
                $zip->addFile($excelFilePath, $excelFileName);
            }

            // Close the zip archive
            $zip->close();

            // Set headers for file download
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
            ];

            // Return the response with the ZIP file as an attachment
            return response()->download($zipFilePath, $zipFileName, $headers);
        } else {
            Session::flash('failed', 'Failed to create zip file');
            return redirect()->back();
        }
    }

    public function create_order_letter(Request $request,$id)
    {
        date_default_timezone_set("Asia/Jakarta");
        $data = Reimbursement::find($id);
        $financeManager = Timesheet_approver::find(15);

        $templateProcessor = new TemplateProcessor('disbursement_order_letter.docx');

        $numericAmount = (float) preg_replace('/[^0-9]/', '', $data->f_granted_funds);
        $grantedFunds = number_format($numericAmount);
        $templateProcessor->setValue('date_sent', htmlentities(date('j-M-Y')));
        $templateProcessor->setValue('f_type', htmlentities($data->f_type));
        $templateProcessor->setValue('requestor', htmlentities($data->user->name));
        $templateProcessor->setValue('f_id', htmlentities($data->f_id));
        $templateProcessor->setValue('granted_funds', htmlentities('IDR '. $grantedFunds));
        $templateProcessor->setValue('bank_account', htmlentities($data->user->users_detail->usr_bank_name));
        $templateProcessor->setValue('account_number', htmlentities($data->user->users_detail->usr_bank_account));
        $templateProcessor->setValue('finance_manager', htmlentities($financeManager->user->name));

        $templateProcessor->saveAs("reimbursement/Result_$data->f_id.docx");
        $dateCreated = date("Y-m-d", strtotime($data->created_at));
        ob_end_clean();

        $employees = User::where('id', $financeManager->approver)->get();

        foreach ($employees as $employee) {
            dispatch(new SendDisbursementOrder($employee, $data));
        }
        return response()->download(public_path("reimbursement/Result_$data->f_id.docx"), "Disbursement_Order_Letter_$data->f_id".'_'."$data->f_purpose_of_purchase"."_"."$dateCreated".".docx");
    }

    public function reject_request(Request $request, $formId)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $mainForm = Reimbursement::find($formId);
        $mainForm->status_id = 404;
        $mainForm->f_granted_funds = 0;
        $mainForm->save();

        // Find all reimbursement items associated with the formId and update them
        Reimbursement_item::where('reimbursement_id', $formId)
            ->update([
                'edited_by_finance' => true,
                'approved_amount' => 0
            ]);

        //Set the other to 403
        Reimbursement_approval::where('reimbursement_id', $formId)
            ->update(['status' => 403]);

        // Loop through each reimb_item
        foreach ($mainForm->items as $reimbItem) {
            // Create a new Reimbursement_approval record
            Reimbursement_approval::create([
                'reimb_item_id' => $reimbItem->id,
                'RequestTo' => Auth::id(),
                'status' => 404,
                'approved_amount' => 0,
                'reimbursement_id' => $formId,
                'notes' => $request->notes
            ]);
        }

        return redirect()->back()->with('failed', 'You rejected the request!');
    }
}
