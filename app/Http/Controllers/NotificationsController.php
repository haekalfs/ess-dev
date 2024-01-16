<?php

namespace App\Http\Controllers;

use App\Models\Notification_alert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class NotificationsController extends Controller
{
    public function index($userId)
    {
        $userId = Crypt::decrypt($userId);

        $notifications = Notification_alert::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
        return view('notifications.index',['notifications' => $notifications, 'userId' => $userId]);
    }
}
