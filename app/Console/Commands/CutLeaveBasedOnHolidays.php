<?php

namespace App\Console\Commands;

use App\Models\Emp_leave_quota;
use App\Models\Leave_request_history;
use App\Models\Notification_alert;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CutLeaveBasedOnHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:cut-leaves-based-on-holidays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cut users\' annual leave based on total holidays in the year';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set("Asia/Jakarta");
        $json = file_get_contents("https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json");
        $array = json_decode($json, true);

        $year = date('Y'); // Specify the year you want to retrieve data for
        $holidayKeyword = "Cuti Bersama"; // Specify the keyword you want to filter

        $totalHolidays = 0;

        foreach ($array as $date => $holiday) {
            if (substr($date, 0, 4) === $year && isset($holiday['deskripsi']) && strpos($holiday['deskripsi'], $holidayKeyword) !== false) {
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
                    $history->description = "Joint Holidays";
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
