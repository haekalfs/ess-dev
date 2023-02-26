<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\Timesheet_workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function approval_director()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        $entries = [];
        foreach (range(1, $currentMonth) as $entry) {
            $month = date("F", mktime(0, 0, 0, $entry, 1));
            $approval = Timesheet_workflow::where('activity', 'Submitted')
                ->get();
        }
        return view('approval.director', compact('month'), ['approval' => $approval]);
    }
}
