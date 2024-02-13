<?php

namespace App\Mail;

use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementPriorApproval extends Mailable
{
    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, Reimbursement_approval $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function build()
    {
        $subject = 'New Status for Reimbursement Request : '. $this->formCreator->request->f_type;
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

        return $this->markdown('mailer.reimburse_prior_approved')
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
