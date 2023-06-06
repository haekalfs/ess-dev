<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Medical;
use App\Models\Medical_details;
use App\Models\User;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;



class MedicalController extends Controller
{
    public function index()
    {
        // Mendapatkan pengguna yang login
        $user = Auth::user();

        // Mendapatkan data medis sesuai dengan pengguna yang login
        $med = Medical::where('user_id', $user->id)->get();

        // $med = Medical::find(Auth::user()->id);
        return view('medical.medical', ['med' => $med]);
    }
    public function entry()
    {
        // $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        // $nextForm = intval(substr($latestForm, 4)) + 1;
        // $nextMedNumber = str_pad($nextForm, 5, '0', STR_PAD_LEFT);
        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $nextMedNumber = str_pad($nextId, 5, '0', STR_PAD_LEFT);
        return view('medical.medical_tambah', compact('nextMedNumber'));
    }

    public function edit($id)
    {
        $med = Medical::with('Medical_details')->findOrFail($id);
        return view('medical.medical_edit', ['med' => $med]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'attach.*' => 'required|file',
            'amount.*' => 'required',
            'desc.*' => 'required',
        ]);

        $attachments = $request->file('attach');
        $amounts = $request->input('amount');
        $descriptions = $request->input('desc');

        // ID Medical
        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $nextMedNumber = str_pad($nextId, 5, '0', STR_PAD_LEFT);
        //Med Number
        // $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        // $nextForm = intval(substr($latestForm, 4)) + 1;

        $medical = new Medical();
        $medical->id = $nextMedNumber;
        $medical->user_id = Auth::user()->id;
        // $medical->med_number = $nextMedNumber;
        $medical->med_req_date = date('Y-m-d');
        $medical->med_payment = $request->payment_method;
        $medical->med_status = 'New Request';
        $medical->med_total_amount = $request->totalAmountInput;
        $medical->save();

        // Memproses setiap data yang diinputkan
        foreach ($attachments as $key => $attachment) {
            // ID Medical details
            $lastmdet = Medical_details::orderBy('mdet_id', 'desc')->first();
            $nextmdet = ($lastmdet) ? $lastmdet->mdet_id + 1 : 1;

            $filename = $nextMedNumber. "." .$attachment->getClientOriginalExtension();

            // Menyimpan file attach ke dalam folder yang diinginkan
            $attach_tujuan = '/storage/med_pic';
            $attachment->move(public_path($attach_tujuan), $filename);

            // Membuat entri baru dalam tabel medical
            
            $meddet = new Medical_details();
            $meddet->mdet_id = $nextmdet;
            $meddet->medical_number = $nextMedNumber;
            $meddet->mdet_attachment = $filename;
            $meddet->mdet_amount = $amounts[$key];
            $meddet->mdet_desc = $descriptions[$key];
            $meddet->save();
        }

        return redirect('/medical/history')->with('Success', 'Data medis berhasil disimpan.');
    }



    public function sto1re(Request $request)
    {
        $this->validate($request, [
            // 'payment_method' => 'required',
            // 'attach' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'amount' => 'required',
            // 'desc' => 'required',
            // 'totalAmount' => 'required',

        ]);

        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $nextForm = intval(substr($latestForm, 4)) + 1;
        $nextMedNumber = str_pad($nextForm, 5, '0', STR_PAD_LEFT);

        Medical::create([
            'id' => $nextId,
            'med_number' => $nextMedNumber,
            'user_id' => Auth::user()->id,
            'med_req_date'=> date('Y-m-d'),
            'med_payment' => $request->payment_method,
            'med_status' => 'New Request',
            'med_total_amount' => $request->totalAmount,
        ]);

        $attachArr = $request->input('attach');
        $amountArr = $request->input('amount');
        $descArr = $request->input('desc');

        // menyimpan data file yang diupload ke variabel $file
        $attach = $request->file('attach');

        $nama_file = Auth::user()->id . "_" . $nextMedNumber . "_" . $attach->getClientOriginalName();

        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'storage/med_pic';
        $attach->move($tujuan_upload, $nama_file);

        // Count the number of items in the arrays
        $num_attach = count(['attach']);
        $num_amount = count(['amount']);
        $num_desc = count(['desc']);

        if ($num_attach == $num_amount) {
            for ($i = 0; $i < count($attachArr); $i++) {
                $data = new Medical_details();
                $data->mdet_id = $i + 1;
                $data->mdet_attachment = $attachArr[$i];
                $data->mdet_amount = $amountArr[$i];
                $data->mdet_desc = $descArr[$i];
                $data->mdet_med_number = $nextMedNumber;
                $data->save();
            }
            //     $Med_number = Medical::whereNull('deleted_at')->orderBy('f_requisition_num', 'desc')->pluck('f_id')->first();
            //     Session::flash('success',"Purchase Order #$Med_number Has Been Created!");
            //     return redirect('/myform');
            // } else {
            //     Session::flash('failed',"Error Database has Occured! Failed to create purchase order!");
            //     return redirect('/myform');
        }

        return redirect('medical.history')->with('success', 'Medical Reimburse Add successfully');
    }

}