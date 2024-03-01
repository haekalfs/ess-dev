<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyMedicalCreation;
use App\Jobs\NotifyMedicalPaid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Medical;
use App\Models\Medical_approval;
use App\Models\Medical_details;
use App\Models\Medical_payment;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Timesheet_approver;
use App\Models\Emp_medical_balance;
use App\Models\Position;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Ilovepdf\Ilovepdf;
use Illuminate\Support\Facades\Storage;
use function GuzzleHttp\Promise\all;
use App\Http\Controllers\DateTime;
use App\Models\Timesheet;
use DateTime as GlobalDateTime;

class MedicalController extends Controller
{
    public function index($yearSelected = null)
    {
        $hired_date = Auth::user()->users_detail->hired_date; // assuming $hired_date is in Y-m-d format
        $current_date = date('Y-m-d'); // get the current date

        // create DateTime objects from the hired_date and current_date values
        $hired_date_obj = new GlobalDateTime($hired_date);
        $current_date_obj = new GlobalDateTime($current_date);

        // calculate the difference between the hired_date and current_date
        $diff = $current_date_obj->diff($hired_date_obj);

        // get the total number of years from the difference object
        $total_years_of_service = $diff->y;

        // Mendapatkan pengguna yang login
        $user = Auth::user();

        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if ($yearSelected) {
            $currentYear = $yearSelected;
        }

        $med = Medical::where('user_id', $user->id)->whereYear('med_req_date', $currentYear)->get();
        // dd($med->medical_approval);
        $emp_medical_balance = Emp_medical_balance::where('user_id', Auth::user()->id)->where('active_periode', Carbon::now()->year)
            ->first();

        // Mendapatkan tanggal aktif dan tanggal sekarang
        $activePeriode = Carbon::parse($emp_medical_balance->expiration);
        $now = Carbon::now();

        // Menghitung selisih bulan
        $monthPeriode = $activePeriode->diffInMonths($now);

        return view(
            'medical.medical',
            [
                'med' => $med,
                'emp_medical_balance' => $emp_medical_balance,
                'total_years_of_service' => $total_years_of_service,
                'yearSelected' => $yearSelected,
                'yearsBefore' => $yearsBefore,
                'monthPeriode' => $monthPeriode,
            ]
        );
    }
    public function entry()
    {

        $lastId = Medical::orderBy('id', 'desc')->first();
        $lastNumber = ($lastId) ? intval(substr($lastId->id, 4, 5)) : 0;
        $currentPrefix = ($lastId) ? substr($lastId->id, 0, 2) : '44';

        // Menentukan tahun saat ini
        $currentYear = date('y');

        // Menambahkan angka 44 dan tahun ke dalam ID baru
        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT); // Format nomor dengan leading zeros

        // Cek apakah tahun saat ini lebih dari 2024
        if ($currentYear > 24) {
            // Tambahkan 1 ke dua digit pertama
            $currentPrefix = strval(intval($currentPrefix) + 1);
            // Reset digit terakhir menjadi 0 jika tabel tidak kosong
            $nextNumber = '00000';
        }

        $nextId = $currentPrefix . $nextNumber;

        // Jika tabel Medical kosong, mulai dari 4400000
        if (!$lastId) {
            $nextId = '4400000';
        }

        return view('medical.medical_request', compact('nextId'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'attach.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'amount.*' => 'required',
            'desc.*' => 'required',
            'date_exp.*' => 'required'
        ]);

        $attachments = $request->file('attach');
        $amounts = $request->input('amount');
        $descriptions = $request->input('desc');
        $date_exp = $request->input('date_exp');

        $approval = Timesheet_approver::whereIn('id', [99])->first();
        $payment_approval = Timesheet_approver::where('id', [15])->first();


        $lastId = Medical::orderBy('id', 'desc')->first();
        $lastNumber = ($lastId) ? intval(substr($lastId->id, 4, 5)) : 0;
        $currentPrefix = ($lastId) ? substr($lastId->id, 0, 2) : '44';

        // Menentukan tahun saat ini
        $currentYear = date('y');

