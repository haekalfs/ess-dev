<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeesDatabase extends Controller
{
    public function index(Request $request)   
    {
        // $listUser = User::with('users_detail');
        // $positionIds = explode(',', $request->position_id);
        // $listUser = User::join('users_details', 'users.id', '=', 'users_details.user_id')
        // ->whereIn('users_details.position_id', $positionIds)
        //     ->get();
        $positionIds = explode(',', $request->position_id);
        $users = User::join('users_details', 'users.id', '=', 'users_details.user_id');

        if (!empty($request->position_id)) {
            $users->whereIn('users_details.position_id', $positionIds);
        }

        $listUser = $users->get();

        return view('management.database.employees', ['users' => $listUser]);
    }
}
