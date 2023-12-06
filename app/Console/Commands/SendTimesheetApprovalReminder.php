<?php

namespace App\Console\Commands;

use App\Jobs\SendTimesheetApprovalNotification;
use App\Mail\ApprovalTimesheet;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Usr_role;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;

class SendTimesheetApprovalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // $users = User::where('id', 'haekals')->get();
        $userToApprove = Timesheet_detail::where('ts_status_id', 20)->groupBy('RequestTo')->pluck('RequestTo')->toArray();
        $users = User::whereIn('id', $userToApprove)->get();

        foreach ($users as $user) {
            dispatch(new SendTimesheetApprovalNotification($user));
        }
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command('timesheet:send-reminder')
            ->twiceMonthly(1, 10)
            ->daily();
    }
}
