<?php

namespace App\Jobs;

use App\Mail\TimesheetReminderAllEmp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTimesheetReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $year;
    protected $previousMonth;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param int $year
     * @param int $previousMonth
     * @return void
     */
    public function __construct($userId, $year, $previousMonth)
    {
        $this->userId = $userId;
        $this->year = $year;
        $this->previousMonth = $previousMonth;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Retrieve the user
        $employee = User::find($this->userId);

        // Create the notification with the employee, year, and previous month
        $notification = new TimesheetReminderAllEmp($employee, $this->year, $this->previousMonth);

        // Send the email
        try {
            Mail::send('mailer.timesheet_entry', $notification->data(), function ($message) use ($notification) {
                $message->to($notification->emailTo())
                        ->subject($notification->emailSubject());
            });
        } catch (\Exception $e) {
            // Handle the error, e.g., log the error message
            \Log::error('Error sending email: ' . $e->getMessage());
        }
    }
}
