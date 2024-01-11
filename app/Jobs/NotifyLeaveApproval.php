<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyLeaveApproval implements ShouldQueue
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
        $notification = new ApprovalLeave($this->employee, $this->userName);
        $getId = Setting::find(3);
        $ccTo = User::find($getId->user_id);

        Mail::send('mailer.approval_leave', $notification->data(), function ($message) use ($notification, $ccTo) {
            $message->to($notification->emailTo())
                    ->cc($ccTo->email) // Add CC recipient here
                    ->subject($notification->emailSubject());
        });
    }
}
