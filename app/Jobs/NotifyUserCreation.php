<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\DisbursementOrderMail;
use App\Mail\ReimbursementPaid;
use App\Mail\ReimbursementPartiallyApproved;
use App\Mail\ReimbursementPriorApproval;
use App\Mail\UserCreation;
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

class NotifyUserCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;

    public function __construct(User $employee)
    {
        $this->employee = $employee;
    }

    public function handle()
    {
        $notification = new UserCreation($this->employee);

        Mail::send($notification);
    }
}
