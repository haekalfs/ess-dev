<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreation extends Mailable
{
    use Queueable, SerializesModels;
    protected $emailUser;
    protected $userName;

    public function __construct($emailUser, $userName, $path)
    {
        $this->emailUser = $emailUser;
        $this->userName = $userName;
        
    }

    public function build()
    {
        $data = [
            'name' => $this->userName,
            'email' => $this->emailUser,
            'userName' => $this->userName,
            'emailUser' => $this->emailUser,
            'link' => 'https://timereport.perdana.co.id/'
        ];

        $subject = 'ESS Acount';

        return $this->markdown('mailer.user_creation', $data)
            ->subject($subject)
            ->to($this->emailUser)
            ->attach(public_path('User Manual ESS Perdana Consulting 2023.pdf'), [
            'as' => 'User Manual ESS Perdana Consulting 2023.pdf',
            'mime' => 'application/pdf',
        ]);
    }

    public function emailSubject()
    {
        return 'ESS Account';
    }

    public function emailTo()
    {
        return $this->emailUser;
    }

    public function data()
    {
        return [
            'name' => $this->userName,
            'email' => $this->emailUser,
            'userName' => $this->userName,
            'emailUser' => $this->emailUser,
            'link' => 'https://timereport.perdana.co.id/'
        ];
    }
}
