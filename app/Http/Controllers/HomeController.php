<?php

namespace App\Http\Controllers;

use App\Models\Emp_leave_quota;
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
        $empLeaveQuotaAnnual = Emp_leave_quota::where('user_id', Auth::user()->id)
            ->where('leave_id', 10)
            ->where('active_periode', '>=', date('Y-m-d'))
            ->sum('quota_left');
        $empLeaveQuotaFiveYearTerm = Emp_leave_quota::where('active_periode', '>=', date('Y-m-d'))->where('user_id', Auth::user()->id)->where('leave_id', 20)->pluck('quota_left')->first();
        $totalQuota = $empLeaveQuotaAnnual + $empLeaveQuotaFiveYearTerm;
        if($empLeaveQuotaFiveYearTerm == NULL){
            $empLeaveQuotaFiveYearTerm = "-";
        }
       return view('home', compact('empLeaveQuotaAnnual', 'empLeaveQuotaFiveYearTerm', 'totalQuota'));
    }

    public function notification_indev()
    {
        Session::flash('warning',"That page is still under development! Thankyou for your patience :)");
        return redirect('home');
    }
}
