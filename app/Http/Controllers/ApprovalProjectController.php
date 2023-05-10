<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company_project;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ApprovalProjectController extends Controller
{
    public function index()
	{
        $userRoles = Auth::user()->role_id()->pluck('role_name')->toArray();
        if (in_array('service_dir', $userRoles)) {
            $listAssignment = Project_assignment::where('approval_status', 40)->get();
            return view('approval.project_approval', ['queue' => $listAssignment]);
        } else {
            // Redirect to a URL with a session
            return redirect()->route('approval.main')->with('message', 'You do not have permission to access this page.');
        }
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

        Session::flash('success',"You approved Assignment #$assignment_id!");
        return redirect()->back();
    }
}
