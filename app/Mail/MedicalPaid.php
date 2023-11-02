<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MedicalPaid extends Mailable
{
    protected $employee;
    protected $userName;
    protected $MedId;

    public function __construct(User $employee, $userName, $MedId)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->MedId = $MedId;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'userName' => $this->userName,
            'MedId' => $this->MedId,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];

        $subject = 'Medical Reimbursement Request Status';

        return $this->markdown('mailer.medical_paid', $data)
            ->subject($subject)
            ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return 'Medical Reimbursement Request Status';
    }

    public function emailTo()
    {
        return $this->employee->email;
    }

    public function data()
    {
        return [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'userName' => $this->userName,
            'MedId' => $this->MedId,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];
    }
}
