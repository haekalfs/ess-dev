<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmployeesDatabase extends Controller
{
    public function index()
    {
        $listUser = User::all();
        return view('management.database.employees', ['users' => $listUser]);
    }
}
