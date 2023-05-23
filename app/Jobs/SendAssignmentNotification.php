<?php

namespace App\Jobs;

use App\Mail\AssignmentNotifyToUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAssignmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $assignmentName;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $assignmentName
     * @return void
     */
    public function __construct(User $employee, string $assignmentName)
    {
        $this->employee = $employee;
        $this->assignmentName = $assignmentName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new AssignmentNotifyToUser($this->employee, $this->assignmentName);
        Mail::send('mailer.notify_user_assignment', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}
