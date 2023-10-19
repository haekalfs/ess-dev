<?php

namespace App\Jobs;

use App\Mail\ApprovalTimesheet;
use App\Models\Timesheet_detail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTimesheetApprovalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $monthPeriod = Timesheet_detail::select('id', 'month_periode')
        ->where('ts_status_id', 20)
        ->where('RequestTo', $this->user->id)
        ->groupBy('id', 'month_periode')
        ->get();

        $notification = new ApprovalTimesheet($this->user, $monthPeriod);
        Mail::send('mailer.timesheetapproval', $notification->data(), function ($message) use ($notification) {
            $message->to($notification->emailTo())
                    ->subject($notification->emailSubject());
        });
    }
}
