<?php

namespace App\Console\Commands;

use App\Models\Emp_leave_quota;
use App\Models\Notification_alert;
use App\Models\User;
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
    protected $description = 'Generate annual and 5 year terms leave';

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

        $nowDate = date('Y-m-d');
        $nextYear = date('Y') + 1;
        $next5Year = date('Y') + 5;
        $expiration = $nextYear . '-04-01';
        $FiveYearExpiration = $next5Year . '-04-01';

        foreach($users as $user){
            $checkUsersQuotaAnnual = Emp_leave_quota::where('leave_id', 10)->where('user_id', $user->id)->orderBy('active_periode', 'desc')->first();
            $checkUsersQuotaFiveYear = Emp_leave_quota::where('leave_id', 20)->where('user_id', $user->id)->orderBy('active_periode', 'desc')->first();

            if($checkUsersQuotaAnnual){
                
            } else{
                $empLeave = new Emp_leave_quota;
                $empLeave->user_id = $user->id;
                $empLeave->quota_used = 0;
                $empLeave->leave_id = 10;
                $empLeave->once_in_service_years = 0;
                $empLeave->active_periode = $nowDate;
                $empLeave->expiration = $expiration;
                $empLeave->quota_left = 12;
                $empLeave->save();
            }
            
            $hiredDate = new DateTime($user->users_detail->hired_date);
            $today = new DateTime(); // This will use the current date

            // Calculate the date that is 5 years from the hired date
            $fiveYearsFromNow = $hiredDate->modify('+5 years');

            if ($today >= $fiveYearsFromNow) {
                if($checkUsersQuotaFiveYear){
                    $activePeriode = $checkUsersQuotaFiveYear->periode_end;
                    $var1 = date('Y') + 5;
                    $var2 = $var1 . '-04-01';
                    $endPeriode = $var2;
                    $empLeave = new Emp_leave_quota;
                    $empLeave->user_id = $user->id;
                    $empLeave->quota_used = 0;
                    $empLeave->leave_id = 20;
                    $empLeave->once_in_service_years = 0;
                    $empLeave->active_periode = $activePeriode;
                    $empLeave->expiration = $endPeriode;
                    $empLeave->quota_left = 22;
                    $empLeave->save();
                } else{
                    $empLeave = new Emp_leave_quota;
                    $empLeave->user_id = $user->id;
                    $empLeave->quota_used = 0;
                    $empLeave->leave_id = 20;
                    $empLeave->once_in_service_years = 0;
                    $empLeave->active_periode = $nowDate;
                    $empLeave->expiration = $FiveYearExpiration;
                    $empLeave->quota_left = 22;
                    $empLeave->save();
                }
            }
        }
    }
}
