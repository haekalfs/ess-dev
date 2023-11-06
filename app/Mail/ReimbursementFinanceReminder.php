<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementFinanceReminder extends Mailable
    {
        protected $employee;
    
        public function __construct(User $employee, $reimbReq)
        {
            $this->employee = $employee;
            $this->reimbReq = $reimbReq;
        }
    
        public function build()
        {
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'link' => 'https://timereport.perdana.co.id/approval/timesheet/p',
                'reimbReq' => $this->reimbReq
            ];
    
            $subject = 'Employees Reimbursement Reminder';
    
            return $this->markdown('mailer.timesheetapproval', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Employees Reimbursement Reminder';
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
                'link' => 'https://timereport.perdana.co.id/approval/timesheet/p',
                'reimbReq' => $this->reimbReq,
            ];
        }
    }
    