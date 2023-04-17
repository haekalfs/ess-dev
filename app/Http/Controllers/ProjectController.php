<?php

namespace App\Http\Controllers;

use App\Models\Company_project;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;

        $records = DB::table('project_assignment_users')
            ->join('company_projects', 'project_assignment_users.company_project_id', '=', 'company_projects.id')
            ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignment_users.*', 'company_projects.project_name', 'company_projects.project_code', 'project_assignments.assignment_no')
            ->where('project_assignment_users.user_id', '=', $userId)
            ->get();
        return view('projects.myproject', ['records' => $records]);
    }

    public function assigning()
    {
        $project = Company_project::all();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.project_name', 'company_projects.project_code')
            ->get();
        return view('projects.assigning', compact('assignment', 'project'));
    }

    public function add_project_assignment(Request $request)
    {
        $this->validate($request,[
            'no_doc' => 'required',
    		'ref_doc' => 'required',
            'project' => 'required',
            'notes' => 'required'
    	]);

        $uniqueId = hexdec(substr(uniqid(), 0, 4));

        while (Project_assignment::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        Project_assignment::create([
            // 'id' => $uniqueId,
    		'assignment_no' => $request->no_doc,
    		'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $uniqueId,
            'company_project_id' => $request->project,
            'notes' => $request->notes
    	]);

        return redirect('/assignment')->with('success', 'Assignment Create successfully');
    }

    public function project_assignment_member($assignment_id)
    {
        $project = Company_project::all();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('projects.assigning_user', ['assignment' => $assignment, 'project' => $project, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }
    
    public function project_assignment_member_view($assignment_id)
    {
        $project = Company_project::all();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('projects.assignment_view_only', ['assignment' => $assignment, 'project' => $project, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }

    public function project_assignment_member_delete($usr_id)
    {
        Project_assignment_user::where('id', $usr_id)->delete();
        return redirect()->back()->with('failed', 'Member deleted!');
    }

    public function add_project_member(Request $request, $assignment_id)
    {
        $this->validate($request,[
            'emp_name' => 'required',
    		'emp_role' => 'required',
            'emp_resp' => 'required',
            'fromTime' => 'required',
            'toTime' => 'required'
    	]);

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (Project_assignment::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        $company_code = Project_assignment::where('id', $assignment_id)->get();
        
        foreach ($company_code as $project) {
            $company_project_id = $project->company_project_id;
        }
        Project_assignment_user::create([
            // 'id' => $uniqueId,
    		'user_id' => $request->emp_name,
    		'role' => $request->emp_role,
            'responsibility' => $request->emp_resp,
            'periode_start' => $request->fromTime,
            'periode_end' => $request->toTime,
            'project_assignment_id' => $assignment_id,
            'company_project_id' => $company_project_id
    	]);
        return redirect()->back()->with('success', 'Assignment saved successfully.');
    }

    public function project_list()
    {
        $projects = Company_project::all();
        return view('projects.list', compact('projects'));
    }
}
