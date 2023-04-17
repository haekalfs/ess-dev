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
        $med = Medical::all();
        // $med = Medical::find(Auth::user()->med_users);
        return view('medical.medical', ['med' => $med]);
    }
    public function entry()
    {
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $nextForm = intval(substr($latestForm, 4)) + 1;
        $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);
        return view('medical.medical_tambah', compact('nextMedNumber'));
    }

    public function edit($id)
    {
        $med = Medical::with('Medical_details')->findOrFail($id);
        return view('medical.medical_edit', ['med' => $med]);
    }

    public function stor1e(Request $request)
    {
        $this->validate($request, [
            'attach' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            'desc' => 'required',
        ]);

        // menyimpan data file yang diupload ke variabel $file
        $attach = $request->file('attach');

        $nama_file = time() . "_" . $attach->getClientOriginalName();

        // $path = storage_path('app/public/example.txt');
        // $content = Storage::get($path);

        // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'storage/med_pic';
        $attach->move($tujuan_upload, $nama_file);

        Medical_details::create([
            'mdet_id' => '1',
            'mdet_med_number' => 'med_01',
            'mdet_attachment' => $nama_file,
            'mdet_desc' => $request->desc,
        ]);

        return redirect()->back();
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'payment_method' => 'required',
            'attach' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'amount' => 'required',
            'desc' => 'required',
            'totalAmount' => 'required',

        ]);

        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $nextForm = intval(substr($latestForm, 4)) + 1;
        $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);

        Medical::create([
            'id' => $nextId,
            'med_number' => $nextMedNumber,
            'med_users' => Auth::user()->id,
            'med_payment' => $request->payment_method,
            'med_status' => 'New Request',
            'med_total_amount' => $request->totalAmount,
        ]);

        $attachArr = $request->input('attach');
        $amountArr = $request->input('amount');
        $descArr = $request->input('desc');

        // menyimpan data file yang diupload ke variabel $file
        $attach = $request->file('attach');

        $nama_file = time() . "_" . $attach->getClientOriginalName();

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

    // public function store(Request $request)
    // {
    //     // Validasi data yang di-submit dari form
    //     $request->validate([
    //         'payment_method' => 'required',
    //         'totalAmount' => 'required',
    //         'attach.*' => 'required',
    //         'amount.*' => 'required',
    //         'desc.*' => 'required',
    //     ]);

    //     $lastId = Medical::orderBy('id', 'desc')->first();
    //     $nextId = ($lastId) ? $lastId->id + 1 : 1;
    //     $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
    //     $nextForm = intval(substr($latestForm, 4)) + 1;
    //     $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);

    //     // Buat objek Medical dan simpan ke dalam database
    //     $medical = new Medical;
    //     $medical->med_id = $request->$nextId;
    //     $medical->med_number = $request->$nextMedNumber;
    //     $medical->med_users = $request->Auth::user()->id;
    //     $medical->med_payment = $request->input('payment_method');
    //     // $medical->med_status = $request->New Request;
    //     $medical->med_total_amount = $request->input('totalAmount');
    //     $medical->save();

    //     // Looping untuk mengambil data dari form dan memasukkannya ke dalam tabel medical_details
    //     $attachArr = $request->input('attach');
    //     $amountArr = $request->input('amount');
    //     $descArr = $request->input('desc');
    //     for ($i = 0; $i < count($attachArr); $i++) {
    //         $medicalDetail = new Medical_details;
    //         $medicalDetail->mdet_id = $medical->med_id;
    //         $medicalDetail->mdet_med_number = $medical->med_number;
    //         $medicalDetail->mdet_attachment = $attachArr[$i];
    //         $medicalDetail->mdet_amount = $amountArr[$i];
    //         $medicalDetail->mdet_desc = $descArr[$i];
    //         $medicalDetail->save();
    //     }


    //     // return response()->json(['message' => 'Data berhasil disimpan']);
    //     return redirect('medical.history')->with('success', 'Medical Reimburse Add successfully');
    // }
}
