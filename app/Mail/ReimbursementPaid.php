<?php

namespace App\Mail;

use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementPaid extends Mailable
{
    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, Reimbursement $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function build()
    {
        $subject = 'Your Reimbursement Request has been Paid : '. $this->formCreator->f_type;
        $link = 'https://timereport.perdana.co.id/reimbursement/history/';

        if($this->formCreator->ccTo){
            // Split comma-delimited string into an array of email addresses
            $ccEmails = explode(',', $this->formCreator->ccTo);

            // Format email addresses individually
            $formattedCcEmails = [];
            foreach ($ccEmails as $email) {
                $formattedCcEmails[] = ['email' => trim($email)];
            }
        } else {
            $formattedCcEmails = NULL;
        }

        return $this->markdown('mailer.reimburse_paid')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->cc($formattedCcEmails)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'formCreator' => $this->formCreator,
                        'link' => $link
                    ]);
    }
}
