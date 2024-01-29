<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyReimbursementCreation;
use App\Jobs\NotifyReimbursementPaid;
use App\Models\Company_project;
use App\Models\Department;
use App\Models\Financial_password;
use App\Models\Notification_alert;
use App\Models\Project_assignment_user;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\Timesheet_approver;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Setting;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $projects = Company_project::all();
        $approver = Timesheet_approver::whereIn('id', [40, 45, 55, 60, 28])->get();

        $existingID = Reimbursement::whereNull('deleted_at')->orderBy('f_id', 'desc')->pluck('f_id')->first();
        $nextID = $existingID + 1;

        return view('reimbursement.request', compact('projects', 'approver', 'nextID'));
    }

    public function submit_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'project' => 'required',
            'approver' => 'sometimes',
            'notes' => 'sometimes'
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect('/reimbursement/create/request');
        }

        $nowYear = date('Y');
        $project = $request->project;

        $typeOfReimbursement = (empty(Company_project::find($project))) ? $project : Company_project::find($project)->project_name;
        $companyProjectId = (empty(Company_project::find($project))) ? $project : Company_project::find($project)->id;

        $userDept = (empty(Company_project::find($project))) ? 1 : 4;
        $RequestTo = empty($request->approver) ? $userDept : $request->approver;

        $approvalByGroup = Timesheet_approver::where('group_id', $RequestTo)
            ->get();

        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::where('company_project_id', $companyProjectId)
        ->whereIn('project_assignment_id', $findAssignment)
        ->where('periode_end', '>=', date('Y-m-d'))
        ->where('role', 'PM')
        ->get();

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
            'status_id' => 20,
            'f_payment_method' => $request->payment_method,
            'f_type' => $typeOfReimbursement,
            'f_approver' => $RequestTo,
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

                if($usersWithPMRole)
                {
                    foreach($usersWithPMRole as $approverPm){
                        Reimbursement_approval::create([
                            'status' => 20,
                            'RequestTo' => $approverPm->user_id,
                            'reimb_item_id' => $itemId,
                            'reimbursement_id' => $uniqueId
                        ]);
                        $userToApprove[] = $approverPm->approver;
                    }
                }

                foreach($approvalByGroup as $approverGroup){
                    Reimbursement_approval::create([
                        'status' => 20,
                        'RequestTo' => $approverGroup->approver,
                        'reimb_item_id' => $itemId,
                        'reimbursement_id' => $uniqueId
                    ]);
                    $userToApprove[] = $approverGroup->approver;
                }
            }

            $employees = User::whereIn('id', $userToApprove)->get();
            $userName = Auth::user()->name;

            foreach ($employees as $employee) {
                dispatch(new NotifyReimbursementCreation($employee, $userName));
            }
            Session::flash('success',"Request has been submitted!");
            return redirect('/reimbursement/history');
        } else {
            Session::flash('failed',"Error Database has Occured! Failed to create request!");
            return redirect('/reimbursement/history');
        }
    }

    public function view_details($id)
    {
        $reimbursement = Reimbursement::where('id', $id)->get();

        foreach ($reimbursement as $as) {
            if ($as->status_id == 20 || $as->status_id == 30) {
                $status = "Waiting for Approval";
            } elseif ($as->status_id == 29) {
                $status = "Approved";
            } elseif ($as->status_id == 2002) {
                $status = "Paid";
            } elseif ($as->status_id == 404) {
                $status = "Rejected";
            } else {
                $status = "Unknown Status";
            }
            $f_id = $as->f_id;
        }

        $emp = User::all();

        $reimbursement_items = Reimbursement_item::where('reimbursement_id', $id)->get();
        $reimbursement_approval = Reimbursement_approval::where('reimbursement_id', $id)->groupBy('RequestTo')->get();

        return view('reimbursement.view_details', ['reimbursement' => $reimbursement, 'stat' => $status, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }

    public function retrieveReimburseData($id)
    {
        // Get the Timesheet records between the start and end dates
        $itemData = Reimbursement_item::find($id);

        return response()->json($itemData);
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

    public function cancel_request($id)
	{
        $reimbRequest = Reimbursement::where('id',$id)->first();

        $deleteRA = Reimbursement_approval::where('reimbursement_id', $id)->get();
        foreach ($deleteRA as $del) {
            $del->delete();
        }

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

        return redirect()->back()->with('success', "Reimbursement Request has been canceled!");
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

            $approvals = Reimbursement::whereIn('status_id', [29, 2002])
            ->whereYear('created_at', $Year)
            ->whereMonth('created_at', $Month)
            ->get();
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

        return view('reimbursement.manage.history', compact('approvals', 'notify', 'notifyMonth', 'yearsBefore', 'Month', 'Year', 'employees'));
    }

    public function disbursed_all(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usersName' => 'required',
            'formId' => 'required'
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect()->back();
        }

        // Extract and convert the comma-delimited values into an array
        $usersName = explode(',', $request->input('usersName'));
        $usersName = array_filter($usersName);

        //id
        $formId = explode(',', $request->input('formId'));
        $formId = array_filter($formId);

        // Ensure both arrays are not empty before proceeding
        if (!empty($usersName) && !empty($formId)) {
            // Update records in the Reimbursement table where id is in the $formId array
            Reimbursement::whereIn('id', $formId)->update(['status_id' => 2002]);

            $employees = User::whereIn('id', $usersName)->get();
            $userName = Auth::user()->name;

            foreach ($employees as $employee) {
                dispatch(new NotifyReimbursementPaid($employee, $userName));
            }
        }

        return redirect()->back()->with('success', "Reimbursement has been marked to Paid! and system begin to sent notification to each users");
    }

    public function export_excel($Month, $Year)
	{
        $checkUserPost = Auth::user()->users_detail->position->id;

        //Tabel Setting Export Role
        $settingExport = Setting::where('id', 2)->first();
        $checkSettingExport = $settingExport->position_id;

        // Compare the hashed passwords
        if (in_array($checkUserPost, [10])) {
            $templatePath = public_path('template_reimbursement.xlsx');
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            // Set up the starting row and column for the data
            $startRow = 8;
            $startCol = 2;

            $approvedReimb = Reimbursement::where('status_id', 29)
            ->whereYear('created_at', $Year)
            ->whereMonth('created_at', $Month)
            ->pluck('id')
            ->toArray();

            $result = Reimbursement_item::where('reimbursement_id', $approvedReimb)->get();

            // Initialize the last printed user name
            $lastUser = '';
            $lastId = '';
            $firstRow = true; // Flag to check if it's the first row for each user
            $total = [];

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
                }

                $sheet->setCellValueByColumnAndRow($startCol + 4, $startRow, $row->description);
                $sheet->setCellValueByColumnAndRow($startCol + 5, $startRow, $row->amount);
                $sheet->setCellValueByColumnAndRow($startCol + 6, $startRow, $row->approved_amount);

                $startRow++;
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

        $reimbursement_items = Reimbursement_item::where('reimbursement_id', $id)->get();
        $reimbursement_approval = Reimbursement_approval::where('reimbursement_id', $id)->groupBy('RequestTo')->get();

        return view('reimbursement.manage.manage_view_details', ['reimbursement' => $reimbursement, 'reimbursement_approval' => $reimbursement_approval, 'stat' => $status, 'fm' => $financeManager, 'user' => $emp, 'f_id' => $f_id, 'reimbursement_items' => $reimbursement_items]);
    }
}
