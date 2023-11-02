<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Position;
use App\Models\Department;
use App\Models\Emp_leave_quota;
use App\Models\Emp_medical_balance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



class UserController extends Controller
{
    public function index()
    {
        $data = User::all();
        return view('manage.users', ['data' => $data]);
    }
    
    public function tambah()
    {
        $dep_data = Department::all();
        $pos_data = Position::all();
        $latestForm = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('employee_id')->max();
        $nextEmpID = $latestForm + 1;
    	return view('manage.users_tambah', ['dep_data' => $dep_data, 'pos_data' => $pos_data, 'nextEmpID' => $nextEmpID]);
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
            'name' => 'required',
            'password' => 'required',
            'usr_id' => ['required','unique:users,id','regex:/^[a-z0-9]+$/'],
            'email' => 'required',
            'status' => 'required',
            'employee_status' => 'required',
            'position' => 'required',
            'department' => 'required',
            'hired_date'=> 'required',
            'employee_id'=> 'required',
            'profile_pic' => 'sometimes|file|image|mimes:jpeg,png,jpg|max:2048'
            ]);
        
        $lastId = Users_detail::whereNull('deleted_at')->orderBy('id', 'desc')->pluck('id')->first();
        $nextId = intval(substr($lastId, 4)) + 1;
        $hash_pwd = Hash::make($request->password);

        // Memeriksa apakah file foto profil diunggah
        if ($request->hasFile('profile')) {
            $profile_file = $request->file('profile');
            $nama_file_profile = $request->email . "." . $profile_file->getClientOriginalExtension();
            $tujuan_upload_profile = '/storage/profile_pic';
            $profile_file->move(public_path($tujuan_upload_profile), $nama_file_profile);
        }
        else {
            // Tentukan nilai default untuk $nama_file_profile jika file tidak diunggah
            $nama_file_profile = null;
        }

        
        // Memeriksa apakah file CV diunggah
        if ($request->hasFile('cv')) {
            $cv_file = $request->file('cv');
             $nama_file_cv = $request->email . "." . $cv_file->getClientOriginalExtension();
            $tujuan_upload_cv = '/storage/cv';
            $cv_file->move(public_path($tujuan_upload_cv), $nama_file_cv);
        } else {
            // Tentukan nilai default untuk $nama_file_profile jika file tidak diunggah
            $nama_file_cv = null;
        }

        User::create([
            'id' => $request->usr_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hash_pwd,
    	]);
 
        Users_detail::create([
            'id' => $nextId,
            'user_id' => $request->usr_id,
            'employee_id' => $request->employee_id,
            'position_id' => $request->position,
            'department_id' => $request->department,
            'status_active' => $request->status,
            'employee_status' => $request->employee_status,
            'hired_date'=> $request->hired_date,
            'usr_address'=> $request->usr_address,
            'current_address'=> $request->current_address,
            'usr_address_city'=> $request->usr_address_city,
            'usr_address_postal'=> $request->usr_address_postal,
            'usr_phone_home'=> $request->usr_phone_home,
            'usr_phone_mobile'=> $request->usr_phone_mobile,
            'usr_npwp'=> $request->usr_npwp,
            'usr_id_type'=> $request->usr_id_type,
            'usr_id_no'=> $request->usr_id_no,
            'usr_id_expiration'=> $request->usr_id_expiration,
            'usr_dob'=> $request->usr_dob,
            'usr_birth_place'=> $request->usr_birth_place,
            'usr_gender'=> $request->usr_gender,
            'usr_religion'=> $request->usr_religion,
            'usr_merital_status'=> $request->usr_merital_status,
            'usr_children'=> $request->usr_children,
            'usr_bank_name'=> $request->usr_bank_name,
            'usr_bank_branch'=> $request->usr_bank_branch,
            'usr_bank_account'=> $request->usr_bank_account,
            'usr_bank_account_name' => $request->usr_bank_account_name,
            'profile_pic' => $nama_file_profile,
            'cv' => $nama_file_cv,
        ]);

        $emp_leave = new Emp_leave_quota();
        $emp_leave->user_id = $request->usr_id;
        $emp_leave->leave_id = 10;
        $emp_leave->quota_used = 0;
        $emp_leave->quota_left = 0;
        $emp_leave->save();

        $med_balance = new Emp_medical_balance();
        $med_balance->user_id = $request->usr_id;
        $med_balance->medical_balance = 0;
        $med_balance->medical_deducted = 0;
        $med_balance->save();



    	return redirect('/manage/users')->with('success', 'User Create successfully');
    }

    public function delete($id)
    {
        $data = DB::table('users')->where('id', $id)->delete();
        $data = DB::table('users_details')->where('user_id', $id)->delete();
        return redirect()->back()->with('success', 'User delete successfully');
    }
    

    public function edit($id)
    {
        $user = User::with('users_detail')->findOrFail($id);
        $dep_data = Department::all();
        $pos_data = Position::all();
        return view('manage.users_edit', ['user'=> $user, 'dep_data' => $dep_data, 'pos_data' => $pos_data]);
    }
    
    public function update(Request $request, $id)
    {
        $dep_data = Department::all();
        $pos_data = Position::all();
        $user = User::find($id);
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
            'status' => 'required',
            'position' => 'required',
            'department' => 'required',
            'hired_date'=> 'required',
            'employee_id' => 'required',
            'employee_status' => 'required',
            'usr_id_type'=> 'required',
            'usr_id_no'=> 'required',
            'usr_id_expiration'=> 'required',
            'usr_dob'=> 'required',
            'usr_birth_place'=> 'required',
            'usr_gender'=> 'required',
            'usr_religion'=> 'required',
            ]);

        // Memeriksa apakah file foto profil diunggah
        if ($request->hasFile('profile')) {
            $profile_file = $request->file('profile');
            $nama_file_profile = $request->email.".".$profile_file->getClientOriginalExtension();
            $tujuan_upload_profile = 'profile_pic';

            // Menghapus file profil lama jika ada
            $oldProfileImage = public_path($tujuan_upload_profile . '/' . $nama_file_profile);
            if (file_exists($oldProfileImage)) {
                unlink($oldProfileImage);
            }

            // Memindahkan file profil yang baru diunggah
            $profile_file->storeAs('public/' . $tujuan_upload_profile, $nama_file_profile);
        
        } elseif ($user->users_detail->profile_pic) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $nama_file_profile = $user->users_detail->profile_pic;
        } else {
            // Tidak ada foto profil di database dan tidak ada unggahan baru, set nilai menjadi null
            $nama_file_profile = null;
        }

        // Memeriksa apakah file CV diunggah
        if ($request->hasFile('cv')) {
            $cv_file = $request->file('cv');
            
            $nama_file_cv = $request->usr_id . "." . $cv_file->getClientOriginalExtension();
            $tujuan_upload_cv = 'cv';

            // Menghapus file profil lama jika ada
            $oldCV = public_path($tujuan_upload_cv . '/' . $nama_file_cv);
            if (file_exists($oldCV)) {
                unlink($oldCV);
            }

            $cv_file->storeAs('public/' . $tujuan_upload_cv, $nama_file_cv);
        } elseif ($user->users_detail->cv) {
            // Menggunakan foto profil yang sudah ada dalam database jika ada
            $nama_file_cv = $user->users_detail->cv;
        } else {
            // Tidak ada foto profil di database dan tidak ada unggahan baru, set nilai menjadi null
            $nama_file_cv = null;
        }
            $user = User::find($id);
            $user->id = $request->usr_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            $user_detail = Users_detail::where('user_id',$id)->first();
            $user_detail->user_id = $request->usr_id;
            $user_detail->status_active = $request->status;
            $user_detail->department_id = $request->department;
            $user_detail->position_id = $request->position;
            $user_detail->employee_id = $request->employee_id;
            $user_detail->usr_dob = $request->usr_dob;
            $user_detail->usr_birth_place = $request->usr_birth_place;
            $user_detail->usr_gender = $request->usr_gender;
            $user_detail->usr_npwp  = $request->usr_npwp;
            $user_detail->usr_religion = $request->usr_religion;
            $user_detail->usr_merital_status = $request->usr_merital_status;
            $user_detail->usr_children = $request->usr_children;
            $user_detail->usr_id_type = $request->usr_id_type;
            $user_detail->usr_id_no= $request->usr_id_no;
            $user_detail->usr_id_expiration = $request->usr_id_expiration;
            $user_detail->employee_status = $request->employee_status;
            $user_detail->hired_date = $request->hired_date;
            $user_detail->resignation_date = $request->resignation_date;
            $user_detail->usr_address = $request->usr_address;
            $user_detail->usr_address_city = $request->usr_address_city;
            $user_detail->usr_address_postal = $request->usr_address_postal;
            $user_detail->usr_phone_home = $request->usr_phone_home;
            $user_detail->usr_phone_mobile = $request->usr_phone_mobile;
            $user_detail->usr_bank_name = $request->usr_bank_name;
            $user_detail->usr_bank_branch = $request->usr_bank_branch;
            $user_detail->usr_bank_account = $request->usr_bank_account;
            $user_detail->usr_bank_account_name = $request->usr_bank_account_name;
            $user_detail->current_address = $request->current_address;
            $user_detail->profile_pic = $nama_file_profile;
            $user_detail->cv = $nama_file_cv;
            $user_detail->save();


        return view('manage.users_edit', [
            'user' => $user,
            'dep_data' => $dep_data,
            'pos_data' => $pos_data,
            'success' => 'User Edit successfully'
        ]);
    }

}
