<?php
namespace App\Jobs;

use App\Models\Checkinout;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SendDataAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $usersData = Checkinout::select('user_id', 'date', DB::raw('MIN(time) as earliest_time'), DB::raw('MAX(time) as latest_time'))
            ->groupBy('user_id', 'date')
            ->get();

        $maxAttempts = 5;
        $attempts = 0;
        $success = false;

        while (!$success && $attempts < $maxAttempts) {
            try {
                foreach ($usersData as $data) {
                    if ($data->fingerId->user_id && $data->date && ($data->earliest_time || $data->latest_time)) {
                        $earliestTime = $data->earliest_time ? Carbon::createFromFormat('H:i:s', $data->earliest_time) : null;
                        $latestTime = $data->latest_time ? Carbon::createFromFormat('H:i:s', $data->latest_time) : null;

                        // Validation for earliest time
                        if ($earliestTime && $earliestTime->lessThan(Carbon::createFromTime(7, 0, 0))) {
                            $earliestTime = Carbon::createFromTime(7, 0, 0); // Set it to 08:00 AM if it's earlier
                        }

                        // Validation for latest time
                        if ($latestTime && $latestTime->greaterThan(Carbon::createFromTime(18, 0, 0))) {
                            $latestTime = Carbon::createFromTime(18, 0, 0); // Set it to 18:00 PM if it's later
                        }
                        Timesheet::updateOrCreate(
                            [
                                'ts_id_date' => str_replace('-', '', $data->date),
                                'ts_user_id' => $data->fingerId->user_id,
                            ],
                            [
                                'ts_date' => $data->date,
                                'ts_task' => 'HO',
                                'ts_task_id' => 'HO',
                                'ts_location' => 'HO',
                                'ts_activity' => 'HO Activities',
                                'ts_from_time' => $earliestTime ? $earliestTime->format('H:i') : null,
                                'ts_to_time' => $latestTime ? $latestTime->format('H:i') : null,
                                'allowance' => 70000,
                                'incentive' => 0,
                                'ts_type' => 1,
                                'ts_status_id' => 10,
                                // Add more activity columns as needed
                            ]
                        );
                    }
                }
                // stop
                $success = true;
            } catch (\Exception $e) {
                // Handle exception or log error
                sleep(5); // Wait for 5 seconds before retrying
                $attempts++;
            }
        }
        // Delete all rows after all attempts
        if ($success) {
            Checkinout::truncate(); // This will delete all rows in the table
        }
    }
}

