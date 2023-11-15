<?php

namespace App\Jobs;

use App\Mail\UserCreation;
// use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyUserCreation implements ShouldQueue
{
    protected $emailUser;
    protected $userName;
    protected $attachmentPath;

    public function __construct(string $emailUser, string $userName, $attachmentPath = null)
    {
        $this->emailUser = $emailUser;
        $this->userName = $userName;
        $this->attachmentPath = $attachmentPath ?: public_path('User Manual ESS Perdana Consulting 2023.pdf');
    }

    public function handle()
    {
        $notification = new UserCreation($this->emailUser, $this->userName, $this->attachmentPath);

        
            Mail::send('mailer.user_creation', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                    ->subject($notification->emailSubject())
                    ->attach($this->attachmentPath); // Melampirkan berkas
            });
    
    }
}
