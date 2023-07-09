<?php

namespace App\Jobs;

use App\Mail\ApprovalTimesheet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTimesheetApprovalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new ApprovalTimesheet($this->user);
        Mail::send('mailer.timesheetapproval', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}
