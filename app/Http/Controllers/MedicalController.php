<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Medical;
use App\Models\Medical_approval;
use App\Models\Medical_details;
use App\Models\User;
use App\Models\Timesheet_approver;
use App\Models\Project_assignment_user;
use App\Models\Emp_leave_quota;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Ilovepdf\Ilovepdf;

use function GuzzleHttp\Promise\all;

class MedicalController extends Controller
{
    public function index()
    {
        // Mendapatkan pengguna yang login
        $user = Auth::user();

        // Mendapatkan data medis sesuai dengan pengguna yang login
        $med = Medical::where('user_id', $user->id)->get();

        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', 10)
        ->where('expiration', '>=', date('Y-m-d'))
        ->sum('quota_left');
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', Auth::user()->id)
        ->where('leave_id', 100)
        ->where('expiration', '>=', date('Y-m-d'))
        ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
        ->where('user_id', Auth::user()->id)
        ->where('leave_id', 20)
        ->sum('quota_left');
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if ($empLeaveQuotaFiveYearTerm == NULL) {
            $empLeaveQuotaFiveYearTerm = "-";
        }
        return view('medical.medical', 
        [
            'med' => $med, 
            'empLeaveQuotaAnnual' => $empLeaveQuotaAnnual,
            'empLeaveQuotaWeekendReplacement' => $empLeaveQuotaWeekendReplacement,
            'empLeaveQuotaFiveYearTerm' => $empLeaveQuotaFiveYearTerm,
            'totalQuota' => $totalQuota 
        ]);
    }
    public function entry()
    {

        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->med_number + 1 : 1;
        return view('medical.medical_tambah', compact('nextId'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'attach.*' => 'required|mimes:jpeg,png,gif,bmp|image',
            'amount.*' => 'required',
            'desc.*' => 'required',
        ]);

        $attachments = $request->file('attach');
        $amounts = $request->input('amount');
        $descriptions = $request->input('desc');

        // User Medical Approval
        $checkUserDept = Auth::user()->users_detail->department->id;

        $approvalFinance_GA = Timesheet_approver::whereIn('id', [10, 45, 99])
            ->get();
        $approvalSales = Timesheet_approver::whereIn('id', [50, 45, 99])
            ->get();
        $approvalHCM = Timesheet_approver::whereIn('id', [10, 60, 99])
            ->get();
        $approvalService = Timesheet_approver::whereIn('id', [20, 40, 99])
            ->get();

        $findAssignment = Project_assignment_user::where('user_id', Auth::user()->id)->pluck('project_assignment_id')->toArray();
        $usersWithPMRole = Project_assignment_user::whereIn('project_assignment_id', $findAssignment)->where('role', 'PM')->get();

        // ID Medical
        $lastId = Medical::orderBy('id', 'desc')->first();

        // Mengambil bagian angka saja dari ID terakhir (menghilangkan 'M' dan konversi ke angka)
        $lastNumber = ($lastId) ? intval($lastId->id) : 0;

        // Menghitung ID berikutnya dimulai dari 100
        $nextNumber = max($lastNumber + 1, 100); // Pastikan tidak kurang dari 100

        // Membuat ID baru dengan format yang diinginkan (100M, 101M, 102M, dst.)
        $nextId = $nextNumber . 'M';


        // Medical Number
        $lastMedNumber = Medical::orderBy('med_number', 'desc')->first();
        $nextMedNumber = ($lastMedNumber) ? $lastMedNumber->med_number + 1 : 1;

        // Membuat entri baru dalam tabel Medicals
        $medical = new Medical();
        $medical->id = $nextId;
        $medical->user_id = Auth::user()->id;
        $medical->med_number = $nextMedNumber;
        $medical->med_req_date = date('Y-m-d');
        $medical->med_payment = $request->payment_method;
        $medical->med_total_amount = $request->totalAmountInput;
        $medical->save();

        // Menambahkan titik kembali pada hasil perhitungan
        // $formattedResult = number_format($result, 2, ',', '.');

        // Memproses setiap data yang diinputkan ke tabel Medicals_detail
        foreach ($attachments as $key => $attachment) {

            // // ID Medical details
            // $lastmdet = Medical_details::orderBy('mdet_id', 'desc')->first();
            // $nextmdet = ($lastmdet) ? $lastmdet->mdet_id + 1 : 1;

            $extension = $attachment->getClientOriginalExtension();
            $filename = $nextId . '_' . $key .  '_' .  Auth::user()->id . '.' . $extension;

            $amount = str_replace('.', '', $amounts[$key]); // Menghilangkan titik
            $result = $amount * 0.8;
            $roundedAmount = intval($result); // Membulatkan ke angka genap terdekat

            // Menyimpan file attach ke dalam folder yang diinginkan
            $attach_tujuan = '/storage/med_pic';
            $attachment->move(public_path($attach_tujuan), $filename);

            // Membuat entri baru dalam tabel medical details
            $meddet = new Medical_details();
            // $meddet->mdet_id = $nextmdet;
            $meddet->medical_id = $nextId;
            $meddet->mdet_attachment = $filename;
            $meddet->mdet_amount = $amounts[$key];
            $meddet->mdet_desc = $descriptions[$key];
            $meddet->amount_approved =  $roundedAmount;
            $meddet->save();
        }


        switch (true) {
            case ($checkUserDept == 4):

                foreach ($approvalFinance_GA as $approverGa) {
                    $userToApprove[] = $approverGa->approver; //[desy,bs,ronny,suryadi]
                    $userPriority[] = $approverGa->id;
                }
                break;
                // No break statement here, so it will continue to the next case
            case ($checkUserDept == 2):

                foreach ($approvalService as $approverService) {
                    $userToApprove[] = $approverService->approver;
                    $userPriority[] = $approverService->id;
                }
                if (!$usersWithPMRole->isEmpty()) {
                    foreach ($usersWithPMRole as $approverPM) {
                        $userToApprove[] = $approverPM->approver;
                        $userPriority[] = $approverPM->id;
                    }
                }
                break;
            case ($checkUserDept == 3):

                foreach ($approvalHCM as $approverHCM) {
                    $userToApprove[] = $approverHCM->approver;
                    $userPriority[] = $approverHCM->id;
                }
                break;
            case ($checkUserDept == 1):

                foreach ($approvalSales as $approverSales) {
                    $userToApprove[] = $approverSales->approver;
                    $userPriority[] = $approverSales->id;
                }
                break; // Add break statement here to exit the switch block after executing the case
            default:
                return redirect()->back()->with('failed', "You haven't assigned to any department! Ask HR Dept to correcting your account details");
                break;
        }
        foreach ($userToApprove as $uTA) {
            $medical_approve = new Medical_approval();
            $medical_approve->medical_id = $nextId;
            $medical_approve->RequestTo = $uTA;
            $medical_approve->status = 15;
            $medical_approve->save();
        }
        return redirect('/medical/history')->with('Success', 'Medical Reimburse Add successfully');
    }

