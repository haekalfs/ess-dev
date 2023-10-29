<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReimbursementRejected extends Mailable
    {
        protected $employee;
        protected $reimbId;
    
        public function __construct(User $employee, $reimbId)
        {
            $this->employee = $employee;
            $this->reimbId = $reimbId;
        }
    
        public function build()
        {
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'reimbId' => $this->reimbId,
                'link' => 'https://timereport.perdana.co.id/reimbursement/view/'
            ];
    
            $subject = 'Reimbursement Request Status';
    
            return $this->markdown('mailer.reimburse_rejected', $data)
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
                'reimbId' => $this->reimbId,
                'link' => 'https://timereport.perdana.co.id/reimbursement/view/'
            ];
        }
    }
    