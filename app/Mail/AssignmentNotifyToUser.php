<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentNotifyToUser extends Mailable
{
    protected $employee;
    protected $assignment;

    public function __construct(User $employee, $assignment)
    {
        $this->employee = $employee;
        $this->assignment = $assignment;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'assignment' => $this->assignment,
            'link' => 'https://timereport.perdana.co.id/myprojects'
        ];

        $subject = "[Assignment] You have been assigned by Service Director";

        return $this->markdown('mailer.approval_assignment', $data)
                    ->subject($subject)
                    ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return "[Assignment] You have been assigned by Service Director";
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
            'assignment' => $this->assignment,
            'link' => 'https://timereport.perdana.co.id/myprojects'
        ];
    }
}
