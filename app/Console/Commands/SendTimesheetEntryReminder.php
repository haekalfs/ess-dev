<?php

namespace App\Console\Commands;

use App\Mail\ApprovalTimesheet;
use App\Mail\TimesheetReminderAllEmployee;
use App\Mail\TimesheetReminderEmployee;
use App\Models\Timesheet_detail;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;

class SendTimesheetEntryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:send-reminder-employee';

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
        $users = Users_detail::groupBy('user_id')->pluck('user_id')->toArray();
        $usersToRemind = User::whereIn('id', $users)->get();

        $year = date('Y');
        $month = date('m');
        foreach ($usersToRemind as $employee) {
            $notification = new TimesheetReminderAllEmployee($employee, $year, $month);
            Mail::send('mailer.timesheet_entry', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                        ->subject($notification->emailSubject());
            });
        }
    }

    // public function schedule(Schedule $schedule)
    // {
    //     $schedule->command('timesheet:send-reminder')
    //         ->twiceMonthly(5, 7)
    //         ->daily();
    // }
}
