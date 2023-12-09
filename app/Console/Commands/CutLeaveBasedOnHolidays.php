<?php

namespace App\Console\Commands;

use App\Jobs\CutLeaveBasedOnHolidaysJob;
use Illuminate\Console\Command;


class CutLeaveBasedOnHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:cut-leaves-based-on-holidays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cut users\' annual leave based on total holidays in the year';

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
        dispatch(new CutLeaveBasedOnHolidaysJob());
        $this->info('Cut Leave data processing job dispatched!');
    }
}
