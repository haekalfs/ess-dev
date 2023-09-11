<?php

namespace App\Http\Controllers;

use App\Models\Company_project;
use App\Models\Department;
use App\Models\Reimbursement;
use App\Models\Timesheet_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReimburseController extends Controller
{
    public function history($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $currentYear = date('Y');
        if($yearSelected){
            $currentYear = $yearSelected;
        }

        $project = Company_project::all();
<<<<<<< HEAD
        $assignment = DB::table('project_assignments')
            ->join('company_projects', 'project_assignments.company_project_id', '=', 'company_projects.id')
            // ->join('project_assignments', 'project_assignment_users.project_assignment_id', '=', 'project_assignments.id')
            ->select('project_assignments.*', 'company_projects.project_name', 'company_projects.project_code')
            ->whereYear('req_date', $currentYear)
            ->get();
        return view('reimbursement.history', compact('assignment', 'project', 'yearsBefore', 'yearSelected'));
    }
=======
        $assignment = Reimbursement::where('f_req_by', Auth::id())->get();
        return view('reimbursement.history', compact('assignment', 'project', 'yearsBefore', 'yearSelected'));
    }

    public function create_request($yearSelected = null)
    {
        $projects = Company_project::all();
        $approver = Department::all();
        
        return view('reimbursement.request', compact('projects', 'approver'));
    }

    public function submit_request(){
        
    }
>>>>>>> d332f228c6cdc499c51ce82d36f4c4e81fb5347f
}
