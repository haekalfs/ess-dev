<?php

namespace App\Mail;

use App\Models\Leave_request_approval;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestRejected extends Mailable
{
    protected $employee;
    protected $leaveRequest;

    public function __construct(User $employee, Leave_request_approval $leaveRequest)
    {
        $this->employee = $employee;
        $this->leaveRequest = $leaveRequest;
    }

    public function build()
    {
        $subject = 'Your Leave Request has been Rejected';
        $link = 'https://timereport.perdana.co.id/reimbursement/history/';

        return $this->markdown('mailer.leave_rejected')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'leaveRequest' => $this->leaveRequest,
                        'link' => $link
                    ]);
    }
}
