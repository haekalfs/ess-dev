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
        $data = DB::table('users')->get();
        return view('manage.users', ['data' => $data]);
    }
    
    public function delete($id)
    {
        $data = DB::table('users')->where('id', $id)->delete();
        return redirect()->back();
    }
    
    public function edit($id)
    {
        $data = User::find($id);
        return view('manage.users_edit', ['data' => $data]);
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
        $data->alamat = $request->alamat;
        $data->save();
        return redirect('/users');
    }
}
