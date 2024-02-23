<?php

namespace App\Jobs;

use App\Mail\CancelLeaveRequest;
use App\Mail\NotifyNewsEmployees;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class BlastNewsEmployees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $titleOfNews;

    public function __construct(User $employee, String $titleOfNews)
    {
        $this->employee = $employee;
        $this->titleOfNews = $titleOfNews;
    }

    public function handle()
    {
        $notification = new NotifyNewsEmployees($this->employee, $this->titleOfNews);

        Mail::send($notification);
    }
}
