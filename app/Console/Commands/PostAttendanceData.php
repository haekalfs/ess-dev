<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAttendanceData;
use Illuminate\Console\Command;
use App\Jobs\SendDataAttendance;
use App\Models\Checkinout;

class PostAttendanceData extends Command
{
    protected $signature = 'attendance:post';

    protected $description = 'Dispatches the SendDataAttendance job';

    public function handle()
    {
        $maxAttempts = 5;
        $attempts = 0;
        $success = false;

        while (!$success && $attempts < $maxAttempts) {
            try {
                dispatch(new ProcessAttendanceData());
                // If ProcessAttendanceData runs successfully without throwing exceptions,
                // it reaches here without encountering a catch block.
                dispatch(new SendDataAttendance());
                $this->info('Attendance data processing job dispatched!');
                $success = true; // Mark the job as successful
            } catch (\Exception $e) {
                // Handle exception or log error
                sleep(5); // Wait for 5 seconds before retrying
                $attempts++;
                // Handle any exceptions thrown during ProcessAttendanceData job execution
                $this->error('Error processing attendance data: ' . $e->getMessage());
            }
        }
    }
}
