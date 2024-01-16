<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchAndFormatHolidaysJob;

class FetchAndFormatHolidays extends Command
{
    protected $signature = 'timesheet:fetch-holidays';
    protected $description = 'Fetch and format holidays data';

    public function handle()
    {
        try {
            // Dispatch the job to fetch and format holidays
            dispatch(new FetchAndFormatHolidaysJob());

            $this->info('Fetch and format holidays job dispatched successfully.');

        } catch (\Exception $e) {
            // Handle exception or log error
            $this->error('Error dispatching FetchAndFormatHolidaysJob: ' . $e->getMessage());
        }
    }
}
