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
use App\Models\API_key;
use App\Models\Department;
use App\Models\Setting;
use App\Models\Position;
use App\Models\Users_fingerprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Ilovepdf\Ilovepdf;
use Exception;
use Illuminate\Support\Facades\Validator;

class HrController extends Controller
{
	public function index()
	{
        $department = Department::all();
		//Cutoff Date Timesheet Submission
		$Cutoffdate = Cutoffdate::all();

		//Position, Users & Approvers
		$users = User::all();
		$position = Position::all();
		$approvers = Timesheet_approver::all();

		//DEFAULT
		$Default_Approve1 = Timesheet_approver::where('id', 29)->first();
		$Default_Approve2 = Timesheet_approver::where('id', 28)->first();

		//Admin
		$reimburse_admin = Timesheet_approver::where('id', 65)->first();
		$medical_admin = Timesheet_approver::where('id', 99)->first();


		//export TS
		$setting_export_ts = Setting::where('id', 1)->first();

		//export reimburse
		$setting_export_reimburse = Setting::where('id', 2)->first();

		//CC Emails
		$cc_email = Setting::where('id', 3)->first();

		$usersFingerprint = Users_fingerprint::all();
		$users = User::all();

		return view(
			'hr.compliance.main',
			[
				'cutoffDate' => $Cutoffdate,
				'approver' => $approvers,
				'user' => $users,
				'Default1' => $Default_Approve1,
				'Default2' => $Default_Approve2,
				'usersFingerprint' => $usersFingerprint,
				'users' => $users,
				'export_ts' => $setting_export_ts,
				'export_reimburse' => $setting_export_reimburse,
				'position'	=> $position,
				'cc_email' => $cc_email,
				'reimburse_admin' => $reimburse_admin,
				'medical_admin' => $medical_admin,
                'department' => $department
			]
		);
	}

	public function update_regulation(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            // Add your validation rules here
        ]);

        // Processing department approvers
        foreach ($request->all() as $key => $value) {
            // Check if the key starts with 'approvers' indicating it's a department approver
            if (strpos($key, 'approvers') === 0) {
                $approverId = substr($key, strlen('approvers')); // Extract the ID from the key
                // Update the Timesheet_approver record with the extracted ID
                Timesheet_approver::where('id', $approverId)->update(['approver' => $value]);
            }
        }

        // Update default approvers
        Timesheet_approver::where('id', 29)->update(['approver' => $request->Default_FA]);
        Timesheet_approver::where('id', 28)->update(['approver' => $request->Default_PA]);

        // Update Reimburse & Medical Admin
        Timesheet_approver::where('id', 65)->update(['approver' => $request->reimburse_admin]);
        Timesheet_approver::where('id', 99)->update(['approver' => $request->medical_admin]);

        // Update Export TS
        Setting::where('id', 1)->update(['position_id' => $request->export_ts]);

        // Update Export Reimburse
        Setting::where('id', 2)->update(['position_id' => $request->export_reimburse]);

        // Update CC Emails
        Setting::where('id', 3)->update(['user_id' => $request->email_cc]);

        return redirect()->back()->with('success', 'Compliance Edit Success');
    }

    public function update_cutoffdate(Request $request)
    {
        foreach ($request->input('cutoff_dates', []) as $key => $value) {
            $cutoffDate = CutoffDate::find($key); // Assuming $key is the ID of the CutoffDate record

            if ($cutoffDate) {
                $cutoffDate->start_date = $value['start_date'];
                $cutoffDate->closed_date = $value['closed_date'];
                $cutoffDate->save();
            }
        }

        return redirect()->back()->with('success', 'Compliance Edit Success');
    }

    public function add_new_approver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'sometimes',
            'department' => 'sometimes',
            'setAs' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $setAs = $request->setAs;
        $desc = "";
        if ($setAs == 1) {
            $desc = "Director Level";
        } else {
            $desc = "Reviewer"; // Or any other default description
        }
        // Create Timesheet_approver record
        Timesheet_approver::create([
            'department_id' => $request->department,
            'approver_level' => $desc,
            'approver' => $request->user_name,
            'group_id' => $request->setAs,
        ]);

        return redirect()->back()->with('success', 'Compliance Edit Success');
    }

    public function remove_approver($userId)
    {
        // Find the timesheet approver by ID
        $approver = Timesheet_approver::find($userId);

        // Check if the timesheet approver exists
        if ($approver) {
            // Delete the timesheet approver
            $approver->delete();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'User has been successfully removed from the group.');
        }

        // If the timesheet approver does not exist, redirect back with a failure message
        return redirect()->back()->with('failed', 'User not found or already removed from the group.');
    }

	public function exit_clear()
	{

		$user = User::with('users_detail')
			->whereHas('users_detail', function ($query) {
				$query->whereNull('resignation_date');
			})->get();

		$data = User::with('users_detail')
			->whereHas('users_detail', function ($query) {
				$query->whereNotNull('resignation_date');
			})->get();

		// $userall = User::with('users_detail')->first();
		return view('hr.exit_clearance.exit_main', ['data' => $data, 'us_List' => $user,]);
	}

	public function print($id)
	{
		$user = User::with('users_detail')->findOrFail($id);
		$templatePath = public_path('exitclearance_temp.docx');
		$key = API_key::where('id', 1)->first();


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

		return response()->download($outputPath)->deleteFileAfterSend(true);
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

	public function add_user_fingerprint(Request $request)
	{
		$request->validate([
			'userName' => 'required',
			'f_id' => 'required',
		]);

		// Check if a record already exists for the user_id
		$validation = Users_fingerprint::where('user_id', $request->user_name)->exists();

		if (!$validation) {
			Users_fingerprint::create([
				'user_id' => $request->user_name,
				'fingerprint_id' => $request->f_id
			]);

			return response()->json(['success' => 'Fingerprint data has been added!']);
		}

		return response()->json(['error' => 'Fingerprint data already exists for this user.']);
	}

	public function delete_user_fingerprint($id)
	{
		$userFingerprint = Users_fingerprint::find($id);
		$usersName = $userFingerprint->user->name;

		if ($userFingerprint) {
			$userFingerprint->delete();
			return redirect()->back()->with('success', "$usersName Fingerprint has been successfully deleted!");
		}
		return redirect()->back()->with('error', "$usersName Fingerprint not found or already deleted!");
	}
}
