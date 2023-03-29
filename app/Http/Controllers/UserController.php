<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = User::all();
        return view('manage.users', ['data' => $data]);
        
    }
    
    public function tambah()
    {
    	return view('manage.users_tambah');
    }

    public function store(Request $request)
    {
    	$this->validate($request,[
            'name' => 'required',
            'password' => 'required',
            'usr_id' => 'required',
            'email' => 'required',
            'status' => 'required',
            'employee_status' => 'required',
            'position' => 'required',
            'hired_date'=> 'required',
            'employee_id'=> 'required',
            'usr_address'=> 'required',
            'current_address'=> 'required',
            'usr_address_city'=> 'required',
            'usr_address_postal'=> 'required',
            'usr_phone_home'=> 'required',
            'usr_phone_mobile'=> 'required',
            'usr_npwp'=> 'required',
            'usr_id_type'=> 'required',
            'usr_id_no'=> 'required',
            'usr_id_expiration'=> 'required',
            'usr_dob'=> 'required',
            'usr_birth_place'=> 'required',
            'usr_gender'=> 'required',
            'usr_religion'=> 'required',
            'usr_merital_status'=> 'required',
            'usr_children'=> 'required',
            'usr_bank_name'=> 'required',
            'usr_bank_branch'=> 'required',
            'usr_bank_account'=> 'required',
            ]);
        
        $lastId = Users_detail::orderBy('id', 'desc')->first()->id;
        $hash_pwd = Hash::make($request->password);

        User::create([
    		// 'id' => $request->id,
            'id' => $request->usr_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $hash_pwd,
    	]);
 
        Users_detail::create([
            'id' => $lastId + 1,
            'user_id' => $request->usr_id,
            'employee_id' => $request->employee_id,
            'position' => $request->position,
            'status' => $request->status,
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
        ]);

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
        return view('manage.users_edit', compact('user'));
    }
    
    public function update(Request $request, $id)
    {
        
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
            'status' => 'required',
            'position' => 'required',
            'hired_date'=> 'required',
            'employee_id'=> 'required',
            'usr_address'=> 'required',
            'current_address'=> 'required',
            'usr_address_city'=> 'required',
            'usr_address_postal'=> 'required',
            'usr_phone_home'=> 'required',
            'usr_phone_mobile'=> 'required',
            'usr_npwp'=> 'required',
            'usr_id_type'=> 'required',
            'usr_id_no'=> 'required',
            'usr_id_expiration'=> 'required',
            'usr_dob'=> 'required',
            'usr_birth_place'=> 'required',
            'usr_gender'=> 'required',
            'usr_religion'=> 'required',
            'usr_merital_status'=> 'required',
            'usr_children'=> 'required',
            'usr_bank_name'=> 'required',
            'usr_bank_branch'=> 'required',
            'usr_bank_account'=> 'required',
            ]);
            
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            $user_detail = Users_detail::where('user_id',$id)->first();
            $user_detail->status = $request->status;
            $user_detail->position = $request->position;
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
            $user_detail->current_address = $request->current_address;
            $user_detail->save();


        return redirect('/manage/users')->with('success', 'User updated successfully');
    }
}
