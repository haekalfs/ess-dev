<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalLeave extends Mailable
{
    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, Leave_request $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function build()
    {
        $subject = 'Leave Approval Reminder : '. $this->formCreator->user->name;
        $link = 'https://timereport.perdana.co.id/approval/leave';

        return $this->markdown('mailer.approval_leave')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->cc('hrd@perdana.co.id')
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'formCreator' => $this->formCreator,
                        'link' => $link
                    ]);
    }
}
