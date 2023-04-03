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
        return view('medical.medical',['med' => $med]);
        
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
        return view('medical.medical_edit', compact('med'));
    }
    public function p(Request $request)
    {
        $this->validate($request, [
            'payment_method' => 'required',
            // 'attach' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'amount' => 'required',
            // 'desc' => 'required',
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
            'med_req_date' => date('Y-m-d'),
            'med_payment' => $request->payment_method,
            'med_status' => 'New Request',
            'med_total_amount' => $request->totalAmount,
        ]);
    
        // $med_detail = new Medical_details();
    
        // if ($request->hasFile('mdet_attachment')) {
        //     $attach = $request->file('mdet_attachment');
        //     $filename = time() . '.' . $attach->getClientOriginalExtension();
        //     $attach->move(public_path('attach'), $filename);
        //     $med_detail->mdet_attachment = $filename;
        // }
    
        // $med_detail->mdet_id = $request['id'];
        // $med_detail->mdet_number = $nextMedNumber;
        // $med_detail->mdet_amount = $request['mdet_amount'];
        // $med_detail->mdet_desc = $request['mdet_desc'];
        // $med_detail->save();
    
        return redirect('medical.history')->with('success', 'Medical Reimburse Add successfully');
 
    }

    public function store(Request $request)
    {
        // Validasi data yang di-submit dari form
        $request->validate([
            'payment_method' => 'required',
            'totalAmount' => 'required',
            'attach.*' => 'required',
            'amount.*' => 'required',
            'desc.*' => 'required',
        ]);
        
        $lastId = Medical::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $nextForm = intval(substr($latestForm, 4)) + 1;
        $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);

        // Buat objek Medical dan simpan ke dalam database
        $medical = new Medical;
        $medical->med_id = $request->$nextId;
        $medical->med_number = $request->$nextMedNumber;
        $medical->med_users = $request->Auth::user()->id;
        $medical->med_payment = $request->input('payment_method');
        // $medical->med_status = $request->New Request;
        $medical->med_total_amount = $request->input('totalAmount');
        $medical->save();

        // Looping untuk mengambil data dari form dan memasukkannya ke dalam tabel medical_details
        $attachArr = $request->input('attach');
        $amountArr = $request->input('amount');
        $descArr = $request->input('desc');
        for ($i = 0; $i < count($attachArr); $i++) {
            $medicalDetail = new Medical_details;
            $medicalDetail->mdet_id = $medical->med_id;
            $medicalDetail->mdet_med_number = $medical->med_number;
            $medicalDetail->mdet_attachment = $attachArr[$i];
            $medicalDetail->mdet_amount = $amountArr[$i];
            $medicalDetail->mdet_desc = $descArr[$i];
            $medicalDetail->save();
        }

        return response()->json(['message' => 'Data berhasil disimpan']);
        // return redirect('medical.history')->with('success', 'Medical Reimburse Add successfully');
    }
}