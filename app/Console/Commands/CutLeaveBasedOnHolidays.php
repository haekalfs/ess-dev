<?php

namespace App\Console\Commands;

use App\Jobs\CutLeaveBasedOnHolidaysJob;
use App\Jobs\NotifyDeductedLeaveQuota;
use App\Models\Emp_leave_quota;
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
    protected $signature = 'leave:cut-leaves-based-on-holidays';

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
            return redirect(url()->previous());
        } else {
            $array = $cachedData;
            // Use the cached data
        }
        $year = date('Y'); // Specify the year you want to retrieve data for
        $holidayKeyword = "Cuti Bersama"; // Specify the keyword you want to filter

        $totalHolidays = 0;

        foreach ($array as $date => $holiday) {
            if (substr($date, 0, 4) === $year && isset($holiday['summary'][0]) && strpos($holiday['summary'][0], $holidayKeyword) !== false) {
                $totalHolidays++;
            }
        }

        dispatch(new CutLeaveBasedOnHolidaysJob());
        $getUsers = Emp_leave_quota::pluck('user_id')->toArray();
        $users = User::whereIn('id', $getUsers)->get();

        foreach ($users as $user) {
            dispatch(new NotifyDeductedLeaveQuota($user, $totalHolidays));
        }
        $this->info('Cut Leave data processing job dispatched!');
    }
}
