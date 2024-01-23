<?php

namespace App\Console\Commands;

use App\Models\Notification_alert;
use App\Models\Timesheet_approver;
use App\Models\Timesheet_detail;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyIconTimesheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:icon-for-notify-timesheet';

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
        // $userIds = Timesheet_approver::whereIn('id', [10,20,25,29,50])->groupBy('approver')->get();
        $userIds = User::all();

        foreach($userIds as $ids)
        {
            try {
                $getRows = Timesheet_detail::where('RequestTo', $ids->id)
                    ->whereYear('created_at', date('Y'))
                    ->get();

                if($getRows){
                    foreach($getRows as $rows)
                    {
                        switch ($rows->ts_status_id) {
                            case 20:
                                //notification Month untuk Review
                                $entry = new Notification_alert;
                                $entry->user_id = $ids->id;
                                $entry->message = "Emp's Timesheet Pending!";
                                $entry->importance = 1;
                                $entry->month_periode = $rows->month_periode;
                                $entry->type = "2";
                                $entry->save();
                                break;
                            default:
                            //none
                                break;
                        }
                    }
                }
                // Your code to handle $getRows goes here
            } catch (\Exception $e) {
                //do nothing
            }
        }
    }
}
