<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmploymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $hiredDate;
    public $HRD;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $status
     * @param  \Carbon\Carbon  $hiredDate
     * @return void
     */
    public function __construct($user, $status, $hiredDate, $HRD)
    {
        $this->user = $user;
        $this->status = $status;
        $this->hiredDate = $hiredDate;
        $this->HRD = $HRD;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Employment Status Notification')
            ->view('mailer.employment_status')
            ->with([
                'style' => $this->getInlineStyles(),
            ]);
    }

    /**
     * Get inline styles.
     *
     * @return string
     */
    protected function getInlineStyles()
    {
        // Add your inline styles here
        return <<<CSS
            body {
                color: #333;
                font-family: 'Arial', sans-serif;
            }

            a {
                color: grey;
            }

            a:hover {
                color: blue;
            }

            /* Add more styles as needed */
        CSS;
    }
}
