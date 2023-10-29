<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\ReimbursementRejected;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyReimbursementRejected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $reimbId;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $reimbId
     * @return void
     */
    public function __construct(User $employee, string $reimbId)
    {
        $this->employee = $employee;
        $this->reimbId = $reimbId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new ReimbursementRejected($this->employee, $this->reimbId);
        Mail::send('mailer.reimburse_rejected', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}