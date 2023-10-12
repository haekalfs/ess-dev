<?php

namespace App\Http\Controllers;

use App\Jobs\NotifyAssignmentCreation;
use App\Mail\ApprovalAssignment;
use App\Models\Approval_status;
use App\Models\Client;
use App\Models\Company_project;
use App\Models\Notification_alert;
use App\Models\Project_assignment;
use App\Models\Project_assignment_user;
use App\Models\Project_location;
use App\Models\Project_role;
use App\Models\Requested_assignment;
use App\Models\User;
use App\Models\Usr_role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
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

        date_default_timezone_set("Asia/Jakarta");

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
        if ($yearSelected) {
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
        $this->validate($request, [
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
            'id' => $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc),
            'assignment_no' => $request->no_doc,
            'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc),
            'company_project_id' => $request->project,
            'notes' => $request->notes,
            'approval_status' => '40'
        ]);

        $url = $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc);

        $roleToApprove = Usr_role::where('role_name', 'service_dir')->pluck('user_id')->toArray();
        $employees = User::whereIn('id', $roleToApprove)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifyAssignmentCreation($employee));
        }
        return redirect("/assignment/member/$url")->with('success', "Assignment #$uniqueIdP Create successfully");
    }

    public function project_assignment_member($assignment_id)
    {
        Project_assignment_user::where('id',)->count();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        // var_dump($assignment);
        $refNum = '';
        foreach ($assignment as $as) {
            $project = Client::where('id', $as->client_id)->first();
            $refNum = $as->assignment_no;
            if ($as->approval_status == 40) {
                $status = "Waiting for Approval";
            } elseif ($as->approval_status == 29) {
                $status = 1;
            } elseif ($as->approval_status == 404) {
                $status = 404;
            } else {
                $status = "Unknown Status";
            }
        }
        // $projects = $project->client->client_name;
        // var_dump($status);
        $emp = User::all();
        $roles = Project_role::all();
        $project_member = Project_assignment_user::where('project_assignment_id', $assignment_id)->get();
        return view('projects.assigning_user', ['assignment' => $assignment, 'project' => $project, 'refNum' => $refNum, 'stat' => $status, 'user' => $emp, 'usr_roles' => $roles, 'assignment_id' => $assignment_id, 'project_member' => $project_member]);
    }

    public function project_assignment_member_view($assignment_id)
    {
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.*')
            ->where('project_assignments.id', '=', $assignment_id)
            ->get();
        foreach ($assignment as $as) {
            $project = Client::where('id', $as->client_id)->first();
            if ($as->approval_status == 40) {
                $status = "Waiting for Approval";
            } elseif ($as->approval_status == 29) {
                $status = "Approved";
            } elseif ($as->approval_status == 404) {
                $status = "Rejected";
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
        $this->validate($request, [
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

        $userFind = User::find($request->emp_name);
        $userAdded = $userFind->name;
        $from = Carbon::createFromFormat('Y-m-d', $request->fromTime);
        $to = Carbon::createFromFormat('Y-m-d', $request->toTime);
        $userId = $request->emp_name;
        $companyProjectId = $company_project_id;

        $existingAssignment = Project_assignment_user::where('user_id', $userId)
            ->where('company_project_id', $companyProjectId)
            ->where(function ($query) use ($from, $to) {
                $query->where(function ($query) use ($from, $to) {
                    $query->where('periode_start', '<=', $from->format('Y-m-d'))
                        ->where('periode_end', '>=', $from->format('Y-m-d'));
                })->orWhere(function ($query) use ($from, $to) {
                    $query->where('periode_start', '<=', $to->format('Y-m-d'))
                        ->where('periode_end', '>=', $to->format('Y-m-d'));
                });
            })
            ->get();

        if ($existingAssignment->isNotEmpty()) {
            return redirect()->back()->with('failed', "$userAdded already have an assignment that intersect with current periode!");
        } else {
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
        $this->validate($request, [
            'p_code' => 'required',
            'p_name' => 'required',
            'p_client' => 'required',
            'p_location' => 'required',
            'address' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);
    
        // Retrieve an array of selected location IDs
        $selectedLocationIds = $request->input('p_location');
    
        // Retrieve the selected locations from the database
        $selectedLocations = Project_location::whereIn('id', $selectedLocationIds)->get();
    
        // Extract the location codes and join them with commas
        $alias = $selectedLocations->pluck('location_code')->implode(', ');
    
        // Create a new Company_project record
        Company_project::create([
            'project_code' => $request->p_code,
            'alias' => $alias, // Save the comma-separated location codes
            'project_name' => $request->p_name,
            'address' => $request->address,
            'periode_start' => $request->from,
            'periode_end' => $request->to,
            'client_id' => $request->p_client
        ]);
    
        return redirect('/project_list')->with('success', 'Project created successfully');
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

        return response()->json(['success' => 'Client created successfully.', 'client' => $client]);
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

        return response()->json(['success' => 'Client created successfully.']);
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
            'role_desc' => 'required',
            'role_fare' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $role = new Project_role;
        $role->role_code = $request->input('role_code');
        $role->role_name = $request->input('role_desc');
        $role->fare = $request->role_fare;
        $role->save();

        return response()->json(['success' => 'Client created successfully.']);
    }

    public function company_project_view($id)
    {
        $project = Company_project::find($id);
        $project_member = Project_assignment_user::where('company_project_id', $id)->get();
        $project_id = Company_project::where('id', $id)->pluck('id')->first();
        $p_location = Project_location::all();
        $client = Client::all();
        return view('projects.company_project_view_only', ['project' => $project, 'project_member' => $project_member, 'project_id' => $project_id, 'clients' => $client, 'locations' => $p_location]);
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
        $validator = Validator::make($request->all(), [
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
        $name = Requested_assignment::where('id', $id)->pluck('req_by')->first();

        $entry = new Notification_alert;
        $entry->user_id = $name;
        $entry->message = "Your Assignment Request is Approved!";
        $entry->importance = 1;
        $entry->save();

        Session::flash('success', "You approved the assignment request!");
        return redirect()->back();
    }

    public function requested_assignment_reject($id)
    {
        date_default_timezone_set("Asia/Jakarta");
        Requested_assignment::where('id', $id)->update(['status' => '404']);
        $name = Requested_assignment::where('id', $id)->pluck('req_by')->first();

        $entry = new Notification_alert;
        $entry->user_id = $name;
        $entry->message = "Your Assignment Request is Rejected!";
        $entry->importance = 404;
        $entry->save();

        Session::flash('failed', "You rejected the assignment request!");
        return redirect()->back();
    }

    public function add_project_assignment_from_request(Request $request, $id)
    {
        $this->validate($request, [
            'no_doc' => 'required',
            'ref_doc' => 'sometimes',
            'notes' => 'sometimes'
        ]);

        $requestAss = Requested_assignment::find($id);

        $uniqueIdP = hexdec(substr(uniqid(), 0, 8));

        while (Project_assignment::where('id', $uniqueIdP)->exists()) {
            $uniqueIdP = hexdec(substr(uniqid(), 0, 8));
        }

        $idAss = $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc);
        
        Project_assignment::create([
            'id' => $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc),
            'assignment_no' => $request->no_doc,
            'reference_doc' => $request->ref_doc,
            'req_date' => date('Y-m-d'),
            'req_by' => Auth::user()->id,
            'task_id' => $idAss,
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
            'project_assignment_id' => $idAss,
            'company_project_id' => $requestAss->company_project_id
        ]);

        $from = Carbon::createFromFormat('Y-m-d', $requestAss->periode_start);
        $to = Carbon::createFromFormat('Y-m-d', $requestAss->periode_end);
        $userId = $requestAss->req_by;
        $companyProjectId = $requestAss->company_project_id;

        // $existingAssignment = Project_assignment_user::where('user_id', $userId)
        //     ->where('company_project_id', $companyProjectId)
        //     ->where(function ($query) use ($from, $to) {
        //         $query->where(function ($query) use ($from, $to) {
        //             $query->where('periode_start', '<=', $from->format('Y-m-d'))
        //                 ->where('periode_end', '>=', $from->format('Y-m-d'));
        //         })->orWhere(function ($query) use ($from, $to) {
        //             $query->where('periode_start', '<=', $to->format('Y-m-d'))
        //                 ->where('periode_end', '>=', $to->format('Y-m-d'));
        //         });
        //     })
        //     ->get();
        $roleToApprove = Usr_role::where('role_name', 'service_dir')->pluck('user_id')->toArray();
        $employees = User::whereIn('id', $roleToApprove)->get();

        foreach ($employees as $employee) {
            dispatch(new NotifyAssignmentCreation($employee));
        }

        return redirect()->back()->with('success', "$userId has been added to an assignment!");

        // if ($existingAssignment->isNotEmpty()) {
        //     return redirect()->back()->with('failed', "$userId already have an assignment that intersect with current periode!");
        // } else {
        //     Project_assignment_user::create([
        //         'user_id' => $requestAss->req_by,
        //         'role' => $requestAss->role,
        //         'responsibility' => $requestAss->responsibility,
        //         'periode_start' => $requestAss->periode_start,
        //         'periode_end' => $requestAss->periode_end,
        //         'project_assignment_id' => $uniqueIdP . preg_replace("/[^0-9]/", "", $request->no_doc),
        //         'company_project_id' => $requestAss->company_project_id
        //     ]);

        // }
    }

    public function retrieveProjectData($id)
    {
        // Get the Timesheet records between the start and end dates
        $project = Company_project::find($id);

        return response()->json($project);
    }

    public function updateProjectData(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'p_code' => 'required',
            'p_name' => 'required',
            'from' => 'required',
            'to' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $from = $request->input('from');
        $fromDate = Carbon::createFromFormat('Y-m-d', $from);
        $formattedFrom = $fromDate->format('d-M-Y');

        $to = $request->input('to');
        $toDate = Carbon::createFromFormat('Y-m-d', $to);
        $formattedTo = $toDate->format('d-M-Y');

        $cp = Company_project::find($id);
        $clientId = $request->input('p_client');
        if ($clientId == NULL || $clientId == '') {
            $clientId = $cp->client_id;
        }
        $p_loc = $request->input('p_location');
        if ($p_loc == NULL || $p_loc == '') {
            $alias = $cp->alias;
        }
        // Retrieve the selected locations from the database
        $selectedLocations = Project_location::whereIn('id', $p_loc)->get();
    
        // Extract the location codes and join them with commas
        $alias = $selectedLocations->pluck('location_code')->implode(', ');
    
        $cp->project_code = $request->input('p_code');
        $cp->alias = $alias;
        $cp->project_name = $request->input('p_name');
        $cp->address = $request->input('address');
        $cp->periode_start = $formattedFrom;
        $cp->periode_end = $formattedTo;
        $cp->client_id = $clientId;
        $cp->save();

        return response()->json(['success' => 'Project updated successfully.']);
    }

    public function retrieveUsrPeriodData($id)
    {
        // Get the Timesheet records between the start and end dates
        $usrData = Project_assignment_user::find($id);

        return response()->json($usrData);
    }

    public function updateUserPeriod(Request $request, $usr_id)
    {
        $validator = Validator::make($request->all(), [
            'fromPeriode' => 'sometimes',
            'toPeriode' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }

        $usr = Project_assignment_user::find($usr_id);
        if (!empty($request->fromPeriode)) {
            $usr->periode_start = $request->fromPeriode;
        }
        $usr->periode_end = $request->toPeriode;
        $usr->save();

        return response()->json(['success' => 'User Periode updated successfully.']);
    }
}
