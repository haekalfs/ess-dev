<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Users_detail;

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
            'nama' => 'required',
            'nama' => 'required',
            'nama' => 'required',
            'nama' => 'required',
    		'nama' => 'required',
    		'alamat' => 'required'
    	]);
 
        User::create([
    		'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'role' => $request->role
    	]);
 
    	return redirect('/manage/users');
    }

    public function delete($id)
    {
        $data = DB::table('users')->where('id', $id)->delete();
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
