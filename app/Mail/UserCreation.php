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

class UserCreation extends Mailable
{
    protected $employee;

    public function __construct(User $employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        $subject = 'Welcome to Perdana Consulting, Explore the Power of ERP Solutions with Us!';
        $link = 'https://timereport.perdana.co.id/';

        // Attach the generated document
        $documentPath = public_path('User Manual ESS Perdana Consulting 2023.pdf');
        $this->attach($documentPath, [
            'as' => "User Manual ESS Perdana Consulting 2023.docx"
        ]);

        return $this->markdown('mailer.user_creation')
            ->subject($subject)
            ->to($this->employee->email)
            ->with([
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'employee' => $this->employee,
                'link' => $link
            ]);
    }
}
