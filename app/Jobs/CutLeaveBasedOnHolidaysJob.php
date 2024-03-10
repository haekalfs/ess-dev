<?php

namespace App\Jobs;

use App\Mail\CutLeaveQuotaEmp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Emp_leave_quota;
use App\Models\Leave_request_history;
use App\Models\Notification_alert;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CutLeaveBasedOnHolidaysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        date_default_timezone_set("Asia/Jakarta");

        $cachedData = Cache::get('holiday_data');
        $maxAttempts = 15;
        $attempts = 0;

        while (!$cachedData && $attempts < $maxAttempts) {
            try {
                $json = file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json");
                $array = json_decode($json, true);
                Cache::put('holiday_data', $array, 60 * 24); // Cache the data for 24 hours
                $cachedData = $array;
            } catch (Exception $e) {
                // Handle exception or log error
                sleep(5); // Wait for 5 seconds before retrying
                $attempts++;
            }
        }

        if (!$cachedData) {
            Session::flash('failed', 'No Internet Connection, Please Try Again Later!');
            return redirect(url()->previous());
        } else {
            $array = $cachedData;
            // Use the cached data
        }

        // $json = file_get_contents("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/calendar.json");
        // $array = json_decode($json, true);

        $year = date('Y'); // Specify the year you want to retrieve data for
        $month = date('m');
        $holidayKeyword = "Cuti Bersama"; // Specify the keyword you want to filter

        // Get the start and end dates for the selected month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        $totalHolidays = 0;
        $holidaysName = [];

        // Iterate over each day in the month
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateToCheck = $date->format('Y-m-d');

            // Check if the date is a holiday and matches the keyword
            if (isset($array[$dateToCheck]) && $array[$dateToCheck]['holiday'] === true && isset($array[$dateToCheck]['summary'][0]) && strpos($array[$dateToCheck]['summary'][0], $holidayKeyword) !== false) {
                $holidaysName[] = json_encode($array[$dateToCheck]['summary']);
                $totalHolidays++;
            }
        }

        $monthPeriod = $year.intval($month);
        $checkIfAlreadyCut = Leave_request_history::whereYear('created_at', $year)
            ->where('leave_request_id', $monthPeriod)
            ->get();

        $currentDate = Carbon::now();
        $getUsers = Emp_leave_quota::pluck('user_id')->toArray();

        if($checkIfAlreadyCut->isEmpty()){
            $users = User::whereIn('id', $getUsers)->get();

            foreach ($users as $user) {
                $hiredDate = $user->users_detail->hired_date; //this should produce Y-m-d on string
                if($hiredDate){
                    $hiredDate = Carbon::createFromFormat('Y-m-d', $user->users_detail->hired_date);

                    // Add a year to the hired date
                    $oneYearAfterHired = $hiredDate->copy()->addYear();

                    if ($currentDate->greaterThanOrEqualTo($oneYearAfterHired)) {

                        $checkQuota = Emp_leave_quota::where('user_id', $user->id)
                        ->whereIn('leave_id', [10, 20])
                        ->where('expiration', '>=', date('Y-m-d'))
                        ->orderBy('expiration', 'asc')
                        ->get();

                        $countQuota = $totalHolidays; // Initialize the count

                        foreach ($checkQuota as $key => $quota) {
                            if ($countQuota > 0) {
                                $deductedQuota = min($countQuota, $quota->quota_left);
                                $countQuota -= $deductedQuota;

                                $quota->quota_used += $deductedQuota;
                                $quota->quota_left -= $deductedQuota;
                                $quota->save();

                                // Update the count for the next quota if it's negative
                                $countQuota = max(0, $countQuota);

                                $history = new Leave_request_history;
                                $history->req_date = date('Y-m-d');
                                $history->req_by = $user->id;
                                $history->quota_used = $deductedQuota;
                                $history->quota_left = $quota->quota_left;
                                $history->description = "Joint Holidays : " . implode(', ', $holidaysName);
                                $history->leave_id = $quota->leave_id;
                                $history->emp_leave_quota_id = $quota->id;
                                $history->leave_request_id = $monthPeriod;
                                $history->requested_days = $totalHolidays;
                                $history->save();

                                // Check if this is the last quota and countQuota is still not zero
                                if ($key === count($checkQuota) - 1 && $countQuota > 0) {
                                    $quota->quota_left -= $countQuota;
                                    $quota->save();
                                }
                            }
                        }

                        $negativeQuotas = Emp_leave_quota::where('user_id', $user->id)
                            ->whereIn('leave_id', [10, 20])
                            ->where('quota_left', '<', 0)
                            ->get();

                        foreach ($negativeQuotas as $negativeQuota) {
                            $entry = new Notification_alert;
                            $entry->user_id = $user->id;
                            $entry->message = "Your Leave Balance has gone negative due to Joint Holidays";
                            $entry->importance = 404;
                            $entry->save();
                        }
                    }
                }
            }
        }
    }
}
