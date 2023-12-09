<?php

namespace App\Http\Controllers;

use App\Models\Emp_leave_quota;
use App\Models\Headline;
use App\Models\News_feed;
use App\Models\Notification_alert;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $newsFeed = News_feed::orderBy('created_at', 'desc')->get();

        try {
            $roles = Auth::user()->role_id()->pluck('role_name')->toArray();
            if (!session()->has('allowed_roles')) {
                session()->put('allowed_roles', $roles);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', 10)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaWeekendReplacement = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', 100)
            ->where('expiration', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('expiration', '>=', date('Y-m-d'))
            ->where('user_id', Auth::user()->id)
            ->where('leave_id', 20)
            ->sum('quota_left');
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm + $empLeaveQuotaWeekendReplacement;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }
        $headline = Headline::all();
       return view('home', compact('empLeaveQuotaAnnual', 'empLeaveQuotaWeekendReplacement', 'headline', 'newsFeed','empLeaveQuotaFiveYearTerm', 'totalQuota'));
    }

    public function notification_indev()
    {
        Session::flash('warning',"That page is still under development! Thankyou for your patience :)");
        return redirect('home');
    }

    public function changeStatus($id)
    {
        try {
            // Retrieve the notification
            $notification = Notification_alert::findOrFail($id);

            // Update the status
            $notification->update(['read_stat' => true]);

            if ($notification->type) {
                // Update other rows with the same type and month_periode
                Notification_alert::where('type', $notification->type)
                    ->where('month_periode', $notification->month_periode)
                    ->where('user_id', Auth::id())
                    ->where('id', '!=', $id)
                    ->update(['read_stat' => true]);
            }

            return response()->json(['success' => 'read.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
