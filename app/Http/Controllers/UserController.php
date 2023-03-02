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
            'employee_id' => 'required',   
            'name' => 'required',
            'email' => 'required',
            'posisi' => 'required',
            ]);
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->update();

            $user_details_save = Users_detail::where('user_id',$id)->first();
            $user_details_save->employee_id = $request->employee_id;
            $user_details_save->position = $request->posisi;
            $user_details_save->save();


        return redirect('/manage/users')
            ->with('success', 'User updated successfully');
    }
    // public function update($id, Request $request)
    // {
        // $this->validate($request,[
        // 'employee_id' => 'required',   
        // 'name' => 'required',
        // 'email' => 'required',
        // 'posisi' => 'required',
        // ]);
    
        // $data = User::find($id);
        // $data->employee_id = $request->employee_id;
        // $data->name = $request->name;
        // $data->email = $request->email;
        // $data->posisi = $request->posisi;
        // $data->save();
    //     return redirect('/manage/users');
    // }
}
