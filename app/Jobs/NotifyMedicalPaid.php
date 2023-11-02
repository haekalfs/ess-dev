<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\MedicalPaid;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyMedicalPaid implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $userName;
    protected $MedId;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $userName
     * @return void
     */
    public function __construct(User $employee, string $userName, string $MedId,)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->MedId = $MedId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new MedicalPaid($this->employee, $this->userName, $this->MedId);
        Mail::send('mailer.medical_paid', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                ->subject($notification->emailSubject());
        });
    }
}
