<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmploymentStatusNotification;
use App\Models\Timesheet_approver;
use App\Models\Users_detail;
use Carbon\Carbon;

class CheckEmploymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Fetch users with their employment details
        $status = ['Contract', 'Probation', 'MT'];
        $getData = Users_detail::whereNotNull('hired_date')->where('status_active', 'Active')->pluck('user_id')->toArray();
        // Fetch users with details
        $users = User::with('users_detail')
        ->whereIn('id', $getData)
        ->get();

        $Hr = Timesheet_approver::find(10);
        $HrName = $Hr->user->name;

        foreach ($users as $user) {
            // Add logic to check employment status and send notifications
            $status = $user->users_detail->employee_status;

            switch ($status) {
                case 'Contract':
                    $this->checkContractStatus($user, $HrName);
                    break;

                case 'Probation':
                    $this->checkProbationStatus($user, $HrName);
                    break;

                case 'MT':
                    $this->checkMTStatus($user, $HrName);
                    break;

                case 'Freelance':
                    //
                    break;

                default:
                    // Handle other cases or log an error
                    break;
            }
        }
    }

    protected function checkContractStatus($user, $HrName)
    {
        $endDate = Carbon::parse($user->users_detail->resignation_date);
        $hiredDate = Carbon::parse($user->users_detail->hired_date);

        // Customize this logic based on your requirements
        $notificationDate = now()->subDays(30); // 30 days before contract end
        $recipients = ['sundari@perdana.co.id', 'suryadi@perdana.co.id'];

        if ($endDate->isAfter($notificationDate))
        {
            $notification = new EmploymentStatusNotification($user, 'Contract', $hiredDate, $HrName);
            Mail::to('hrd@perdana.co.id')->cc($recipients)->send($notification);
        }
    }

    protected function checkProbationStatus($user, $HrName)
    {
        $hiredDate = Carbon::parse($user->users_detail->hired_date);

        // Customize this logic based on your requirements
        $notificationDate = $hiredDate->addMonths(2); // 2 months after the hired date
        $recipients = ['sundari@perdana.co.id', 'suryadi@perdana.co.id'];

        if (now()->isAfter($notificationDate))
        {
            $notification = new EmploymentStatusNotification($user, 'Probation', $hiredDate, $HrName);
            Mail::to('hrd@perdana.co.id')->cc($recipients)->send($notification);
        }
    }

    protected function checkMTStatus($user, $HrName)
    {
        $hiredDate = Carbon::parse($user->users_detail->hired_date);

        // Customize this logic based on your requirements
        $firstNotificationDate = $hiredDate->addMonths(2); // 2 months after the hired date
        $secondNotificationDate = $hiredDate->addMonths(35); // 35 months after the hired date
        $recipients = ['sundari@perdana.co.id', 'suryadi@perdana.co.id'];

        // Check for the first notification
        if (now()->isAfter($firstNotificationDate))
        {
            $notification = new EmploymentStatusNotification($user, 'Probation of Management Trainee Program', $hiredDate, $HrName);
            Mail::to('hrd@perdana.co.id')->cc($recipients)->send($notification);
        }

        // Check for the second notification
        if (now()->isAfter($secondNotificationDate))
        {
            $notification = new EmploymentStatusNotification($user, 'Management Trainee', $hiredDate, $HrName);
            Mail::to('hrd@perdana.co.id')->cc($recipients)->send($notification);
        }
    }
}
