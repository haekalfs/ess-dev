<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\LeaveRequestRejected;
use App\Mail\ReimbursementItemChangesbyFinance;
use App\Mail\ReimbursementPaid;
use App\Mail\ReimbursementPartiallyApproved;
use App\Mail\ReimbursementRejected;
use App\Models\Leave_request_approval;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyRejectedLeaveRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $leaveRequest;

    public function __construct(User $employee, Leave_request_approval $leaveRequest)
    {
        $this->employee = $employee;
        $this->leaveRequest = $leaveRequest;
    }

    public function handle()
    {
        $notification = new LeaveRequestRejected($this->employee, $this->leaveRequest);

        Mail::send($notification);
    }
}
