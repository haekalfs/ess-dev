<?php

namespace App\Console\Commands;

use App\Mail\ApprovalTimesheet;
use App\Models\User;
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
        $users = User::where('id', 'haekals')->get();

        foreach ($users as $user) {
            $notification = new ApprovalTimesheet($user);
            Mail::send('mailer.timesheetapproval', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                        ->subject($notification->emailSubject());
            });
        }
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command('timesheet:send-reminder')
            ->twiceMonthly(5, 7)
            ->daily();
    }
}
