<?php

namespace App\Http\Controllers;

use App\Models\Emp_leave_quota;
use App\Models\Headline;
use App\Models\News_feed;
use App\Models\Notification_alert;
use App\Models\Project_assignment_user;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
        // Check if the quotes data is cached
        if (!Cache::has('quotes')) {
            // Read the contents of the JSON file if not cached
            $quotesJson = file_get_contents(public_path('quotes.json'));

            // Convert JSON string to an associative array
            $quotesArray = json_decode($quotesJson, true);

            // Cache the quotes data for 24 hours (adjust the time according to your needs)
            Cache::put('quotes', $quotesArray, now()->addHours(24));
        }

        // Get quotes data from the cache
        $quotesArray = Cache::get('quotes');

        if ($quotesArray && is_array($quotesArray) && count($quotesArray) > 0) {
            // Select a random quote from the fetched data
            $randomQuote = $quotesArray[array_rand($quotesArray)];

            // Set the quote and author separately in the session
            if (isset($randomQuote['quote']) && isset($randomQuote['author'])) {
                Session::flash('quotes', 'Daily Qoutes : ' . $randomQuote['quote'] . ' 🎉✨🔢');
                Session::flash('author', 'Daily Qoutes : ' . $randomQuote['author']);
            } else {
                // Handle missing quote or author data from the file
                Session::flash('quotes', 'No quote available');
                Session::flash('author', 'Unknown author');
            }
        } else {
            // Handle empty or invalid data from the file or cache
            Session::flash('quotes', 'No quotes available');
            Session::flash('author', 'Unknown author');
        }

        $newsFeed = News_feed::orderBy('created_at', 'desc')->get();

        try {
            $roles = Auth::user()->role_id()->pluck('role_name')->toArray();
            if (!session()->has('allowed_roles')) {
                session()->put('allowed_roles', $roles);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        // Get the current year and month
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Set the start date and end date for the current month
        $startDate = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($currentYear, $currentMonth)->endOfMonth();

        // Fetch data for the current month and user
        $findAssignment = Project_assignment_user::where('user_id', Auth::id())
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('periode_start', '<=', $endDate->format('Y-m-d'))
                    ->where('periode_end', '>=', $startDate->format('Y-m-d'));
            })
            ->get();

        // Count the number of records
        $countAssignments = $findAssignment->count();



        $reimbursementCount = Reimbursement::where('f_req_by', Auth::id())->whereYear('created_at', $currentYear)->count();

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

        $headline = Headline::all();
       return view('home', compact('empLeaveQuotaAnnual', 'countAssignments', 'headline', 'newsFeed','reimbursementCount', 'totalQuota'));
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
