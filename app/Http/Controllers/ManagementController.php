<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    return view('management.roles', ['users' => $usersList]);
    }

    public function add_roles(Request $request)
    {
        $this->validate($request,[
            'date_prepared' => 'required',
    		'po_req_number' => 'required'
    	]);


        return view('projects.assigning', compact('assignment', 'project'));
    }
}
