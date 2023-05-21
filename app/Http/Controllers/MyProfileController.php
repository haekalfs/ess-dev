<?php

namespace App\Http\Controllers;

use App\Models\Notification_alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyProfileController extends Controller
{
    public function index()
    {
        if((session('status'))){
            $entry = new Notification_alert();
            $entry->user_id = Auth::user()->id;
            $entry->message = "An email to reset your password has been sent!";
            $entry->importance = 1;
            $entry->save();
        }
        $user_info = User::find(Auth::user()->id);
        return view('profile.myprofile',['user_info' => $user_info]);
    }
}
