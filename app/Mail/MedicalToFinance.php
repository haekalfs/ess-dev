<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MedicalToFinance extends Mailable
{
    protected $employee;
    protected $MedId;
    protected $emailFinance;
    protected $userName;
    public function __construct(User $employee,  $MedId, $emailFinance, $userName)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->MedId = $MedId;
        $this->emailFinance = $emailFinance;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->emailFinance,
            'userName' => $this->emailFinance->name,
            'MedId' => $this->MedId,
            'link' => 'https://timereport.perdana.co.id/medical/review/'
        ];

        $subject = 'New Medical Request awaiting to be process';

        return $this->markdown('mailer.medical_review', $data)
            ->subject($subject)
            ->to($this->emailFinance);
    }

    public function emailSubject()
    {
        return 'New Medical Request awaiting to be process';
    }

    public function emailTo()
    {
        return $this->emailFinance;
    }

    public function data()
    {
        return [
            'name' => $this->employee->name,
            'email' => $this->emailFinance,
            'userName' => $this->emailFinance->name,
            'MedId' => $this->MedId,
            'link' => 'https://timereport.perdana.co.id/medical/review/'
        ];
    }
}
