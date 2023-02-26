<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyProfileController extends Controller
{
    public function index()
    {
        $user_info = User::find(Auth::user()->id);
        return view('profile.myprofile',['user_info' => $user_info]);
    }
}
