<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company_project;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Project_role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    		'ref_doc' => 'sometimes',
            'project' => 'required',
            'notes' => 'sometimes'
    	]);

        $uniqueIdP = hexdec(substr(uniqid(), 0, 3));

        while (Project_assignment::where('id', $uniqueIdP)->exists()) {
            $uniqueIdP = hexdec(substr(uniqid(), 0, 3));
        }

        Project_assignment::create([
            'id' => $uniqueIdP,
    		'assignment_no' => $request->no_doc,
    		'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $uniqueIdP,
            'company_project_id' => $request->project,
            'notes' => $request->notes
    	]);

        return redirect("/assignment/member/$uniqueIdP")->with('success', "Assignment #$uniqueIdP Create successfully");
    }

    public function project_assignment_member($assignment_id)
    {
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        // var_dump($assignment);
        foreach ($assignment as $as){
            $project = Client::where('id', $as->client_id)->first();
        }
        // $projects = $project->client->client_name;
        // var_dump($project);
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

    public function project_assignment_delete(Request $request, $id)
    {
        $assignment = Project_assignment::find($id);
        $member = Project_assignment_user::where('project_assignment_id', $id);

        if (!$assignment) {
            return redirect('/assignment')->with('failed', 'Project Assignment not found!');
        }

        $assignment->delete();
        $member->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('/assignment')->with('success', 'Project Assignment has been deleted!');
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
        $userAdded = $request->emp_name;
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
        return redirect()->back()->with('success', "$userAdded has been added to the assignment!");
    }

    public function project_list()
    {
        $projects = Company_project::all();
        $p_location = Project_location::all();
        $client = Client::all();
        return view('projects.list', ['projects' => $projects, 'locations' => $p_location, 'clients' => $client]);
    }

    public function getClientsRows()
    {
        // Get the Timesheet records between the start and end dates
        $activities = Client::all();
        
        return response()->json($activities);
    }

    public function create_new_project(Request $request)
    {
        $this->validate($request,[
            'p_code' => 'required',
            'p_name' => 'required',
            'p_client' => 'required',
    		'p_location' => 'required',
            'address' => 'required',
            'from' => 'required',
            'to' => 'required'
    	]);

        $uniqueId = hexdec(substr(uniqid(), 0, 4));

        while (Company_project::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 4));
        }

        $location = Project_location::find($request->p_location);
        Company_project::create([
            // 'id' => $uniqueId,
    		'project_code' => $request->p_code,
    		'alias' => $location->location_code,
            'project_name' => $request->p_name,
            'address' => $request->address,
            'periode_start' => $request->from,
            'periode_end' => $request->to,
            'client_id' => $request->p_client
    	]);

        return redirect('/project_list')->with('success', 'Project Create successfully');
    }

    public function create_new_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $client = new Client;
        $client->client_name = $request->input('client_name');
        $client->address = $request->input('address');
        $client->save();
        
        return response()->json(['success'=>'Client created successfully.', 'client' => $client]);
    }
}
