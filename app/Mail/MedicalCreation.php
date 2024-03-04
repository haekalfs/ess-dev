<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MedicalCreation extends Mailable
{
    use Queueable, SerializesModels;
    protected $employee;
    protected $userName;
    protected $medical_id;

    public function __construct(User $employee, $userName, $medical_id)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->medical_id = $medical_id;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'userName' => $this->userName,
            'medical_id' => $this->medical_id,
            'link' => 'https://timereport.perdana.co.id/approval/medical/'
        ];

        $subject = 'Medical Reimburse Approval Reminder';

        return $this->markdown('mailer.medical_approval', $data)
            ->subject($subject)
            ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return 'Medical Reimburse Approval Reminder';
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
            'medical_id' => $this->medical_id,
            'link' => 'https://timereport.perdana.co.id/approval/medical/'
        ];
    }
}
