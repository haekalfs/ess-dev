<?php

namespace App\Jobs;

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
use Exception;
use Illuminate\Support\Facades\Cache;
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
        $holidayKeyword = "Cuti Bersama"; // Specify the keyword you want to filter

        $totalHolidays = 0;

        foreach ($array as $date => $holiday) {
            if (substr($date, 0, 4) === $year && isset($holiday['summary'][0]) && strpos($holiday['summary'][0], $holidayKeyword) !== false) {
                $totalHolidays++;
            }
        }

        $users = User::all();

        foreach ($users as $user) {
            $checkQuota = Emp_leave_quota::where('user_id', $user->id)
                ->whereIn('leave_id', [10, 20])
                ->where('expiration', '>=', date('Y-m-d'))
                ->orderBy('expiration', 'asc')
                ->get();

            if($totalHolidays > 8){ //set the maximum cut to only 8
                $totalHolidays = 8;
            }
            $countQuota = $totalHolidays; // Initialize the count

            foreach ($checkQuota as $quota) {
                if ($countQuota > 0) {
                    $remainingQuota = $quota->quota_left;
                    $deductedQuota = min($countQuota, $remainingQuota);
                    $countQuota -= $deductedQuota;

                    $quota->quota_used += $deductedQuota;
                    $quota->quota_left -= $deductedQuota;
                    $quota->save();

                    $history = New Leave_request_history;
                    $history->req_date = date('Y-m-d');
                    $history->req_by = $user->id;
                    $history->quota_used = $deductedQuota;
                    $history->quota_left = $quota->quota_left;
                    $history->description = "Joint Holidays [Deducted by System]";
                    $history->leave_id = $quota->leave_id;
                    $history->emp_leave_quota_id = $quota->id;
                    $history->requested_days = $totalHolidays;
                    $history->save();
                } else {
                    break; // Stop deducting if countQuota reaches zero
                }
                $entry = new Notification_alert;
                $entry->user_id = $user->id;
                $entry->message = "Your Leave Balance has been deducted due to Joint Holidays";
                $entry->importance = 404;
                $entry->save();
            }
        }
    }
}
