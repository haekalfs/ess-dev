<?php

namespace App\Jobs;

use App\Mail\MedicalToFinance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyMedicalToFinance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $userName;
    protected $MedId;
    protected $emailFinance;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $userName
     * @return void
     */
    public function __construct(User $employee, string $MedId, string $emailFinance, string $userName)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->MedId = $MedId;
        $this->emailFinance = $emailFinance;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new MedicalToFinance($this->employee, $this->MedId, $this->emailFinance, $this->userName);
        Mail::send('mailer.medical_approved_finance', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                ->subject($notification->emailSubject());
        });
    }
}
