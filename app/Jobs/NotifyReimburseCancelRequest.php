<?php

namespace App\Jobs;

use App\Mail\ReimbursementCancelRequest;
use App\Mail\ReimbursementCreation;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyReimburseCancelRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $formCreator;

    public function __construct(User $employee, String $formCreator)
    {
        $this->employee = $employee;
        $this->formCreator = $formCreator;
    }

    public function handle()
    {
        $notification = new ReimbursementCancelRequest($this->employee, $this->formCreator);

        Mail::send($notification);
    }
}
