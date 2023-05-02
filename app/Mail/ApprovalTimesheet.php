<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalTimesheet extends Mailable
    {
        protected $employee;
    
        public function __construct(User $employee)
        {
            $this->employee = $employee;
        }
    
        public function build()
        {
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'link' => 'https://timereport.perdana.co.id/approval/'
            ];
    
            $subject = 'Timesheet Approval Reminder';
    
            return $this->markdown('mailer.timesheetapproval', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Timesheet Approval Reminder';
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
                'link' => 'https://timereport.perdana.co.id/approval/'
            ];
        }
    }
    