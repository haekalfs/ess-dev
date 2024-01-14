<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutLeaveQuotaEmp extends Mailable
{
    protected $employee;
    protected $totalHolidays;

    public function __construct(User $employee, $totalHolidays)
    {
        $this->employee = $employee;
        $this->totalHolidays = $totalHolidays;
    }

    public function build()
    {
        $data = [
            'name' => $this->employee->name,
            'totalHolidays' => $this->totalHolidays,
            'link' => 'https://timereport.perdana.co.id/'
        ];

        $subject = 'Leave Quota Information';

        return $this->markdown('mailer.leave_quota_deducted', $data)
            ->subject($subject)
            ->to($this->employee->email);
    }

    public function emailSubject()
    {
        return 'Leave Quota Information';
    }

    public function emailTo()
    {
        return $this->employee->email;
    }

    public function data()
    {
        return [
            'name' => $this->employee->name,
            'totalHolidays' => $this->totalHolidays,
            'link' => 'https://timereport.perdana.co.id/medical/history/'
        ];
    }
}
