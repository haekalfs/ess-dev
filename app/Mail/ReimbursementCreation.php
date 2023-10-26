<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementCreation extends Mailable
    {
        protected $employee;
        protected $userName;
    
        public function __construct(User $employee, $userName)
        {
            $this->employee = $employee;
            $this->userName = $userName;
        }
    
        public function build()
        {
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'userName' => $this->userName,
                'link' => 'https://timereport.perdana.co.id/approval/reimburse/'
            ];
    
            $subject = 'Reimbursement Approval Reminder';
    
            return $this->markdown('mailer.reimburse_approval', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Reimbursement Approval Reminder';
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
                'link' => 'https://timereport.perdana.co.id/approval/reimbursement/'
            ];
        }
    }
    