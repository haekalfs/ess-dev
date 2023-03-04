<?php

namespace App\Http\Controllers;

use App\Models\Company_project;
use App\Models\Project_assignment;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.myproject');
    }

    public function assigning()
    {
        $assignment = Project_assignment::all();
        return view('projects.assigning', compact('assignment'));
    }

    public function project_list()
    {
        $projects = Company_project::all();
        return view('projects.list', compact('projects'));
    }
}
