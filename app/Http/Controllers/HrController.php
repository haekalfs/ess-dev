<?php

namespace App\Http\Controllers;

use App\Exports\ExitClearancePrint;
use App\Models\Cutoffdate;
use App\Models\Financial_password;
use App\Models\Timesheet;
use App\Models\Timesheet_approval_cutoff_date;
use App\Models\Timesheet_approver;
use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Ilovepdf\Ilovepdf;
use Exception;

class HrController extends Controller
{
    public function index(){
		//Cutoff Date Timesheet Submission
		$Cutoffdate = Cutoffdate::find(1);
		$CutoffdateTimesheetApproval = Cutoffdate::find(2);
		$leaveApprovalCutoffdate = Cutoffdate::find(3);
		$reimburseApprovalCutoffdate = Cutoffdate::find(4);

		$users = User::all();
		$approvers = Timesheet_approver::all();

		//Finance And GA
		$FGA_Approve1 = Timesheet_approver::where('id', 10 )->first();
		$FGA_Approve2 = Timesheet_approver::where('id', 45)->first();
		$FGA_Approve3 = Timesheet_approver::where('id', 15)->first();

		//Technology And HCM
		$THC_Approve1 = Timesheet_approver::where('id', 11)->first();
		$THC_Approve2 = Timesheet_approver::where('id', 60)->first();

		//Sales And Marketing
		$SM_Approve1 = Timesheet_approver::where('id', 50)->first();
		$SM_Approve2 = Timesheet_approver::where('id', 55)->first();

		//Services
		$Service_Approve1 = Timesheet_approver::where('id', 20)->first();
		$Service_Approve2 = Timesheet_approver::where('id', 40)->first();

		//DEFAULT
		$Default_Approve1 = Timesheet_approver::where('id', 29)->first();
		$Default_Approve2 = Timesheet_approver::where('id', 28)->first();

		return view('hr.compliance.main', 
		['cutoffDate' => $Cutoffdate, 
		'tsCutoffdate' => $CutoffdateTimesheetApproval, 
		'leaveCutoffdate' => $leaveApprovalCutoffdate, 
		'reimburseCutoffdate' => $reimburseApprovalCutoffdate, 
		'approver' => $approvers, 
		'user' => $users, 
		'FGA1' => $FGA_Approve1,
		'FGA2' => $FGA_Approve2,
		'Finance' => $FGA_Approve3,
		'THC1' => $THC_Approve1,
		'THC2' => $THC_Approve2,
		'SM1' => $SM_Approve1,
		'SM2' => $SM_Approve2,
		'Service1' => $Service_Approve1,
		'Service2' => $Service_Approve2,
		'Default1' => $Default_Approve1,
		'Default2' => $Default_Approve2,

		]); 
	}

	public function update_regulation(Request $request){
	
		$request->validate([
			'FGA_FA' => 'sometimes',
			'FGA_PA' => 'sometimes',
			'THC_FA' => 'sometimes',
			'THC_PA' => 'sometimes',

		]);

		//Password Finance
			$Newpassword = $request->input('confirmPassword');
			$hash = Hash::make($Newpassword);

			$password_finance = Financial_password::where('id', 1)->first();
			$password_finance->password = $hash;
			$password_finance->save();

		// Cutoff Date
			// Cutoff Date Submit TS
			$Cutoffdate_input = Cutoffdate::where('id', 1)->first();
			$Cutoffdate_input->closed_date = $request->ts_submit_date;
			$Cutoffdate_input->save();

			// Cutoff Date Approve TS
			$CutoffdateTimesheetApproval_input = Cutoffdate::where('id', 2)->first();
			$CutoffdateTimesheetApproval_input->closed_date = $request->ts_approve_date;
			$CutoffdateTimesheetApproval_input->save();

			// Cutoff Date Approve Leave
			$leaveApprovalCutoffdate_input = Cutoffdate::where('id', 3)->first();
			$leaveApprovalCutoffdate_input->closed_date = $request->leave_approve_date;
			$leaveApprovalCutoffdate_input->save();

			// Cutoff Date Approve Reimburse
			$reimburseApprovalCutoffdate_input = Cutoffdate::where('id', 4)->first();
			$reimburseApprovalCutoffdate_input->closed_date = $request->reimburse_approve_date;
			$reimburseApprovalCutoffdate_input->save();

		// Approver
			// Finance & GA
			$input_FGA_Approve1 = Timesheet_approver::where('id', 10)->first();
			$input_FGA_Approve1->approver = $request->FGA_FA;
			$input_FGA_Approve1->save();

			$input_FGA_Approve2 = Timesheet_approver::where('id', 45)->first();
			$input_FGA_Approve2->approver = $request->FGA_PA;
			$input_FGA_Approve2->save();

			$input_FGA_Approve3 = Timesheet_approver::where('id', 15)->first();
			$input_FGA_Approve3->approver = $request->Finance_approver;
			$input_FGA_Approve3->save();
			
			//Technology And HCM
			$input_THC_Approve1 = Timesheet_approver::where('id', 11)->first();
			$input_THC_Approve1->approver = $request->THM_FA;
			$input_THC_Approve1->save();

			$input_THC_Approve2 = Timesheet_approver::where('id', 60)->first();
			$input_THC_Approve2->approver = $request->THM_PA;
			$input_THC_Approve2->save();

			//Sales And Marketing
			$input_SM_Approve1 = Timesheet_approver::where('id', 50)->first();
			$input_SM_Approve1->approver = $request->SM_FA;
			$input_SM_Approve1->save();

			$input_SM_Approve2 = Timesheet_approver::where('id', 55)->first();
			$input_SM_Approve2->approver = $request->SM_PA;
			$input_SM_Approve2->save();

			//Services
			$input_Service_Approve1 = Timesheet_approver::where('id', 20)->first();
			$input_Service_Approve1->approver = $request->Service_FA;
			$input_Service_Approve1->save();

			$input_Service_Approve2 = Timesheet_approver::where('id', 40)->first();
			$input_Service_Approve2->approver = $request->Service_PA;
			$input_Service_Approve2->save();

			//DEFAULT
			$input_Default_Approve1 = Timesheet_approver::where('id', 29)->first();
			$input_Default_Approve1->approver = $request->Default_FA;
			$input_Default_Approve1->save();

			$input_Default_Approve2 = Timesheet_approver::where('id', 28)->first();
			$input_Default_Approve2->approver = $request->Default_PA;
			$input_Default_Approve2->save();

		return redirect()->back()->with('success', 'Compilance Edit Success');
	}