    public function edit($id)
    {
        $user_info = User::find(Auth::user()->id);
        $med = Medical::findOrFail($id);
        $medDet = Medical_details::where('medical_id', $med->id)->get();
        $medApp = Medical_approval::where('medical_id', $med->id)->get();

        // $medButton = Medical_approval::where('medical_id', $med->id)
        //     ->whereIn('status', [20, 29])
        //     ->pluck('medical_id', 'status')
        //     ->toArray();
        // dd($medButton);
        return view(
            'medical.medical_edit',
            [
                
                'med' => $med,
                'user_info' => $user_info,
                'medDet' => $medDet,
                'medApp' => $medApp
            ]
        );
    }

    public function delete_med_all($id)
    {
        Medical::findOrFail($id)->delete();
        Medical_details::where('medical_id', $id)->delete();
        Medical_approval::where('medical_id', $id)->delete();
        // dd($id);
        $user = Auth::user();
        $med = Medical::where('user_id', $user->id)->get();
        return view('medical.medical', ['med' => $med])->with('Success', 'Medical Reimburse Delete successfully');
    }

    public function update_medDetail(Request $request, $mdet_id, $medical_id)
    {

        $medDet = Medical_details::where('mdet_id', $medical_id)->first();
        $request->validate([
            'attach.*' => 'sometimes|mimes:jpeg,png,gif,bmp|image',
            'amount.*' => 'sometimes',
            'desc.*' => 'sometimes',
        ]);

        if ($request->hasFile('inputAttach')) {
            $inputAttach = $request->file('inputAttach');

            $extension = $inputAttach->getClientOriginalExtension();
            $filename = $medical_id . 'M' . '_' . $mdet_id .  '_' .  Auth::user()->id . '.' . $extension;

            $attach_tujuan = '/med_pic';
            $inputAttach->storeAs('public/' . $attach_tujuan, $filename);

            // Menghapus file attachment lama jika ada
            $oldAttach = public_path($attach_tujuan . '/' . $filename);
            if (file_exists($oldAttach)) {
                unlink($oldAttach);
            }
        } elseif ($medDet->mdet_attachment) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $filename = $medDet->mdet_attachment;
        }
        // $med_detail = Medical_details::find($mdet_id);
        $medDet->mdet_id = $medDet->mdet_id;
        $medDet->mdet_attachment = $filename;
        $medDet->mdet_amount = $request->input_mdet_amount;
        $medDet->mdet_desc = $request->input_mdet_desc;
        $medDet->save();

