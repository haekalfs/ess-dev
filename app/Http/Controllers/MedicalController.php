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
        // $string = "MED_00001";
        // $prefix = $pieces[0]; // "MED"
        // $latestTitle = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        // $nextTitle = $latestTitle + 1;
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $pieces = explode('_', $latestForm);
        $number = $pieces[1]; // "00001"
        $nextForm = intval($number) + 1;
        $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);
        return view('medical.medical_tambah', compact('nextMedNumber'));
        
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
            'id' => 'required',
    		'med_number' => 'required',
            'med_users' => 'required',
            'med_req_date' => 'required',
            'med_payment' => 'required',
            'med_status' => 'required',
            'med_total_amount' => 'required',
            'mdet_id' => 'required',
            'mdet_attachment' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mdet_amount' => 'required|numeric',
            'mdet_desc' => 'required|string|max:255',
    	]);

        $uniqueId = hexdec(substr(uniqid(), 0, 8));
        $latestForm = Medical::whereNull('deleted_at')->orderBy('med_number')->pluck('med_number')->first();
        $pieces = explode('_', $latestForm);
        $number = $pieces[1]; // "00001"
        $nextForm = intval($number) + 1;
        $nextMedNumber = 'MED_' . str_pad($nextForm, 5, '0', STR_PAD_LEFT);
        $request_date = date('Y-m-d'); //ambil tanggal sekarang

        while (Medical::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }


        Medical::create([
            'id' => $uniqueId,
    		'med_number' => $nextMedNumber,
            'med_users' => Auth::user()->id,
            'med_req_date' => $request_date,
            'med_payment' => $request->method_payment,
            'med_status' => 'New Request',
            'med_total_amount' => $request->totalAmount,
    	]);
        
        $med_detail = new Medical_details();

        if ($request->hasFile('attach')) {
            $attach = $request->file('attach');
            $filename = time() . '.' . $attach->getClientOriginalExtension();
            $attach->move(public_path('attach'), $filename);
            $med_detail->mdet_attachment = $filename;
        }

        $med_detail->mdet_id = $request[$uniqueId];
        $med_detail->mdet_number = $request[$nextMedNumber];
        $med_detail->mdet_amount = $request['amount'];
        $med_detail->mdet_desc = $request['desc'];
        $med_detail->save();

        return redirect('medical.history')->with('success', 'Medical Reimburse Add successfully');

        // menyimpan data file yang diupload ke variabel $file
        // $file = $request->file('attach');

        // // nama file
        // echo 'File Name: '.$file->getClientOriginalName();
        // echo '<br>';

        // // ekstensi file
        // echo 'File Extension: '.$file->getClientOriginalExtension();
        // echo '<br>';

        // // real path
        // echo 'File Real Path: '.$file->getRealPath();
        // echo '<br>';

        // // ukuran file
        // echo 'File Size: '.$file->getSize();
        // echo '<br>';

        // // tipe mime
        // echo 'File Mime Type: '.$file->getMimeType();

        // // isi dengan nama folder tempat kemana file diupload
        // $upload_folder = public_path('img/');

        // // upload file
        // $file->move($upload_folder, Auth::medical_details()->mdet_number.".".$file->getClientOriginalExtension());

        // $mdet = medical_details::find(Auth::user()->id);
        // $mdet->mdet_attachment = Auth::user()->id.".".$file->getClientOriginalExtension();
        // $mdet->save();
        
        // Medical_details::create([
        //     'id' => $uniqueId,
        //     'mdet_number' => $nextMedNumber,
        //     'mdet_attachment' => $request-> attach,
        //     'mdet_amount' => $request-> amount,
        //     'mdet_desc' => $request-> desc,

        // ]);

        // // Validate the form data
        // $data = $request->validate([
        //     'mdet_attachment.*' => 'required',
        //     'mdet_amount.*' => 'required',
        //     'mdet_desc.*' => 'required'
        // ]);

        // $attach = $request->file('attach');
        // $amount = $request->input('amount');
        // $desc = $request->input('desc');
    
        // if ($request->hasFile('attach')) {
        //     foreach ($attach as $key => $value) {
        //         $imageName = time() . '_' . $value->getClientOriginalName();
        //         $value->move(public_path('images'), $imageName);
        //         $data[] = array(
        //             'attach' => $imageName,
        //             'amount' => $amount[$key],
        //             'desc' => $desc[$key]
        //         );
        //     }
        // }

        // for($i = 0; $i < count($attach); $i++){
        //     DB::table('Medical_details')->insert([
        //         'mdet_attachment' => $attach[$i],
        //         'mdet_amount' => $amount[$i],
        //         'mdet_desc' => $desc[$i]
        //     ]);
        // }
        
        // Count the number of items in the arrays
        // $num_items = count($data['attach']);
        // $num_units = count($data['amount']);
        // $num_units = count($data['desc']);

        // if ($num_items == $num_units) {
        //     for ($i = 0; $i < count($attach); $i++) {
        //         $data = new Medical;
        //         $data->no_item = $i+1;
        //         $data->attach = $attach[$i];
        //         $data->amount = $amount[$i];
        //         $data->desc = $desc[$i];
        //         $data->id = $uniqueId;
        //         $data->save();
        //     }
        //     $med_no = Medical::whereNull('deleted_at')->orderBy('med_number', 'desc')->pluck('id')->first();
        //     Session::flash('success',"Purchase Order #$med_no Has Been Created!");
        //     return redirect('/medical/history');
        // }else {
        //     Session::flash('failed',"Error Database has Occured! Failed to create purchase order!");
        //     return redirect('/medical/history');
        // }
    }
}