<?php

namespace App\Jobs;

use App\Mail\RejectedTimesheet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifRejectedTimesheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $year;
    protected $month;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  int  $year
     * @param  int  $month
     * @return void
     */
    public function __construct(User $employee, int $year, int $month)
    {
        $this->employee = $employee;
        $this->year = $year;
        $this->month = $month;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new RejectedTimesheet($this->employee, $this->year, $this->month);
        Mail::send('mailer.rejected_timesheet', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}