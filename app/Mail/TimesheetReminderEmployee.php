<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

    class TimesheetReminderEmployee extends Mailable
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
            $encryptYear = Crypt::encrypt(intval($this->year));
            $encryptMonth = Crypt::encrypt($this->month);
            $data = [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'link' => "https://timereport.perdana.co.id/timesheet/entry/$encryptYear/$encryptMonth",
                'month' => $this->month,
                'year' => $this->year
            ];
    
            $subject = 'Timesheet Entry Reminder';
    
            return $this->markdown('mailer.timesheet_entry', $data)
                        ->subject($subject)
                        ->to($this->employee->email);
        }
    
        public function emailSubject()
        {
            return 'Timesheet Entry Reminder';
        }
    
        public function emailTo()
        {
            return $this->employee->email;
        }
        
        public function data()
        {
            $encryptYear = Crypt::encrypt(intval($this->year));
            $encryptMonth = Crypt::encrypt($this->month);
            return [
                'name' => $this->employee->name,
                'email' => $this->employee->email,
                'link' => "https://timereport.perdana.co.id/timesheet/entry/$encryptYear/$encryptMonth",
                'month' => $this->month,
                'year' => $this->year
            ];
        }
    }
    