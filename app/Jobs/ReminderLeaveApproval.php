<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\ReimbursementFinanceReminder;
use App\Mail\ReminderApprovalLeaveRequest;
use App\Mail\ReminderApprovalReimburse;
use App\Models\Leave;
use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ReminderLeaveApproval implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $leaveRequest;

    public function __construct(User $employee, String $leaveRequest)
    {
        $this->employee = $employee;
        $this->leaveRequest = $leaveRequest;
    }

    public function handle()
    {
        $notification = new ReminderApprovalLeaveRequest($this->employee, $this->leaveRequest);

        Mail::send($notification);
    }
}
