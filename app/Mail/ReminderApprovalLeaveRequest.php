<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderApprovalLeaveRequest extends Mailable
{
    protected $employee;
    protected $leaveRequest;

    public function __construct(User $employee, String $leaveRequest)
    {
        $this->employee = $employee;
        $this->leaveRequest = $leaveRequest;
    }

    public function build()
    {
        $subject = 'Leave Requests awaiting for your Approval : '. $this->leaveRequest . 'Item(s)';
        $link = 'https://timereport.perdana.co.id/approval/leave/';

        return $this->markdown('mailer.leave_approval_reminder')
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
