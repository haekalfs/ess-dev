<?php

namespace App\Console\Commands;

use App\Mail\ApprovalTimesheet;
use App\Mail\TimesheetReminderAllEmp;
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
        $previousMonth = date('m', strtotime('-1 month'));
        
        foreach ($usersToRemind as $employee) {
            try {
                $year = date('Y');
                $month = date('m');
                $previousMonth = date('m', strtotime('-1 month'));
        
                $notification = new TimesheetReminderAllEmp($employee, $year, $previousMonth);
        
                Mail::send('mailer.timesheet_entry', $notification->data(), function ($message) use ($notification) {
                    $message->to($notification->emailTo())
                            ->subject($notification->emailSubject());
                });
            } catch (\Exception $e) {
                // Handle the error, e.g., log the error message
                \Log::error('Error sending email: ' . $e->getMessage());
            }
        }
    }

    // public function schedule(Schedule $schedule)
    // {
    //     $schedule->command('timesheet:send-reminder')
    //         ->twiceMonthly(5, 7)
    //         ->daily();
    // }
}
