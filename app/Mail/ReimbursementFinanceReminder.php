<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementFinanceReminder extends Mailable
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
        $subject = 'New reimbursement requests awaiting to be process : '. $this->formCreator;
        $link = 'https://timereport.perdana.co.id/reimbursement/manage/';

        return $this->markdown('mailer.reimburse_approved_finance')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->cc('admin@perdana.co.id')
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'formCreator' => $this->formCreator,
                        'link' => $link
                    ]);
    }
}
