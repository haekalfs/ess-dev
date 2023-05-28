<?php

namespace App\Http\Controllers;

use App\Jobs\SendAssignmentNotification;
use App\Mail\AssignmentNotifyToUser;
use App\Models\Client;
use App\Models\Company_project;
use App\Models\Notification_alert;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ApprovalProjectController extends Controller
{
    public function index()
	{
        $accessController = new AccessController();
        $result = $accessController->usr_acc(204);
        
        $listAssignment = Project_assignment::where('approval_status', 40)->get();
        return view('approval.project_approval', ['queue' => $listAssignment]);
	}

    public function preview_assignment($assignment_id)
    {
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        foreach ($assignment as $as){
            $project = Client::where('id', $as->client_id)->first();
            if ($as->approval_status == 40){
                $status = "Waiting for Approval";
                $btnApprove = '<div class="col-auto">
                    <a href="/approval/project/assignment/approve/' . $assignment_id . '" class="btn btn-primary btn-sm">
                        <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Approve
                    </a>
                </div>';
            } elseif($as->approval_status == 29) {
                $status = "Approved";
                $btnApprove = "";
            } elseif($as->approval_status == 404) {
                $status = "Rejected";
                $btnApprove = "";
            } else {
                $status = "Unknown Status";
                $btnApprove = "";
            }
        }
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('approval.assignment_preview', ['btnApprove' => $btnApprove,'assignment' => $assignment, 'stat' => $status, 'project' => $project, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }

    public function approve_assignment($assignment_id)
    {
        date_default_timezone_set("Asia/Jakarta");
        Project_assignment::where('id', $assignment_id)->update(['approval_status' => '29']);

        $assignment = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        
        $userNotify = [];
        foreach($assignment as $as){
            $entry = new Notification_alert();
            $entry->user_id = $as->user_id;
            $cp = $as->company_project->project_name;
            $entry->message = "You have assigned to an assignment of $cp!";
            $entry->importance = 1;
            $entry->save();
            $userNotify[] = $as->user_id;
            $assignmentName = $as->company_project->project_name;
        }

        $employees = User::whereIn('id', $userNotify)->get();

        foreach ($employees as $employee) {
            dispatch(new SendAssignmentNotification($employee, $assignmentName));
        }
        
        Session::flash('success',"You approved Assignment #$assignment_id!");
        return redirect()->back();
    }

    public function reject_assignment($assignment_id)
    {
        date_default_timezone_set("Asia/Jakarta");
        Project_assignment::where('id', $assignment_id)->update(['approval_status' => '404']);

        Session::flash('failed',"You rejected Assignment #$assignment_id!");
        return redirect()->back();
    }
}
