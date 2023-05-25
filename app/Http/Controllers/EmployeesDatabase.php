<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\MasterDataExport;
use App\Models\Users_detail;

class EmployeesDatabase extends Controller
{
    public function index(Request $request)   
    {
        // $listUser = User::with('users_detail');
        // $positionIds = explode(',', $request->position_id);
        // $listUser = User::join('users_details', 'users.id', '=', 'users_details.user_id')
        // ->whereIn('users_details.position_id', $positionIds)
        //     ->get();
        $status = $request->query('status');
        $positionIds = explode(',', $request->position_id);
        $users = User::join('users_details', 'users.id', '=', 'users_details.user_id');

        if (!empty($request->position_id)) {
            $users->whereIn('users_details.position_id', $positionIds);
        }

        if ($status) {
            if ($status === 'Active') {
                $users->where('status_active', 'Active');
            } elseif ($status === 'nonActive') {
                $users->where('status_active', 'nonActive');
            }
        }
        $listUser = $users->get();

        return view('management.database.employees', ['users' => $listUser]);
    }


    public function exportData()
    {
        // Ambil data dari tabel user
        $users = User::with('users_detail.department','users_detail.position')->get();

        // Ambil data dari tabel users_detail
        $usersDetail = Users_detail::all();

        return (new MasterDataExport($users, $usersDetail))->toResponse(request());
    }
}
