<?php

namespace App\Mail;

use App\Models\Leave_request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyNewsEmployees extends Mailable
{
    protected $employee;
    protected $titleOfNews;

    public function __construct(User $employee, int $titleOfNews)
    {
        $this->employee = $employee;
        $this->titleOfNews = $titleOfNews;
    }

    public function build()
    {
        $subject = 'Breaking News : '. $this->titleOfNews;
        $link = 'https://timereport.perdana.co.id/home';

        return $this->markdown('mailer.news_information')
                    ->subject($subject)
                    ->to($this->employee->email)
                    ->with([
                        'name' => $this->employee->name,
                        'email' => $this->employee->email,
                        'titleOfNews' => $this->titleOfNews,
                        'link' => $link
                    ]);
    }
}
