<?php

namespace App\Mail;

use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelLeaveRequest extends Mailable
{
    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, String $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function build()
    {
        $subject = 'Leave Request has been Canceled';
        $link = 'https://timereport.perdana.co.id/approval/leave/';

        return $this->markdown('mailer.leave_cancel_request')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'userName' => $this->formCreator,
                        'link' => $link
                    ]);
    }
}
