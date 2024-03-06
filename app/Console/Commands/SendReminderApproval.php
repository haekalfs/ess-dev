<?php

namespace App\Console\Commands;

use App\Jobs\ReminderApprovalDirectorReimbursement;
use App\Jobs\ReminderApprovalReimbursement;
use App\Jobs\SendReimbursementReminderFinance;
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

class SendReminderApproval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reimburse:send-reminder-approval';

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
        $isManager = Timesheet_approver::whereNotIn('group_id', [1])
            ->pluck('approver')->toArray();

        // Retrieve user IDs to approve reimbursement
        $userToApprove = Reimbursement_approval::where('status', 20)->whereIn('RequestTo', $isManager)
            ->groupBy('RequestTo')
            ->pluck('RequestTo');

        // Retrieve users based on the IDs
        $employees = User::whereIn('id', $userToApprove)->get();

        foreach ($employees as $employee) {
            // Count the number of forms pending approval for each employee
            $checkForms = Reimbursement_approval::where('status', 20)
                ->where('RequestTo', $employee->id)
                ->groupBy('reimbursement_id')
                ->count();

            // Dispatch a reminder if there are pending forms for approval
            if ($checkForms > 0) {
                dispatch(new ReminderApprovalReimbursement($employee, $checkForms));
            }
        }

        $isDirector = Timesheet_approver::whereIn('group_id', [1])
            ->pluck('approver')->toArray();

        //getApproved from manager
        $Check = DB::table('reimbursement_approval')
            ->select('reimb_item_id')
            ->whereNotIn('RequestTo', $isDirector)
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = 30 THEN 1 ELSE 0 END)')
            ->groupBy('reimb_item_id')
            ->pluck('reimb_item_id')
            ->toArray();
        // Retrieve user IDs to approve reimbursement
        $isUnapproved = Reimbursement_approval::where('status', 20)->whereIn('RequestTo', $isDirector)
            ->whereIn('reimb_item_id', $Check)
            ->groupBy('RequestTo')
            ->pluck('RequestTo');

        // Retrieve users based on the IDs
        $directors = User::whereIn('id', $isUnapproved)->get();

        foreach ($directors as $dir) {
            // Count the number of forms pending approval for each employee
            $checkFormsDir = Reimbursement_approval::where('status', 20)
                ->where('RequestTo', $dir->id)
                ->groupBy('reimbursement_id')
                ->count();

            // Dispatch a reminder if there are pending forms for approval
            if ($checkFormsDir > 0) {
                dispatch(new ReminderApprovalDirectorReimbursement($dir, $checkFormsDir));
            }
        }
    }

    public function schedule(Schedule $schedule)
    {
        //
    }
}
