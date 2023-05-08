<?php

namespace App\Http\Controllers;

use App\Models\Approval_status;
use App\Models\Client;
use App\Models\Company_project;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Project_role;
use App\Models\Requested_assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
            ->where('project_assignments.approval_status', 29)
            ->get();
        $roles = Project_role::all();
        $project = Company_project::all();

        $myRequest = Requested_assignment::where('req_by', Auth::user()->id)->where('status', '0')->get();
        return view('projects.myproject', ['records' => $records, 'usr_roles' => $roles, 'project' => $project, 'myRequest' => $myRequest]);
    }

    public function assigning($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if($yearSelected){
            $currentYear = $yearSelected;
        }
        $project = Company_project::all();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.project_name', 'company_projects.project_code')
            ->whereYear('req_date', $currentYear)
            ->get();
        return view('projects.assigning', compact('assignment', 'project', 'yearsBefore', 'yearSelected'));
    }

    public function add_project_assignment(Request $request)
    {
        $this->validate($request,[
            'no_doc' => 'required',
    		'ref_doc' => 'sometimes',
            'project' => 'required',
            'notes' => 'sometimes'
    	]);

        $uniqueIdP = hexdec(substr(uniqid(), 0, 8));

        while (Project_assignment::where('id', $uniqueIdP)->exists()) {
            $uniqueIdP = hexdec(substr(uniqid(), 0, 8));
        }

        Project_assignment::create([
            'id' => $uniqueIdP,
    		'assignment_no' => $request->no_doc,
    		'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $uniqueIdP,
            'company_project_id' => $request->project,
            'notes' => $request->notes,
            'approval_status' => '40'
    	]);

        return redirect("/assignment/member/$uniqueIdP")->with('success', "Assignment #$uniqueIdP Create successfully");
    }

    public function project_assignment_member($assignment_id)
    {
        Project_assignment_user::where('id', )->count();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        // var_dump($assignment);
        foreach ($assignment as $as){
            $project = Client::where('id', $as->client_id)->first();
            if ($as->approval_status == 40){
                $status = "Waiting for Approval";
            } elseif($as->approval_status == 29) {
                $status = 1;
            } else {
                $status = "Unknown Status";
            }
        }
        // $projects = $project->client->client_name;
        // var_dump($project);
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('projects.assigning_user', ['assignment' => $assignment, 'project' => $project, 'stat' => $status, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }
    
    public function project_assignment_member_view($assignment_id)
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
            } elseif($as->approval_status == 29) {
                $status = "Approved";
            } else {
                $status = "Unknown Status";
            }
        }
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('projects.assignment_view_only', ['assignment' => $assignment, 'stat' => $status, 'project' => $project, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }

    public function project_assignment_member_delete($usr_id)
    {
        Project_assignment_user::where('id', $usr_id)->delete();
        return redirect()->back()->with('failed', 'Member deleted!');
    }

    public function project_assignment_member_delete_two($usr_id, $project_assignment_id)
    {
        $checkTotal = Project_assignment_user::where('project_assignment_id', $project_assignment_id)->count();
        if ($checkTotal == 1) {
            Project_assignment::where('id', $project_assignment_id)->delete();
            Project_assignment_user::where('id', $usr_id)->delete();
        } else {
            Project_assignment_user::where('id', $usr_id)->delete();
        }
        
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

    public function delete_client($id)
    {
        $client = Client::find($id);

        if ($client) {
            $client->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['failed' => false, 'message' => 'Client not found!']);
        }
    }

    public function listLocations()
    {
        $p_location = Project_location::all();
        return response()->json($p_location);
    }

    public function delete_location($id)
    {
        $location = Project_location::find($id);

        if ($location) {
            $location->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['failed' => false, 'message' => 'Location not found!']);
        }
    }

    public function create_new_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loc_code' => 'required',
            'loc_desc' => 'required',
            'loc_fare' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $loc = new Project_location;
        $loc->location_code = $request->input('loc_code');
        $loc->description = $request->input('loc_desc');
        $loc->fare = $request->input('loc_fare');
        $loc->save();
        
        return response()->json(['success'=>'Client created successfully.']);
    }

    public function listProjectRoles()
    {
        $p_role = Project_role::all();
        return response()->json($p_role);
    }

    public function delete_project_role($id)
    {
        $role = Project_role::find($id);

        if ($role) {
            $role->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['failed' => false, 'message' => 'Client not found!']);
        }
    }

    public function create_new_project_roles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_code' => 'required',
            'role_desc' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $role = new Project_role;
        $role->role_code = $request->input('role_code');
        $role->role_name = $request->input('role_desc');
        $role->save();
        
        return response()->json(['success'=>'Client created successfully.']);
    }

    public function company_project_view($id)
    {
        $project = Company_project::find($id);
        $project_member = Project_assignment_user::where('company_project_id', $id)->get();
        $project_id = Company_project::where('id', $id)->pluck('id')->first();
        return view('projects.company_project_view_only', ['project' => $project, 'project_member' => $project_member, 'project_id' => $project_id]);
    }

    public function project_delete(Request $request, $id)
    {
        $project = Company_project::find($id);
        $assignment = Project_assignment::where('company_project_id', $id);
        $member = Project_assignment_user::where('project_assignment_id', $id);

        if (!$project) {
            return redirect('/project_list')->with('failed', 'Project not found!');
        }

        $project->delete();
        $assignment->delete();
        $member->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect('/project_list')->with('success', "Project Organization #$id has been deleted!");
    }

    public function requested_assignment()
    {
        $request = Requested_assignment::all();
        return view('projects.requested_assignment', ['request' => $request]);
    }
    
    public function requested_assignment_entry(Request $request)
    {
        $this->validate($request,[
            'emp_name' => 'required',
            'emp_role' => 'required',
            'project' => 'required',
    		'emp_resp' => 'required',
            'fromTime' => 'required',
            'toTime' => 'required'
    	]);

        Requested_assignment::create([
    		'req_date' => date('Y-m-d'),
    		'req_by' => $request->emp_name,
            'role' => $request->emp_role,
            'responsibility' => $request->emp_resp,
            'company_project_id' => $request->project,
            'periode_start' => $request->fromTime,
            'periode_end' => $request->toTime,
            'status' => 0
    	]);

        return redirect('/myprojects')->with('success', 'Assignment has been requested!');
    }

    public function requested_assignment_view($id)
    {
        $request = Requested_assignment::find($id);
        return view('projects.requested_assignment_view', ['request' => $request]);
    }

    public function requested_assignment_approve($id)
    {
        date_default_timezone_set("Asia/Jakarta");
        Requested_assignment::where('id', $id)->update(['status' => '1']);

        Session::flash('success',"You approved the assignment request!");
        return redirect()->back();
    }

    public function add_project_assignment_from_request(Request $request, $id)
    {
        $this->validate($request,[
            'no_doc' => 'required',
    		'ref_doc' => 'sometimes',
            'notes' => 'sometimes'
    	]);

        $requestAss = Requested_assignment::find($id);

        $uniqueIdP = hexdec(substr(uniqid(), 0, 8));

        while (Project_assignment::where('id', $uniqueIdP)->exists()) {
            $uniqueIdP = hexdec(substr(uniqid(), 0, 8));
        }

        Project_assignment::create([
            'id' => $uniqueIdP,
    		'assignment_no' => $request->no_doc,
    		'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $uniqueIdP,
            'company_project_id' => $requestAss->company_project_id,
            'notes' => $request->notes,
            'approval_status' => '40'
    	]);

        Project_assignment_user::create([
    		'user_id' => $requestAss->req_by,
    		'role' => $requestAss->role,
            'responsibility' => $requestAss->responsibility,
            'periode_start' => $requestAss->periode_start,
            'periode_end' => $requestAss->periode_end,
            'project_assignment_id' => $uniqueIdP,
            'company_project_id' => $requestAss->company_project_id
    	]);

        return redirect('/assignment')->with('success', "Assignment #$uniqueIdP from request has been created successfully");
    }
}
