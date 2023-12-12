<?php

namespace App\Console\Commands;

use App\Models\Emp_leave_quota;
use App\Models\Notification_alert;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateLeaveAnnually extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:generate-employees-leave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate annual and 5 year terms leave// running tiap 1 jan';

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
        $users = User::all();
        $endMonth = 3; // March

        foreach ($users as $user) {
            $hiredDate = $user->users_detail->hired_date; //this should produce Y-m-d on string
            if($hiredDate){
                $hiredDate = Carbon::createFromFormat('Y-m-d', $user->users_detail->hired_date);

                $checkUsersQuotaAnnual = Emp_leave_quota::where('leave_id', 10)
                    ->where('user_id', $user->id)
                    ->orderBy('active_periode', 'desc')
                    ->first();

                if ($checkUsersQuotaAnnual) {

                    if($checkUsersQuotaAnnual->active_periode == $user->users_detail->hired_date){ //if last picked quota active periode is the same date as hired date
                        $lastQuotaDate = $checkUsersQuotaAnnual->active_periode
                        ? Carbon::createFromFormat('Y-m-d', $checkUsersQuotaAnnual->active_periode)
                        : $hiredDate->copy()->subYear(); // Default to hired date if no previous quota

                        $startDate = $lastQuotaDate->copy()->addYear()->startOfDay();
                        $endDate = Carbon::create($startDate->year + 1, $endMonth, 31)->startOfDay();
                        $endDate->endOfMonth(); // Set it to the end of the month

                        // Now $startDate and $endDate contain only the date part (Y-m-d)
                        $startDate2 = $lastQuotaDate->copy()->addYear()->startOfDay();
                        $totalMonths = $startDate->diffInMonths($startDate2->endOfYear()) + 1;
                    } else {
                        $lastQuota = Carbon::createFromFormat('Y-m-d', $checkUsersQuotaAnnual->active_periode);
                        $lastQuotaYear = $lastQuota->year;
                        $startDate = Carbon::createFromDate($lastQuotaYear + 1, 1, 1);
                        $endDate = Carbon::createFromDate($lastQuotaYear + 2, $endMonth, 31);
                        $totalMonths = 12;
                    }

                    $lastQuotaExpiration = Carbon::createFromFormat('Y-m-d', $checkUsersQuotaAnnual->expiration); //validation for creating so it does not multiplies
                    if ($lastQuotaExpiration->isCurrentYear() || $lastQuotaExpiration->isNextYear()) {
                        $empLeave = new Emp_leave_quota;
                        $empLeave->user_id = $user->id;
                        $empLeave->quota_used = 0;
                        $empLeave->leave_id = 10;
                        $empLeave->once_in_service_years = 0;
                        $empLeave->active_periode = $startDate->format('Y-m-d');
                        $empLeave->expiration = $endDate->format('Y-m-d');
                        $empLeave->quota_left = $totalMonths;
                        $empLeave->save();
                    }
                }

                $checkUsersQuotaFiveYearTerm = Emp_leave_quota::where('leave_id', 20)
                    ->where('user_id', $user->id)
                    ->orderBy('active_periode', 'desc')
                    ->first();

                // Check if the hired date is exactly 5 years ago from now
                if ($hiredDate->diffInYears(Carbon::now()) >= 5) {
                    // Calculate start and end dates for the new leave quota
                    if ($checkUsersQuotaFiveYearTerm) {
                        $lastQuota5 = Carbon::createFromFormat('Y-m-d', $checkUsersQuotaFiveYearTerm->active_periode);
                        $lastQuota5Year = $lastQuota5->year;

                        $lastQuotaDate = Carbon::createFromDate($lastQuota5Year, 1, 1)->startOfDay();
                    } else {
                        $lastQuotaDate = $hiredDate->copy()->subYear()->addYear(1)->startOfDay()->month(1)->day(1);
                    }

                    $startDate = $lastQuotaDate->copy()->addYear(5)->startOfDay();
                    $endDate = Carbon::create($startDate->year + 2, $endMonth, 31)->startOfDay(); // End period
                    $endDate->endOfMonth(); // Set it to the end of the month

                    // $empLeave = new Emp_leave_quota;
                    // $empLeave->user_id = $user->id;
                    // $empLeave->quota_used = 0;
                    // $empLeave->leave_id = 20;
                    // $empLeave->once_in_service_years = 0;
                    // $empLeave->active_periode = $startDate->format('Y-m-d');
                    // $empLeave->expiration = $endDate->format('Y-m-d');
                    // $empLeave->quota_left = 22;
                    // $empLeave->save();
                }
            }
        }
    }
}
