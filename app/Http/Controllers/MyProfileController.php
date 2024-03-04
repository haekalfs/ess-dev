<?php

namespace App\Http\Controllers;

use App\Models\Notification_alert;
use App\Models\User;
use App\Models\Users_detail;
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

    public function upload_cv(Request $request, $id)
    {
        $user = User::find($id);
        $request->validate([
            'cv' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employeeID = $user->users_detail->employee_id;
        // $cv_file = $request->file('cv');
        // $name_file_cv = $user->id . "_" . "cv" . "." . $cv_file->getClientOriginalExtension();
        // $folder_upload_cv = '/storage/cv';
        // $cv_file->move(public_path($folder_upload_cv), $name_file_cv);

        $cv_file = $request->file('cv');

        $name_file_cv = $employeeID . "_cv" . "." . $cv_file->getClientOriginalExtension();
        $folder_upload_cv = '/cv_storage';

        // Menghapus file cv lama jika ada
        $oldCV = public_path($folder_upload_cv . '/' . $name_file_cv);
        if (file_exists($oldCV)) {
            unlink($oldCV);
        }

        $cv_file->move(public_path($folder_upload_cv), $name_file_cv);

        $cv = Users_detail::where('user_id', $id)->first();
        $cv->cv = $name_file_cv;
        $cv->save();

        return redirect()->back()->with('success', "Your CV has been uploaded Successfully");
    }
}
