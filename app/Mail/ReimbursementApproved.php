<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementApproved extends Mailable
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
                'link' => 'https://timereport.perdana.co.id/reimbursement/history/'
            ];
    
            $subject = 'Reimbursement Request Status';
    
            return $this->markdown('mailer.reimburse_approved', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Reimbursement Request Status';
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
                'link' => 'https://timereport.perdana.co.id/reimbursement/history/'
            ];
        }
    }
    