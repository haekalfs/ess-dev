<?php

namespace App\Http\Controllers;

use App\Models\Company_project;
use App\Models\Project_assignment;
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
            'date_prepared' => 'required',
    		'po_req_number' => 'required',
            'req_by' => 'required',
            'ppnSel' => 'required',
            'buyer' => 'required',
            'assignment_cost' => 'required',
            'vendor' => 'required',
            'vendor_address' => 'required',
            'vendor_email' => 'required',
            'vendor_phone' => 'required',
            'purpose_of_purchase' => 'required',
            'top' => 'required',
            'unitType' => 'required',
            'notes' => 'required'
    	]);
        return view('projects.assigning', compact('assignment', 'project'));
    }

    public function add_project_assignment_member(Request $request, $assignment_id)
    {
        $project = Company_project::all();
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.project_name', 'company_projects.project_code')
            ->get();
        return view('projects.assigning', compact('assignment', 'project'));
    }

    public function project_list()
    {
        $projects = Company_project::all();
        return view('projects.list', compact('projects'));
    }
}
