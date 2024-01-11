<?php

namespace App\Console\Commands;

use App\Jobs\SendTimesheetReminderJob;
use App\Models\Users_detail;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
    protected $description = 'Send timesheet entry reminders to employees';

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
        // Retrieve user IDs to be reminded
        $userIds = Users_detail::groupBy('user_id')->pluck('user_id')->toArray();

        // Get the current date
        $currentDate = Carbon::now();

        // Calculate the previous month
        $previousMonth = $currentDate->subMonth();

        // Obtain the previous month and year separately
        $year = $previousMonth->year;
        $month = $previousMonth->month;

        // If you need the month in a specific format (e.g., with leading zeros), you can use the format method
        $monthFormatted = $previousMonth->format('m');

        // Dispatch a job for each user to be reminded
        foreach ($userIds as $userId) {
            dispatch(new SendTimesheetReminderJob($userId, $year, $monthFormatted));
        }

        $this->info('Timesheet entry reminders dispatched successfully.');

        return 0;
    }
}