	public function exit_clear(){

		$user = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNull('resignation_date');
		})->get();

		$data = User::with('users_detail')
		->whereHas('users_detail', function ($query) {
			$query->whereNotNull('resignation_date');
		})->get();

		// $userall = User::with('users_detail')->first();
		return view('hr.exit_clearance.exit_main', ['data' => $data, 'us_List' => $user, ]);
	}

	public function print($id)
	{
		$user = User::with('users_detail')->findOrFail($id);
		$templatePath = public_path('exitclearance_temp.docx');

		$positionName = $user->users_detail->position->position_name ?? '';
		$bulan = date('F');

		$daftarBulan = [
			'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
			'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
		];

		$templateProcessor = new TemplateProcessor($templatePath);
		$templateProcessor->setValue('name', $user->name);
		$templateProcessor->setValue('emp_id', $user->users_detail->employee_id);
		$templateProcessor->setValue('position', $positionName);
		$templateProcessor->setValue('no_ID', $user->users_detail->usr_id_no);
		$templateProcessor->setValue('D', date('d'));
		$templateProcessor->setValue('M', $daftarBulan[date('n') - 1]);
		$templateProcessor->setValue('Y', date('Y'));

		// Simpan file Word
		$outputPath = public_path('Exit Clearance ' . $user->name . '.docx');
		$templateProcessor->saveAs($outputPath);

		// Convert the Word file to PDF using iLovePDF API
		$apiPublicKey = 'project_public_58ccb352acd03ede9a96cc4251942fe5_x3t5t1d006d6f6675cf7272092eba8a4909d1';
		$apiSecretKey = 'secret_key_1574ad48ae54707085bce8b2a2d1585c_6-YEB8d06c4afe04ba378cdc81b2365e1aaad';

		$ilovepdf = new Ilovepdf($apiPublicKey, $apiSecretKey);

		// Start the task for converting to PDF
		$task = $ilovepdf->newTask('officepdf');

		// Add the uploaded file to the task
		$task->addFile($outputPath);

		// Execute the task
		$task->execute();

		// Download the converted PDF file directly to the user's browser
		$task->download();
		$outputPathPDF = public_path('Exit Clearance ' . $user->name . '.pdf');

		// Delete the temporary file
		unlink($outputPath);
		
		return response()->download($outputPathPDF)->deleteFileAfterSend(true);
	}


	public function resign_emp(Request $request)
	{
		// Validasi input jika diperlukan
		$request->validate([
			'inputUser' => 'required',
			'resign_date' => 'required|date',
		]);

		// Ambil data input dari form
		$userId = $request->inputUser;
		$resignDate = $request->resign_date;

		// Cari objek Users_detail berdasarkan user_id
		$userDetail = Users_detail::where('user_id', $userId)->first();

		if ($userDetail) {
			// Update kolom status_active dan resignation_date
			$userDetail->status_active = 'nonActive';
			$userDetail->resignation_date = $resignDate;
			$userDetail->save();

			// Berhasil, lakukan redirect atau respons sesuai kebutuhan
			return redirect()->back()->with('success', 'Data berhasil disimpan.');
		} else {
			// User tidak ditemukan, lakukan redirect atau respons sesuai kebutuhan
			return redirect()->back()->with('error', 'User tidak ditemukan.');
		}
	}

}
