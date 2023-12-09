<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendDataAttendance;

class PostAttendanceData extends Command
{
    protected $signature = 'attendance:post';

    protected $description = 'Dispatches the SendDataAttendance job';

    public function handle()
    {
        dispatch(new SendDataAttendance());

        $this->info('Attendance data posting job dispatched!');
    }
}
