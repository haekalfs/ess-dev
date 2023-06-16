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
        return view('medical.medical_tambah', compact('nextId'));
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
        // $nextMedNumber = str_pad($nextId, 5, '0', STR_PAD_LEFT);
        //Med Number
        // $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        // $nextForm = intval(substr($latestForm, 4)) + 1;

        $medical = new Medical();
        $medical->id = $nextId;
        $medical->user_id = Auth::user()->id;
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

            $extension = $attachment->getClientOriginalExtension();
            $filename = 'MED_0000' . $nextId . '_' . $key .  '_' .  Auth::user()->id . '.' . $extension;

            // Menyimpan file attach ke dalam folder yang diinginkan
            $attach_tujuan = '/storage/med_pic';
            $attachment->move(public_path($attach_tujuan), $filename);

            // Membuat entri baru dalam tabel medical
            
            $meddet = new Medical_details();
            $meddet->mdet_id = $nextmdet;
            $meddet->medical_number = $nextId;
            $meddet->mdet_attachment = $filename;
            $meddet->mdet_amount = $amounts[$key];
            $meddet->mdet_desc = $descriptions[$key];
            $meddet->save();
        }

        return redirect('/medical/history')->with('Success', 'Medical Reimburse Add successfully');
    }

    public function edit($id)
    {
        $user_info = User::find(Auth::user()->id);
        $med = Medical::findOrFail($id);
        $medDet = Medical_details::where('medical_number', $med->id)->get();
        return view('medical.medical_edit', ['med' => $med, 'user_info' => $user_info, 'medDet' => $medDet ]);
    }

    public function delete_med_all($id)
    {
        DB::table('medicals')->where('id', $id)->delete();
        DB::table('medicals_detail')->where('medical_number', $id)->delete();
        $user = Auth::user();
        $med = Medical::where('user_id', $user->id)->get();
        return view('medical.medical', ['med' => $med])->with('Success', 'Medical Reimburse Delete successfully');
    }

    public function update_medDetail(Request $request, $mdet_id)
    {
        $user_info = User::find(Auth::user()->id);
        $medDet = Medical_details::find($mdet_id);
        $request->validate([
            'attach.*' => 'required|file',
            'amount.*' => 'required',
            'desc.*' => 'required',
        ]);

        if ($request->hasFile('inputAttach')){
            $inputAttach = $request->file('inputAttach');

            $filename = $medDet->mdet_attachment;
            $attach_tujuan = '/storage/med_pic';
            $inputAttach->move(public_path($attach_tujuan), $filename);

            // Menghapus file profil lama jika ada
            // $oldCV = public_path($tujuan_upload_cv . '/' . $nama_file_cv);
            // if (file_exists($oldCV)) {
            //     unlink($oldCV);
            // }

        } elseif ($medDet->mdet_attachment) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $filename = $medDet->mdet_attachment;
        }
        $med_detail = Medical_details::find($mdet_id);
        $med_detail->mdet_attachment = $filename;
        $med_detail->mdet_amount = $request->input_mdet_amount;
        $med_detail->mdet_desc = $request->input_mdet_desc;
        $med_detail->save();
        
        return view('medical.medical_edit', ['user_info' => $user_info, 'medDet' => $medDet, 'success' => 'User Edit successfully']);

    }
}