<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Role_template;
use App\Models\API_key;
use App\Models\User;
use App\Models\User_access;
use App\Models\Usr_role;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Row;

class ManagementController extends Controller
{
    public function roles()
    {
        $accessController = new AccessController();
        $result = $accessController->usr_acc(901);

        $users = DB::table('users')
        ->leftjoin('usr_roles', 'users.id', '=', 'usr_roles.user_id')
        ->leftjoin('roles', 'roles.id', '=', 'usr_roles.role_id')
        ->select('users.id', 'users.name', 'roles.description', 'usr_roles.role_name', 'usr_roles.created_at')
        ->orderBy('users.name')
        ->get();

        $usersData = [];
        $u_List = User::all();
        $r_name = Role::all();
        $Assign = User::all();

        $pages = Page::all();
        foreach ($users as $user) {
            if (!isset($usersData[$user->id])) {

                // If this is the first time we've seen this user, create a new entry in the $usersData array
                $usersData[$user->id] = [
                    'us_Dat' => $user->id,
                    'name' => $user->name,
                    'roles' => [],
                    'created_at' => $user->created_at
                ];
            }

            // Add the role to the list of roles for this user
            $usersData[$user->id]['roles'][] = $user->description;
        }

        // Convert the $usersData array into a simple list of users with comma-delimited roles
        $usersList = [];

        foreach ($usersData as $userData) {
            $rolesList = implode(', ', $userData['roles']);
            $usersList[] = [
                'us_Dat' => $userData['us_Dat'],
                'name' => $userData['name'],
                'roles' => $rolesList,
                'created_at' => $userData['created_at']
            ];
        }

        $user_access = DB::table('roles')
        ->select('roles.id', 'pages.description AS pageDesc', 'roles.description', 'pages.id AS page_id', 'user_access.created_at')
        ->leftJoin('user_access', 'roles.id', '=', 'user_access.role_id')
        ->rightJoin('pages', 'pages.id', '=', 'user_access.page_id')
        ->orderBy('page_id', 'asc')
        ->get();

        $usersAcData = [];
        foreach ($user_access as $userAc) {
            if (!isset($usersAcData[$userAc->page_id])) {

                // If this is the first time we've seen this user, create a new entry in the $usersData array
                $usersAcData[$userAc->page_id] = [
                    'page_id' => $userAc->page_id,
                    'page' => $userAc->pageDesc,
                    'GrantAcc' => [],
                    'created_at' => $userAc->created_at
                ];
            }

            // Add the role to the list of roles for this user
            $usersAcData[$userAc->page_id]['GrantAcc'][] = $userAc->description;
        }

        // Convert the $usersData array into a simple list of users with comma-delimited roles
        $usersAcList = [];
        $autoIncrement = 1;
        foreach ($usersAcData as $userAcData) {
            $grantList = implode(', ', $userAcData['GrantAcc']);
            $usersAcList[] = [
                'id' => $autoIncrement++,
                'page_id' => $userAcData['page_id'],
                'page' => $userAcData['page'],
                'grantTo' => $grantList,
                'created_at' => $userAcData['created_at']
            ];
        }

        return view('management.roles', ['users' => $usersList, 'access' => $usersAcList, 'pages' => $pages,'r_name' => $r_name, 'us_List' => $u_List, 'Assign' => $Assign]);
    }

    public function manage_access()
    {
        $r_name = Role::all();

        $pages = Page::all();

        $user_access = DB::table('roles')
        ->select('roles.id', 'pages.description AS pageDesc', 'roles.description', 'pages.id AS page_id', 'user_access.created_at')
        ->leftJoin('user_access', 'roles.id', '=', 'user_access.role_id')
        ->rightJoin('pages', 'pages.id', '=', 'user_access.page_id')
        ->orderBy('page_id', 'asc')
        ->get();

        $usersAcData = [];
        foreach ($user_access as $userAc) {
            if (!isset($usersAcData[$userAc->page_id])) {

                // If this is the first time we've seen this user, create a new entry in the $usersData array
                $usersAcData[$userAc->page_id] = [
                    'page_id' => $userAc->page_id,
                    'page' => $userAc->pageDesc,
                    'GrantAcc' => [],
                    'created_at' => $userAc->created_at
                ];
            }

            // Add the role to the list of roles for this user
            $usersAcData[$userAc->page_id]['GrantAcc'][] = $userAc->description;
        }

        // Convert the $usersData array into a simple list of users with comma-delimited roles
        $usersAcList = [];
        $autoIncrement = 1;
        foreach ($usersAcData as $userAcData) {
            $grantList = implode(', ', $userAcData['GrantAcc']);
            $usersAcList[] = [
                'id' => $autoIncrement++,
                'page_id' => $userAcData['page_id'],
                'page' => $userAcData['page'],
                'grantTo' => $grantList,
                'created_at' => $userAcData['created_at']
            ];
        }

        return view('management.manage_access', ['access' => $usersAcList, 'pages' => $pages,'r_name' => $r_name]);
    }

