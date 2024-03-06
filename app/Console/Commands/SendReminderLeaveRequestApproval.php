<?php

namespace App\Console\Commands;

use App\Jobs\NotifyLeaveApproval;
use App\Jobs\ReminderApprovalReimbursement;
use App\Jobs\ReminderLeaveApproval;
use App\Jobs\SendReimbursementReminderFinance;
use App\Models\Leave_request_approval;
use App\Models\Reimbursement;
use App\Models\Reimbursement_approval;
use App\Models\Timesheet_approver;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendReminderLeaveRequestApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:send-reminder-approval';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isManager = Timesheet_approver::whereIn('id', [10, 15, 20, 50, 25])
            ->pluck('approver')->toArray();

        // Retrieve user IDs to approve reimbursement
        $userToApprove = Leave_request_approval::where('status', 15)->whereIn('RequestTo', $isManager)
            ->groupBy('RequestTo')
            ->pluck('RequestTo');

        // Retrieve users based on the IDs
        $employees = User::whereIn('id', $userToApprove)->get();

        foreach ($employees as $employee) {
            // Count the number of forms pending approval for each employee
            $checkForms = Leave_request_approval::where('status', 15)
                ->where('RequestTo', $employee->id)
                ->groupBy('leave_request_id')
                ->count();

            // Dispatch a reminder if there are pending forms for approval
            if ($checkForms > 0) {
                dispatch(new ReminderLeaveApproval($employee, $checkForms));
            }
        }

        $isDirector = Timesheet_approver::whereIn('group_id', [1])
            ->pluck('approver')->toArray();

        //Get Approved manager
        $Check = DB::table('leave_request_approval')
            ->select('leave_request_id')
            ->whereNotIn('RequestTo', $isDirector)
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 20 THEN 1 WHEN status = 404 THEN 0 ELSE 0 END)')
            ->groupBy('leave_request_id', 'RequestTo')
            ->pluck('leave_request_id')
            ->toArray();
        // Retrieve user IDs to approve reimbursement
        $isUnapproved = Leave_request_approval::where('status', 15)->whereIn('RequestTo', $isDirector)
            ->whereIn('leave_request_id', $Check)
            ->groupBy('RequestTo')
            ->pluck('RequestTo');

        // Retrieve users based on the IDs
        $directors = User::whereIn('id', $isUnapproved)->get();

        foreach ($directors as $dir) {
            // Count the number of forms pending approval for each employee
            $checkFormsDir = Leave_request_approval::where('status', 15)
                ->where('RequestTo', $dir->id)
                ->groupBy('leave_request_id')
                ->count();

            // Dispatch a reminder if there are pending forms for approval
            if ($checkFormsDir > 0) {
                dispatch(new ReminderLeaveApproval($dir, $checkFormsDir));
            }
        }
    }

    public function schedule(Schedule $schedule)
    {
        //
    }
}
