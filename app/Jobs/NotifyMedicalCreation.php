<?php

namespace App\Jobs;

use App\Mail\MedicalCreation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyMedicalCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $userName;
    protected $medical_id;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $userName
     * @return void
     */
    public function __construct(User $employee, string $userName, string $medical_id)
    {
        $this->employee = $employee;
        $this->userName = $userName;
        $this->medical_id = $medical_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new MedicalCreation($this->employee, $this->userName, $this->medical_id);
        Mail::send('mailer.medical_approval', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                ->subject($notification->emailSubject());
        });
    }
}
