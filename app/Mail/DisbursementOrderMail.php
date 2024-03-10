<?php

namespace App\Mail;

use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Reimbursement_item;
use App\Models\Timesheet_approver;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisbursementOrderMail extends Mailable
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
        $subject = 'Disbursement Order: ' . $this->formCreator->f_type;
        $link = 'https://timereport.perdana.co.id/reimbursement/manage/';

        // Attach the generated document
        $f_id = $this->formCreator->f_id.'_'.$this->formCreator->user->name;
        $documentPath = public_path("reimbursement/Result_$f_id.docx");
        $this->attach($documentPath, [
            'as' => "Disbursement_Order_Letter_$f_id.docx"
        ]);

        return $this->markdown('mailer.disbursement_order')
            ->subject($subject)
            ->to($this->employee->email)
            ->cc('dian@perdana.co.id')
            ->with([
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'formCreator' => $this->formCreator,
                'link' => $link
            ]);
    }
}
