<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CutLeaveQuotaEmp extends Mailable
{
    protected $employee;
    protected $totalHolidays;

    public function __construct(User $employee, int $totalHolidays)
    {
        $this->employee = $employee;
        $this->totalHolidays = $totalHolidays;
    }

    public function build()
    {
        $subject = 'Your Leave Quota will be deducted : '. $this->totalHolidays . 'day(s)';
        $link = 'https://timereport.perdana.co.id/leave/history';

        return $this->markdown('mailer.leave_quota_deducted')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'totalHolidays' => $this->totalHolidays,
                        'link' => $link
                    ]);
    }
}
