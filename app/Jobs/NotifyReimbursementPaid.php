<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\ReimbursementPaid;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyReimbursementPaid implements ShouldQueue
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
        $notification = new ReimbursementPaid($this->employee, $this->userName);
        Mail::send('mailer.reimburse_paid', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}