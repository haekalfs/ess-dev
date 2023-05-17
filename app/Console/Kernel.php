<?php

namespace App\Console;

use App\Console\Commands\SendTimesheetApprovalReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\SendTimesheetApprovalReminder::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command(Commands\SendTimesheetApprovalReminder::class)->twiceMonthly(5, 7)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
