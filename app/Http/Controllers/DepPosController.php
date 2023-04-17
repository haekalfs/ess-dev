<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Position;
use App\Models\Department;
use App\Models\User;

class DepPosController extends Controller
{
    public function index()
    {
        $department_List = Department::all();
        $position_List = Position::all();
        return view('management.position', ['department_List' => $department_List, 'position_List' => $position_List]);
    }

    // DEPARTMENT

    public function add_department(Request $request)
    {
        $lastId = Department::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;
        
        $this->validate($request, [
            'new_department' => 'required',
            'new_department_code' => 'required'
        ]);

        Department::create([
            'id' => $nextId,
            'department_id' => $request->new_department_code,
            'department_name' => $request->new_department,
        ]);
        return redirect('/hrtools/manage/position')->with('Success', 'Department Create successfully');
    }
    public function delete_department($id)
    {
        $del_dep = DB::table('department')->where('id', $id)->delete();
        return redirect('/hrtools/manage/position')->with('success', 'Department delete successfully');
    }

//END DEPARTMENT
//POSITION
    public function add_position(Request $request)
    {
        $lastId = Position::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;

        $this->validate($request, [
            'new_Position' => 'required',
            'new_Position_code' => 'required'
        ]);

        Position::create([
            'id' => $nextId,
            'position_id' => $request->new_Position_code,
            'position_name' => $request->new_Position,
        ]);
        return redirect('/hrtools/manage/position')->with('Success', 'Position Create successfully');
    }
    public function delete_position($id)
    {
        $del_pos = DB::table('position')->where('id', $id)->delete();
        return redirect('/hrtools/manage/position')->with('Success', 'Position delete successfully');
    }
//END POSITION

}
