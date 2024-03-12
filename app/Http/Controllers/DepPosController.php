<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Position;
use App\Models\Department;
use App\Models\Timesheet_approver;
use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Support\Facades\Session;

class DepPosController extends Controller
{
    public function index()
    {
        $department_List = Department::all();
        $position_List = Position::all();
        $users = User::all();

        Session::flash('notify', 'This section is linked with Compliance! <a href="/hr/compliance/">Click Here</a> to manage the approvers');
        return view('management.position', ['department_List' => $department_List, 'users' => $users, 'position_List' => $position_List]);
    }

    // DEPARTMENT
    public function add_department(Request $request)
    {
        // Get the last department ID or default to 1
        $nextId = Department::orderBy('id', 'desc')->value('id') + 1;

        // Validate the incoming request
        $validatedData = $request->validate([
            'new_department' => 'required',
            'array_users' => 'required|array',
        ]);

        // Create the department
        $department = Department::create([
            'id' => $nextId,
            'department_name' => $validatedData['new_department'],
        ]);

        // Fetch positions of all selected users
        $userPositions = Users_detail::whereIn('user_id', $validatedData['array_users'])
            ->with('position') // Eager load the position relationship
            ->get();

        // Save the selected users as approvers for the department
        foreach ($userPositions as $userDetail) {
            // Get the position ID for the current user
            $positionId = $userDetail->position->position_level;

            // Determine the group ID and description based on the position ID
            if ($positionId == 1) {
                $groupId = 1;
                $desc = "Director Level";
            } elseif ($positionId == 2) {
                $groupId = 2;
                $desc = "Manager Level";
            } else {
                $groupId = 3; // Or any other default group ID
                $desc = "Default Level"; // Or any other default description
            }

            // Create Timesheet_approver record
            Timesheet_approver::create([
                'department_id' => $department->id,
                'approver_level' => $desc,
                'approver' => $userDetail->user_id,
                'group_id' => $groupId,
            ]);
        }

        return redirect('/hrtools/manage/position')->with('success', 'Department created successfully');
    }

    public function delete_department($id)
    {
        // Retrieve the department with its related approvers
        $department = Department::with('approvers')->findOrFail($id);

        // Delete the related approvers
        $department->approvers()->delete();

        // Delete the department
        $department->delete();

        return redirect('/hrtools/manage/position')->with('success', 'Department deleted successfully');
    }

    //POSITION
    public function add_position(Request $request)
    {
        $lastId = Position::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;

        $this->validate($request, [
            'new_Position' => 'required',
            'priority' => 'required'
        ]);

        Position::create([
            'id' => $nextId,
            'position_name' => $request->new_Position,
            'position_level' => $request->priority
        ]);
        return redirect('/hrtools/manage/position')->with('success', 'Position Create successfully');
    }

    public function delete_position($id)
    {
        $del_pos = DB::table('position')->where('id', $id)->delete();
        return redirect('/hrtools/manage/position')->with('success', 'Position delete successfully');
    }
}
