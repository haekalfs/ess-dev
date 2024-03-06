<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderDirectorApprovalReimburse extends Mailable
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
        $subject = 'Reimbursement Requests awaiting for your Final Approval : '. $this->formCreator . ' Item(s)';
        $link = 'https://timereport.perdana.co.id/approval/reimburse/';

        return $this->markdown('mailer.reimburse_approved_finance')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'formCreator' => $this->formCreator,
                        'link' => $link
                    ]);
    }
}
