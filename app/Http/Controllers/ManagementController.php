<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Role_template;
use App\Models\User;
use Maatwebsite\Excel\Row;

class ManagementController extends Controller
{
    public function roles()
    {
        
        $users = DB::table('users')
        ->join('roles', 'users.id', '=', 'roles.user_id')
        ->select('users.id', 'users.name', 'roles.role_name', 'roles.created_at')
        ->orderBy('users.name')
        ->get();

    $usersData = [];
    $u_List= User::all();
    $r_name= Role_template::all();
    foreach ($users as $user) {
        if (!isset($usersData[$user->id])) {
            // If this is the first time we've seen this user, create a new entry in the $usersData array
            $usersData[$user->id] = [
                'name' => $user->name,
                'roles' => [],
                'created_at' => $user->created_at
            ];
        }
        
        // Add the role to the list of roles for this user
        $usersData[$user->id]['roles'][] = $user->role_name;
    }

    // Convert the $usersData array into a simple list of users with comma-delimited roles
    $usersList = [];

    foreach ($usersData as $userData) {
        $rolesList = implode(', ', $userData['roles']);
        $usersList[] = [
            'name' => $userData['name'],
            'roles' => $rolesList,
            'created_at' => $userData['created_at']
        ];
    }
        
    return view('management.roles', ['users' => $usersList, 'r_name' =>$r_name, 'us_List' => $u_List]);
    }


    public function add_roles(Request $request)
    {
        // $latestForm = Role_template::whereNull('deleted_at')->orderBy('id')->pluck('id')->first();
        // $nextForm = intval(substr($latestForm, 4))+ 1;
        $lastId = Role_template::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        $randomNumber = mt_rand(10, 90);              
        
        $this->validate($request,[
    		'new_role' => 'required',
            'new_role_code' => 'required'
    	]);

        Role_template::create([
            'id' => $nextId,
            'role' => $request->new_role_code,
    		'role_name' => $request->new_role,
            'role_id'=> $randomNumber
    	]);
        return redirect('/hrtools/manage/roles')->with('success', 'Role Create successfully');
    }

    public function assign_roles(Request $request)
    {
        $lastId = Role::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;        
        
        $this->validate($request,[
    		'inputUser' => 'required',
            'inputRole' => 'required'
    	]);

        Role::create([
            'id' => $nextId,
            'user_id' => $request->inputUser,
    		'role_name' => $request->inputRole,
    	]);
        return redirect('/hrtools/manage/roles')->with('success', 'Role Assign successfully');
    }
    public function assign_delete($user_id)
    {   
        $users = DB::table('roles')->where('user_id', $user_id)->delete();
        return redirect('/hrtools/manage/roles')->with('success', 'Role delete successfully');
    }
    
    public function delete($id)
    {   
        $r_name = DB::table('role_templates')->where('id', $id)->delete();
        return redirect('/hrtools/manage/roles')->with('success', 'Role delete successfully');
    }
    
    public function edit($id)
    {
        $r_name = Role_template::findOrFail($id);
        return view('/hrtools/manage/roles', compact('role_template'));
    }
}