        return redirect()->back()->with('success', 'Medical Detail Edit Success.');
    }

    public function delete_medDetail($mdet_id, $medical_id)
    {
        DB::table('medicals_detail')
            ->where('mdet_id', $medical_id)
            ->delete();

        return redirect()->back()->with('success', 'Medical Detail Delete Success.');
    }

    public function download($id)
    {
        $user_info = User::find(Auth::user()->id);
        $med = Medical::findOrFail($id);
        $medicalDetails = Medical_details::where('medical_id', $med->id)->get();

        $templatePath = public_path('medical_temp.docx');
        // $key = API_key::where('id', 1)->first();
        // dd($medDet);
        
        $templateProcessor = new TemplateProcessor($templatePath);
        $templateProcessor->setValue('emp_id', $user_info->users_detail->employee_id);
        $templateProcessor->setValue('emp_name', $user_info->name);
        $templateProcessor->setValue('emp_hired_date', $user_info->users_detail->hired_date);
        $templateProcessor->setValue('req_date', $med->med_req_date);
        $templateProcessor->setValue('payment', $med->med_payment);
        $totalAmountFormatted = number_format($med->med_total_amount, 0, '.', '.');
        $templateProcessor->setValue('total_amount', $totalAmountFormatted);

        // Loop melalui medical details
        $rowCount = count($medicalDetails);
        foreach ($medicalDetails as $i => $medDet) {
            // Gantikan placeholder untuk setiap medical detail
            $templateProcessor->setValue("AMOUNT$i", $medDet->mdet_amount);
            $templateProcessor->setValue("DESC$i", $medDet->mdet_desc);

            // Simpan gambar ke direktori 'public/med_pic'
            $gambar = $medDet->mdet_attachment;
            $gambarPath = public_path('storage/med_pic/' . $gambar);

            // Tambahkan gambar ke dokumen Word
            $templateProcessor->setImageValue("ATTACHMENT$i", array('path' => $gambarPath, 'width' => 500, 'height' => 700, 'margin-top' => 100));

            // If there are less than 3 rows, set the remaining rows to empty strings
            if ($rowCount < 3) {
                for ($i = $rowCount; $i < 3; $i++) {
                    $templateProcessor->setValue("AMOUNT$i", "");
                    $templateProcessor->setValue("DESC$i", "");
                    $templateProcessor->setValue("ATTACHMENT$i", "");
                }
            }
        }
        // Simpan file Word
        $outputPath = public_path('Medical Reimbursement ' . $user_info->name . '.docx');
        $templateProcessor->saveAs($outputPath);

        // // Convert the Word file to PDF using iLovePDF API
        // $apiPublicKey = $key->public_key;
        // $apiSecretKey = $key->secret_key;

        // $ilovepdf = new Ilovepdf($apiPublicKey, $apiSecretKey);

        // // Start the task for converting to PDF
        // $task = $ilovepdf->newTask('officepdf');

        // // Add the uploaded file to the task
        // $task->addFile($outputPath);

        // // Execute the task
        // $task->execute();

        // // Download the converted PDF file directly to the user's browser
        // $task->download();
        // $outputPathPDF = public_path('Exit Clearance ' . $user->name . '.pdf');

        // // Delete the temporary file
        // unlink($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }


    public function resubmit(Request $request, $id)
    {
        $med = Medical::find($id);
        // $medDet = Medical_details::where('medical_id', $med->id)->get();
        $medApp = Medical_approval::where('medical_id', $med->id)->get();

        foreach($medApp as $mA){
            $mA->status = 15;
            $mA->save();

        }

        return redirect('/medical/history')->with('success', 'Re-Submit Your Medical Successfully.');
    }
      
    // Medical Manage
    public function index_manage()
    {
        $user = User::all();

        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = Carbon::create(null, $month, 1)->format('F');
        }

        $currentYear = Carbon::now()->year;
        $years = range($currentYear, $currentYear - 10);
        return view('medical.medical_manage', ['user' => $user, 'months' => $months,  'years' => $years]);
    }
}
