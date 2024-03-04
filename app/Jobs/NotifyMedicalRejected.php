<?php

namespace App\Jobs;

use App\Mail\ApprovalLeave;
use App\Mail\MedicalRejected;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyMedicalRejected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $MedId;
    protected $approverName;

    /**
     * Create a new job instance.
     *
     * @param  User  $employee
     * @param  string  $MedId
     * @return void
     */
    public function __construct(User $employee, string $MedId, string $approverName)
    {
        $this->employee = $employee;
        $this->MedId = $MedId;
        $this->approverName = $approverName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = new MedicalRejected($this->employee, $this->MedId, $this->approverName);
        Mail::send('mailer.medical_rejected', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                ->subject($notification->emailSubject());
        });
    }
}
