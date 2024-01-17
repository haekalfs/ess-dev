<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckEmploymentStatus;

class DispatchEmploymentStatusCheck extends Command
{
    protected $signature = 'employment:check';
    protected $description = 'Dispatch the CheckEmploymentStatus job';

    public function handle()
    {
        $this->info('Dispatching CheckEmploymentStatus job...');
        dispatch(new CheckEmploymentStatus());
        $this->info('CheckEmploymentStatus job dispatched successfully!');
    }
}
