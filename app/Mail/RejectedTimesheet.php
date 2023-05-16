<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

    class RejectedTimesheet extends Mailable
        {
        protected $employee;
        protected $month;
        protected $year;
    
        public function __construct(User $employee, $year, $month)
        {
            $this->employee = $employee;
            $this->month = $month;
            $this->year = $year;
        }
    
        public function build()
        {
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'link' => 'https://timereport.perdana.co.id/timesheet/entry/',
                'month' => $this->month,
                'year' => $this->year
            ];
    
            $subject = 'Timesheet Rejected';
    
            return $this->markdown('mailer.rejected_timesheet', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Timesheet Rejected';
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
                'link' => 'https://timereport.perdana.co.id/timesheet',
                'month' => $this->month,
                'year' => $this->year
            ];
        }
    }
    