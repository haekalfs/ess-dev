<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Models\User;

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
        return redirect()->back();
    }
    

    public function edit(User $user, Users_detail $users_detail)
    {
        return view('manage.users_edit', compact('user', 'users_detail'));
    }
    
    public function update($id, Request $request)
    {
        $this->validate($request,[
        'employee_id' => 'required',   
        'name' => 'required',
        'email' => 'required',
        'posisi' => 'required',
        ]);
    
        $data = User::find($id);
        $data->employee_id = $request->employee_id;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->posisi = $request->posisi;
        $data->save();
        return redirect('/manage/users');
    }
}
