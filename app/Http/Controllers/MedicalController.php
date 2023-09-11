<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\Medical;
use App\Models\Medical_approval;
use App\Models\Medical_details;
use App\Models\User;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function GuzzleHttp\Promise\all;

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
        $data = $request->all();

        // Tampilkan data yang diterima dari form
        dd($data); 
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

    public function update_medDetail(Request $request, $mdet_id, $medical_number)
    {
        
        $medDet = Medical_details::where('mdet_id', $medical_number)->first();
        $request->validate([
            'attach.*' => 'sometimes|file',
            'amount.*' => 'sometimes',
            'desc.*' => 'sometimes',
        ]);

        if ($request->hasFile('inputAttach')){
            $inputAttach = $request->file('inputAttach');

            $extension = $inputAttach->getClientOriginalExtension();
            $filename = 'MED0000' . $medical_number . '_' . $mdet_id .  '_' .  Auth::user()->id . '.' . $extension;
            
            $attach_tujuan = '/med_pic';
            $inputAttach->storeAs('public/' . $attach_tujuan, $filename);

            // Menghapus file profil lama jika ada
            $oldCV = public_path($attach_tujuan . '/' .$filename);
            if (file_exists($oldCV)) {
                unlink($oldCV);
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

    public function delete_medDetail($mdet_id, $medical_number){
        DB::table('medicals_detail')
        ->where('mdet_id', $medical_number)
        ->delete();

        return redirect()->back()->with('success', 'Medical Detail Delete Success.');
    }


// Approval Medical

    public function approval_edit($id){
        $med = Medical::findOrFail($id);
        $medDet = Medical_details::where('medical_number', $med->id)->get();
        return view('medical.medical_edit_approval', ['med' => $med, 'medDet' => $medDet]);
    }

    public function update_approval (Request $request, $medical_number){
        $medDet = Medical_details::where('mdet_id', $medical_number)->first();
        $request->validate([
            'amount_approved.*' => 'sometimes',
            'desc.*' => 'sometimes',
        ]);

        $medDet->amount_approved = $request->input_mdet_amount_approved;
        $medDet->mdet_desc = $request->input_mdet_desc;
        $medDet->save();

        return redirect()->back()->with('success', 'Medical Approval Edit Success');
    }

    public function approve(Request $request, $id)
    {

        $data = $request->all();

        // Tampilkan data yang diterima dari form
        dd($data);
        $request->validate([
            'input_approve_note' => 'required',
        ]);

        $totalAmountApproved = $request->input('totalAmountApprovedInput');
        // Lakukan pengolahan atau penyimpanan ke database menggunakan $totalAmountApproved
        // ...

        $medical = Medical::findOrFail($id);
        $medical->approved_by = $request->approved_name;
        $medical->approved_date = $request->date_approved;
        $medical->approved_note = $request->input_approve_note;
        $medical->total_approved = $totalAmountApproved;
        $medical->med_status = 'Approved';
        $medical->save();
        return redirect()->back()->with('success', 'Medical Approval Edit Success');
    }
}