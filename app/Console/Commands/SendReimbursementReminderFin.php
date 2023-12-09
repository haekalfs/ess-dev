<?php

namespace App\Console\Commands;

use App\Jobs\SendReimbursementReminderFinance;
use App\Models\Reimbursement_approval;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;

class SendReimbursementReminderFin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reimburse:send-reminder-finance';

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
        // $users = User::where('id', 'haekals')->get();
        $userToApprove = [];
        $data = Users_detail::whereIn('position_id', [21,22,23])->pluck('user_id')->toArray();

        $userToApprove = $data;
        $users = User::whereIn('id', $userToApprove)->get();

        foreach ($users as $user) {
            dispatch(new SendReimbursementReminderFinance($user));
        }
    }

    public function schedule(Schedule $schedule)
    {
        //
    }
}