        // Menambahkan angka 44 dan tahun ke dalam ID baru
        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT); // Format nomor dengan leading zeros

        // Cek apakah tahun saat ini lebih dari 2024
        if ($currentYear > 24) {
            // Tambahkan 1 ke dua digit pertama
            $currentPrefix = strval(intval($currentPrefix) + 1);
            // Reset digit terakhir menjadi 0 jika tabel tidak kosong
            $nextNumber = '00000';
        }

        $nextId = $currentPrefix . $nextNumber;

        // Jika tabel Medical kosong, mulai dari 4400000
        if (!$lastId) {
            $nextId = '4400000';
        }
        $attachment_num = $nextId;

        $request_date = date('ymd');

        // Membuat entri baru dalam tabel Medicals
        $medical = new Medical();
        $medical->id = $nextId;
        $medical->user_id = Auth::user()->id;
        $medical->med_req_date = date('Y-m-d');
        $medical->med_payment = $request->payment_method;
        $medical->med_total_amount = $request->totalAmountInput;
        $medical->save();

        // Memproses setiap data yang diinputkan ke tabel Medicals_detail
        foreach ($attachments as $key => $attachment) {

            $extension = $attachment->getClientOriginalExtension();
            $filename = $attachment_num . $request_date . $key . '.' . $extension;

            $amount = str_replace('.', '', $amounts[$key]); // Menghilangkan titik
            // $result = $amount * 0.8;
            // $roundedAmount = intval($result); // Membulatkan ke angka genap terdekat
            // $format_angka = number_format($roundedAmount, 0, ',', '.');
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
            $meddet->amount_approved =  $amounts[$key];
            $meddet->mdet_date_exp = $date_exp[$key];
            $meddet->save();
        }

        $userToApprove = $approval->approver;

        $medical_approve = new Medical_approval();
        $medical_approve->medical_id = $nextId;
        $medical_approve->RequestTo = $userToApprove;
        $medical_approve->status = 15;
        $medical_approve->save();

        $medical_payment = new Medical_payment();
        $medical_payment->medical_id = $nextId;
        $medical_payment->payment_approver = $payment_approval->approver;
        $medical_payment->paid_status = 15;
        $medical_payment->save();

        $employees = User::where('id', $userToApprove)->get();
        $userName = Auth::user()->name;

        // foreach ($employees as $employee) {
        //     dispatch(new NotifyMedicalCreation($employee, $userName));
        // }

        return redirect('/medical/history')->with('success', 'Medical Reimburse Add successfully, Dont forget to bring the original receipt to the Finance Department.');
    }


    public function delete_med_all($id)
    {
        Medical::findOrFail($id)->delete();
        Medical_details::where('medical_id', $id)->delete();
        Medical_approval::where('medical_id', $id)->delete();

        return redirect()->back()->with('success', 'Medical Reimburse Delete successfully');
    }

    public function edit($id)
    {
        $user_info = User::find(Auth::user()->id);
        $med = Medical::findOrFail($id);
        $medDet = Medical_details::where('medical_id', $id)->get();
        $medApp = Medical_approval::where('medical_id', $id)->get();
        $medPay = Medical_payment::where('medical_id', $id)->get();

        $lastUpdated = $med->medical_approval->updated_at;

        // Hitung selisih waktu antara sekarang dan waktu terakhir diperbarui
        $timeDiff = now()->diffInSeconds($lastUpdated);

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
                'medApp' => $medApp,
                'medPay' => $medPay,
                'timeDiff' => $timeDiff,
            ]
        );
    }

    public function update_medDetail(Request $request, $mdet_id, $medical_id)
    {

        $medDet = Medical_details::where('mdet_id', $medical_id)->first();
        $request->validate([
            'attach_edit' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'amount.*' => 'sometimes',
            'desc.*' => 'sometimes',
        ]);

        if ($request->hasFile('attach_edit')) {
            $inputAttach = $request->file('attach_edit');
            $fileOld = $medDet->mdet_attachment;

            // Hapus file menggunakan Storage
            Storage::delete(public_path('med_pic/' . $fileOld));

            $filenameOld = pathinfo($fileOld, PATHINFO_FILENAME);
            $extension = $inputAttach->getClientOriginalExtension();
            $filename = $filenameOld . '.' . $extension;

            $attach_tujuan = 'med_pic';
            $inputAttach->storeAs('public/' . $attach_tujuan, $filename);
        } elseif ($medDet->mdet_attachment) {
            $filename = $medDet->mdet_attachment;
        }

        // $med_detail = Medical_details::find($mdet_id);
        $medDet->mdet_id = $medDet->mdet_id;
        $medDet->mdet_attachment = $filename;
        $medDet->mdet_amount = $request->input_mdet_amount;
        $medDet->mdet_date_exp = $request->input_mdet_date_exp;
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

        $med->med_total_amount = $request->totalAmountInput;
        $med->save();

        $medApp = Medical_approval::where('medical_id', $id)->firstOrFail();
        $medApp->status = 15;
        $medApp->approval_notes = NULL;
        $medApp->approval_date = NULL;
        $medApp->total_amount_approved = NULL;
        $medApp->save();

        return redirect('/medical/history')->with('success', 'Re-Submit Your Medical Successfully.');
    }


    // Medical Review By Finance Manager
    public function review_fm(Request $request)
    {
        $approve = Timesheet_approver::where('id', 15)->first();
        $approval = $approve->approver;

        $medReview = Medical_approval::where('status', 29)->pluck('medical_id')->toArray(); // Get medical_ids that match the condition

        $user = User::all();

        $currentYear = Carbon::now()->year;
        $month = Carbon::now()->month;
        $currentMonth = date('m');
        $years = range($currentYear, $currentYear - 10);

        $statusPay = $request->input('status_pay', '20'); // Mengambil nilai status_pay dari permintaan, defaultnya '20' (Unpaid)

        $query = Medical::whereIn('id', $medReview)
            ->with(['medical_payment' => function ($query) use ($approval) {
                $query->where('payment_approver', $approval);
            }])
            ->where(function ($query) use ($request) {
                // Filter by user_id
                if ($request->has('user_id') && $request->user_id !== '1') {
                    $query->where('user_id', $request->user_id);
                }

                // Filter by year
                if ($request->has('year') && $request->year !== '') {
                    $query->whereYear('med_req_date', $request->year);
                } else {
                    // Jika tidak ada filter tahun, ambil data untuk tahun saat ini
                    $query->whereYear('med_req_date', Carbon::now()->year);
                }

                // Filter by month
                if ($request->has('month') && $request->month !== '') {
                    $query->whereMonth('med_req_date', $request->month);
                } else {
                    // Jika tidak ada filter bulan, ambil data untuk bulan saat ini
                    $query->whereMonth('med_req_date', Carbon::now()->month);
                }
            })
            ->whereHas('medical_payment', function ($q) use ($statusPay) {
                // Filter by status_pay
                $q->where('paid_status', $statusPay);
            });

        $med = $query->get();


        // dd($med);
        $emp_medical_balance = Emp_medical_balance::where('user_id', Auth::user()->id)->where('active_periode', Carbon::now()->year)
            ->first();
        return view(
            'medical.review.review_medical',
            [
                'med' => $med,
                'user' => $user,
                'years' => $years,
                'month' => $month,
                'currentMonth' => $currentMonth,
                'emp_medical_balance' => $emp_medical_balance
            ]
        );
    }

    public function detail_review($id)
    {
        $med = Medical::findOrFail($id);
        $userMedId = $med->user_id;
        $medDet = Medical_details::where('medical_id', $med->id)->get();
        $user = Users_detail::where('user_id', $userMedId)->first();

        $hired_date = $user->hired_date; // assuming $hired_date is in Y-m-d format
        $current_date = date('Y-m-d'); // get the current date

        // create DateTime objects from the hired_date and current_date values
        $hired_date_obj = new GlobalDateTime($hired_date);
        $current_date_obj = new GlobalDateTime($current_date);

        // calculate the difference between the hired_date and current_date
        $diff = $current_date_obj->diff($hired_date_obj);

        // get the total number of years from the difference object
        $total_years_of_service = $diff->y;

        $medAppUpdate = Medical_approval::where('medical_id', $med->id)
            ->whereIn('RequestTo', [Auth::user()->id])
            ->whereNotIN('status', [15])
            ->orderByDesc('updated_at')
            ->orderBy('medical_id')
            ->first();

        $currentYear = Carbon::now()->year;
        $medBalance = Emp_medical_balance::where('user_id', $userMedId)
            ->where('active_periode', '<=', $currentYear)->where('expiration', '>=', $currentYear)
            ->first();

        $position = Position::all();
        return view(
            'medical.review.view_details_review',
            [
                'med' => $med,
                'medBalance' => $medBalance,
                'medDet' => $medDet,
            ]
        );
    }

    public function paid(Request $request, $id)
    {
        $user_med = Medical::where('id', $id)->first(); // Mengambil objek Medical dengan ID tertentu
        $userId = $user_med->user_id;
        $MedId = $user_med->med_number;
        $employees = User::where('id', $userId)->get();
        $userName = Auth::user()->name;

        $currentYear = Carbon::now()->year;
        // Cari $medBalance yang masih aktif dan memiliki tahun yang sama
        $medBalance = Emp_medical_balance::where('user_id', $userId)
            ->where('active_periode', '<=', $currentYear)->where('expiration', '>=', $currentYear)
            ->first();

        $totalAmountPaid = $request->input_total_paid;

        $balance = $medBalance->medical_balance;
        if (is_numeric($balance)) {
            $balanceAmount = str_replace('.', '', $balance);
            $AmountApproved = str_replace('.', '', $totalAmountPaid);
            $total = $balanceAmount - $AmountApproved;
            $formattedTotal = number_format($total, 0, ',', '.');

            $medBalance->medical_remaining = $formattedTotal;
            $medBalance->save();
        } else {
            // Handle the case when $balance is non-numeric
        }

        $deducted = $medBalance->medical_deducted;
        if (is_numeric($deducted) && is_numeric($totalAmountPaid)) {
            $deductedAmount = str_replace('.', '', $deducted);
            $AmountApproved = str_replace('.', '', $totalAmountPaid);
            $totalDeducted = $deductedAmount + $AmountApproved;
            $formattedDeductedTotal = number_format($totalDeducted, 0, ',', '.');

            $medBalance->medical_deducted = $formattedDeductedTotal;
            $medBalance->save();
        } else {
            // Handle the case when $deducted or $totalAmountApproved is non-numeric
        }

        $medPay = Medical_payment::where('medical_id', $id)->firstOrFail();
        $medPay->payment_date = date('Y-m-d');
        $medPay->paid_status = 29;
        $medPay->total_payment = $totalAmountPaid;
        $medPay->save();

        // foreach ($employees as $employee) {
        //     dispatch(new NotifyMedicalPaid($employee, $userName, $MedId,));
        // }
        // $userName = $user_med->user->name; // Mengambil nama pengguna terkait

        return redirect('/medical/review')->with('success', "You Have Paid $userName Medical Reimbursement ");
    }

    // Medical Manage
    public function index_manage(Request $request)
    {
        $query = Emp_medical_balance::query();

        // Filter by user_id
        if ($request->has('user_id') && $request->user_id !== '1') {
            $query->where('user_id', $request->user_id);
        }

        // Initialize $year variable
        $year = null;

        // Filter by year
        if ($request->has('year') && $request->year !== '') {
            $year = $request->year;
            $query->whereYear('active_periode', $year);
        } else {
            // Jika tidak ada filter tahun, ambil data untuk tahun saat ini
            $year = Carbon::now()->year;
            $query->whereYear('active_periode', $year);
        }

        $medBalance = $query->get();
        $user = User::all();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear, $currentYear - 10);


        return view('medical.medical_manage', ['medBalance' => $medBalance, 'user' => $user, 'years' => $years]);
    }


    public function add_balance(Request $request)
    {
        $request->validate([
            'input_user_id' => 'required',
            'input_balance' => 'required',
            'input_active_periode' => 'required',
        ]);

        $year = date('Y');
        $ExpirationDate = date($year . '-12-31');

        // Membuat entri baru dalam tabel Medicals
        $medBalance = new Emp_medical_balance();
        $medBalance->user_id = $request->input_user_id;
        $medBalance->medical_balance = $request->input_balance;
        $medBalance->medical_remaining = $request->input_balance;
        $medBalance->medical_deducted = 0;
        $medBalance->active_periode = $request->input_active_periode;
        $medBalance->expiration = $ExpirationDate;
        $medBalance->save();

        return redirect()->back()->with('success', 'Medical Balance Add Success.');
    }

    public function edit_balance(Request $request, $id)
    {
        $request->validate([
            'input_edit_balance' => 'required',
        ]);

        // Membuat entri baru dalam tabel Medicals
        $medBalance = Emp_medical_balance::where('id', $id)->first();
        $userName = $medBalance->user->name;

        $medBalance->medical_balance = $request->input_edit_balance;
        $medBalance->save();

        return redirect()->back()->with('success', "You've Successfully Edited $userName Medical Balance. ");
    }
}