    public function manage_roles()
    {
        $r_name = Role::all();
        return view('management.manage_roles', ['r_name' => $r_name]);
    }

    public function remove_roles_from_user($id)
    {
        Usr_role::where('user_id', $id)->delete();
        return redirect('/management/security_&_roles/')->with('failed', 'User Roles has been Removed!');
    }

    public function assign_roles(Request $request)
    {
        $lastId = Usr_role::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;

        $this->validate($request, [
            'inputUser' => 'required',
            'inputRole' => 'required'
        ]);

        $roleName = Role::where('id', $request->inputRole)->pluck('role')->first();

        Usr_role::create([
            'id' => $nextId,
            'user_id' => $request->inputUser,
            'role_id' => $request->inputRole,
            'role_name' => $roleName,
        ]);
        return redirect('/management/security_&_roles/')->with('success', 'Role Assign successfully');
    }

    public function remove_access($id){
        User_access::where('page_id', $id)->delete();
        return redirect('/management/security_&_roles/manage/access')->with('failed', 'All Access has been Removed!');
    }

    public function grant_access_to_roles(Request $request)
    {
        $this->validate($request, [
            'inputPage' => 'required',
            'inputRole' => 'required'
        ]);

        $roleName = Role::where('id', $request->inputRole)->pluck('description')->first();
        User_access::create([
            'page_id' => $request->inputPage,
            'role_id' => $request->inputRole
        ]);
        return redirect('/management/security_&_roles/manage/access')->with('success', "Access Granted to $roleName");
    }

    public function add_roles(Request $request)
    {
        // $latestForm = Role_template::whereNull('deleted_at')->orderBy('id')->pluck('id')->first();
        // $nextForm = intval(substr($latestForm, 4))+ 1;
        $lastId = Role::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;

        $this->validate($request, [
            'new_role' => 'required',
            'new_role_code' => 'required'
        ]);

        Role::create([
            'id' => $nextId,
            'role' => $request->new_role_code,
            'description' => $request->new_role
        ]);
        return redirect('/management/security_&_roles/manage/roles')->with('success', 'Role Create successfully');
    }

    public function delete_roles($id)
    {
        DB::table('roles')->where('id', $id)->delete();
        return redirect('/management/security_&_roles/manage/roles')->with('failed', 'Role has been deleted!');
    }

    public function assign_delete($id)
    {
        // $users = DB::table('roles')->where('user_id', $user_id)->delete();
        $Assign = User::where('id', $id);
        return redirect('/management/security_&_roles/')->with('success', 'Role delete successfully');
    }

    public function test($id)
    {
        $kntl = User::where('id', $id)->get();
        return response()->json($kntl);
    }

    public function edit($id)
    {
        $r_name = Role::findOrFail($id);
        return view('/hrtools/manage/roles', compact('role_template'));
    }


    // API Setting

    public function index_api()
    {
        $api_all = API_key::all();
        return view('/management/manage_api', compact('api_all', ));
    }

    public function add_api (Request $request)
    {
        $lastId = API_KEY::orderBy('id', 'desc')->first();
        $nextId = ($lastId) ? $lastId->id + 1 : 1;

        $request->validate([
            'add_name_api' => 'required',
            'add_public_key' => 'required',
            'add_secret_key' => 'required',
        ]);

       API_key::create([
            'id' => $nextId,
            'name' => $request->add_name_api,
            'public_key' => $request->add_public_key,
            'secret_key' => $request->add_secret_key,
        ]);

        return redirect()->back()->with('success', 'New API KEY Add Success');
    }

    public function update_api(Request $request, $id)
    {
        $request->validate([
            'input_public_key.*' => 'sometimes',
            'input_secret_key' => 'sometimes',
        ]);

        $api_edit = API_key::find($id);
        $api_edit->public_key = $request->input_public_key;
        $api_edit->secret_key = $request->input_secret_key;
        $api_edit->save();

        return redirect()->back()->with('success', 'API KEY Update Success');
    }

    //this should be corrected later
    public function retrieveRoles()
    {
        $retrieveRoles = Usr_role::where('role_name', 's-user')
            ->distinct('user_id') // Fetch distinct user_id values
            ->pluck('user_id'); // Retrieve only the user_id values

        return response()->json($retrieveRoles);
    }
}
