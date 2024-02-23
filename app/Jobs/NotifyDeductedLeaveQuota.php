<?php

namespace App\Jobs;

use App\Mail\CutLeaveQuotaEmp;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyDeductedLeaveQuota implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $totalHolidays;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $userName
     * @return void
     */
    public function __construct(User $employee, int $totalHolidays)
    {
        $this->employee = $employee;
        $this->totalHolidays = $totalHolidays;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new CutLeaveQuotaEmp($this->employee, $this->totalHolidays);

        Mail::send($notification);
    }
}
