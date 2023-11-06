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

    public function __construct(User $employee, $MedId)
    {
        $this->employee = $employee;
        $this->MedId = $MedId;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'MedId' => $this->MedId,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];

        $subject = 'Medical Request Status';

        return $this->markdown('mailer.medical_rejected', $data)
            ->subject($subject)
            ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return 'Medical Request Status';
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
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];
    }
}
