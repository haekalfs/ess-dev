<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MedicalRejected extends Mailable
{
    protected $employee;
    protected $MedId;
    protected $approverName;

    public function __construct(User $employee, $MedId, $approverName)
    {
        $this->employee = $employee;
        $this->MedId = $MedId;
        $this->approverName = $approverName;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'MedId' => $this->MedId,
            'approverName'=> $this->approverName,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];

        $subject = 'Medical Request Rejected';

        return $this->markdown('mailer.medical_rejected', $data)
            ->subject($subject)
            ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return 'Medical Request Rejected';
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
            'MedId' => $this->MedId,
            'approverName' => $this->approverName,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];
    }
}
