<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessAttendanceData;

class GetAttendanceData extends Command
{
    protected $signature = 'attendance:get';

    protected $description = 'Dispatches the ProcessAttendanceData job';

    public function handle()
    {
        dispatch(new ProcessAttendanceData());

        $this->info('Attendance data processing job dispatched!');
    }
}
