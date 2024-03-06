<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\ReimbursementFinanceReminder;
use App\Mail\ReminderApprovalReimburse;
use App\Mail\ReminderDirectorApprovalReimburse;
use App\Models\Leave;
use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ReminderApprovalDirectorReimbursement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, String $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function handle()
    {
        $notification = new ReminderDirectorApprovalReimburse($this->employee, $this->formCreator);

        Mail::send($notification);
    }
}
