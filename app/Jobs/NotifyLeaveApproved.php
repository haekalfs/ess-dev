<?php

namespace App\Jobs;

use App\Mail\ApprovedLeave;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyLeaveApproved implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $userName;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $userName
     * @return void
     */
    public function __construct(User $employee, string $userName)
    {
        $this->employee = $employee;
        $this->userName = $userName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new ApprovedLeave($this->employee, $this->userName);
        Mail::send('mailer.approved_leave', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}
