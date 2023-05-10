<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[App\Http\Controllers\HomeController::class, 'index'])->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//testing
Route::get('/testing', 'TimesheetController@calendar')->middleware('auth');
Route::post('/timesheet/entry/saves', 'TimesheetController@save_entries')->name('save.entries');//testing

//Timesheet
    //Editing
Route::get('/get-data/{year}/{month}/{id}', 'TimesheetController@getActivitiesEntry');
Route::post('/update-entries/{id}', 'TimesheetController@updateActivitiesEntry')->name('entries.update');

Route::get('/timesheet/{yearSelected?}', 'TimesheetController@index')->middleware('auth')->name('timesheet');
Route::get('/timesheet/entry/{year}/{month}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/entries', 'TimesheetController@save_entries')->name('entries.store');
Route::post('/multiple_entries', 'TimesheetController@save_multiple_entries')->name('multiple.entries.store');
Route::get('/get-activities/{year}/{month}', 'TimesheetController@getActivities')->name('activities.get-activities');
    //DELETE
Route::delete('/activities/{id}', 'TimesheetController@destroy')->middleware('auth');
Route::delete('/activities/all/{year}/{month}', 'TimesheetController@destroy_all')->middleware('auth');
    //Preview
Route::get('/timesheet/entry/preview/{year}/{month}', 'TimesheetController@preview')->name('preview.timesheet')->middleware('auth');
Route::get('/timesheet/entry/preview/print/{year}/{month}', 'TimesheetController@print')->middleware('auth');
Route::get('/timesheet/approval/preview/print/{year}/{month}/{user_timesheet}', 'TimesheetController@print_selected')->middleware('auth');
    //submit
Route::get('/timesheet/entry/submit/{year}/{month}', 'TimesheetController@submit_timesheet')->name('submit-timesheet')->middleware('auth');
    //Review
Route::get('/timesheet/review/fm', 'ApprovalController@review')->name('review.finance')->middleware('auth');
Route::get('/timesheet/review/fm/export/{month}/{year}', 'ExportTimesheet@export_excel')->middleware('auth');


// Testing
Route::get('/development', 'HomeController@notification_indev');
Route::get('/calendar/{year}/{month}', 'TimesheetController@showCalendar');
Route::put('/calendar/{year}/{month}/{day}', 'CalendarController@update')->name('calendar.update');
Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities');

//Approval
Route::get('/approval', 'ApprovalController@index')->name('approval.main')->middleware('auth');
Route::get('/approval/timesheet/p', 'ApprovalController@timesheet_approval')->name('approval_primary')->middleware('auth');
Route::get('/reject/director/{user_id}/{year}/{month}', 'ApprovalController@reject_director')->name('reject-director')->middleware('auth');
Route::get('/approval/timesheet/preview/{user_id}/{year}/{month}', 'ApprovalController@ts_preview')->name('preview.timesheet')->middleware('auth');

Route::get('/approval/fin_ga_dir/{user_id}/{year}/{month}', 'ApprovalController@approve_fin_ga_dir')->name('approve-director')->middleware('auth');
Route::get('/approval/service_dir/{user_id}/{year}/{month}', 'ApprovalController@approve_service_dir')->middleware('auth');
    //Sub Approval
Route::get('/approval/pm/{user_id}/{year}/{month}', 'ApprovalController@approve_pm')->middleware('auth');
Route::get('/approval/pa/{user_id}/{year}/{month}', 'ApprovalController@approve_pa')->middleware('auth');
Route::get('/approval/hr/{user_id}/{year}/{month}', 'ApprovalController@approve_hr')->middleware('auth');
    //Sub Approval for Finances Dept
Route::get('/approval/fm/{user_id}/{year}/{month}', 'ApprovalController@approve_fm')->middleware('auth');

    //Approval Project
Route::get('/approval/project/assignment/', 'ApprovalProjectController@index')->name('approval.project')->middleware('auth');
Route::get('/approval/project/assignment/preview/{id}', 'ApprovalProjectController@preview_assignment')->name('preview.project.assignment')->middleware('auth');
Route::get('/approval/project/assignment/approve/{id}', 'ApprovalProjectController@approve_assignment')->name('approve.project.assignment')->middleware('auth');

//myprofile
Route::get('/myprofile', 'MyProfileController@index')->name('myprofile')->middleware('auth');

//
Route::get('/leave/history/{yearSelected?}', 'LeaveController@history')->name('leave')->middleware('auth');

//Project Assignment
Route::get('/myprojects', 'ProjectController@index')->name('myproject')->middleware('auth');
Route::get('/assignment/{yearSelected?}', 'ProjectController@assigning')->name('project-assigning')->middleware('auth');
Route::post('/assignment/add_entries', 'ProjectController@add_project_assignment')->name('add_projects')->middleware('auth');
Route::get('/assignment/member/{id}', 'ProjectController@project_assignment_member')->name('project-assigning')->middleware('auth');
Route::post('/assignment/add_member_to_assignment/{assignment_id}', 'ProjectController@add_project_member')->name('add_projects')->middleware('auth');
Route::get('/project_list', 'ProjectController@project_list')->name('project-list')->middleware('auth');
Route::get('/assignment/view/details/{id}', 'ProjectController@project_assignment_member_view')->name('project-assigning-view')->middleware('auth');
Route::get('/assignment/member/delete/{id}', 'ProjectController@project_assignment_member_delete')->name('project-assigning-delete')->middleware('auth');

Route::get('/project_list/delete/assignment/member/{id}/{project_assignment_id}', 'ProjectController@project_assignment_member_delete_two')->name('project-assigning-delete-two')->middleware('auth');

Route::delete('/assignment/delete/{id}', 'ProjectController@project_assignment_delete')->name('project-assignment-delete')->middleware('auth');
Route::post('/client/create', 'ProjectController@create_new_client')->middleware('auth')->name('insert.new.client');
Route::get('/retrieveClients', 'ProjectController@getClientsRows')->name('client-list')->middleware('auth');
Route::delete('/project_list/delete/client/{id}', 'ProjectController@delete_client')->name('client-delete')->middleware('auth');

Route::post('/project_list/new', 'ProjectController@create_new_project')->name('project-list-create')->middleware('auth');
Route::get('/retrieveLocations', 'ProjectController@listLocations')->name('list-location')->middleware('auth');
Route::post('/location/create', 'ProjectController@create_new_location')->middleware('auth')->name('insert.new.location');
Route::delete('/project_list/delete/location/{id}', 'ProjectController@delete_location')->name('location-delete')->middleware('auth');
Route::get('/project_list/view/details/{id}', 'ProjectController@company_project_view')->name('company-project-view')->middleware('auth');
Route::delete('/project_list/delete/{id}', 'ProjectController@project_delete')->name('project-delete')->middleware('auth');
Route::get('/retrieveProjectRoles', 'ProjectController@listProjectRoles')->name('list-project-roles')->middleware('auth');
Route::post('/projectRole/create', 'ProjectController@create_new_project_roles')->middleware('auth')->name('insert.new.role');
Route::delete('/project_list/delete/project_role/{id}', 'ProjectController@delete_project_role')->name('role-delete')->middleware('auth');

Route::get('/assignment/requested/by/user', 'ProjectController@requested_assignment')->name('myproject')->middleware('auth');
Route::post('/assignment/request/', 'ProjectController@requested_assignment_entry')->middleware('auth')->name('req.ass');
Route::get('/assignment/requested/by/user/view/{id}', 'ProjectController@requested_assignment_view')->middleware('auth');
Route::get('/assignment/requested/by/user/approve/{id}', 'ProjectController@requested_assignment_approve')->middleware('auth');
Route::post('/assignment/add_entries/based_on/request/{id}', 'ProjectController@add_project_assignment_from_request')->middleware('auth');

//manage users
Route::get('/manage/users', 'UserController@index')->middleware('auth')->middleware(['checkRole:admin,manager']);
Route::get('/users/tambah', 'UserController@tambah')->middleware('auth');
Route::post('/users/store', 'UserController@store')->middleware('auth');
Route::get('/users/edit/{id}', 'UserController@edit')->middleware('auth');
Route::put('/users/update/{id}', 'UserController@update')->middleware('auth');
Route::get('/users/hapus/{id}', 'UserController@delete')->middleware('auth');
Route::post('/check-user-id', 
function () {
    $userId = request('usr_id');
    $user = App\Models\User::where('id', $userId)->first();

    if ($user) {
        return response()->json(['exists' => true]);
    } else {
        return response()->json(['exists' => false]);
    }
});


//list consul and employee
Route::get('/manage/consultant', 'UserController@consultant')->middleware('auth')->middleware(['checkRole:admin,manager']);
Route::get('/manage/employee', 'UserController@employee')->middleware('auth')->middleware(['checkRole:admin,manager']);

//Management
Route::get('/management/security_&_roles/', 'ManagementController@roles')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/management/security_&_roles/manage/access', 'ManagementController@manage_access')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/management/security_&_roles/manage/roles', 'ManagementController@manage_roles')->middleware('auth')->middleware(['checkRole:admin']);
    //link non-page
    Route::get('/management/security_&_roles/remove/roles/{id}', 'ManagementController@remove_roles_from_user')->middleware('auth')->middleware(['checkRole:admin']);
    Route::get('/management/security_&_roles/remove/access/{id}', 'ManagementController@remove_access')->middleware('auth')->middleware(['checkRole:admin']);

    Route::post('/manage/roles/assign_roles', 'ManagementController@assign_roles')->middleware('auth')->middleware(['checkRole:admin']);
    Route::match(['get', 'post'], '/management/security_&_roles/add/access/', 'ManagementController@grant_access_to_roles')->middleware('auth')->middleware(['checkRole:admin']);

    Route::post('/manage/roles/add_roles', 'ManagementController@add_roles')->middleware('auth')->middleware(['checkRole:admin']);
    Route::get('/manage/roles/delete/{id}', 'ManagementController@delete_roles')->middleware('auth')->middleware(['checkRole:admin']);
    Route::get('/manage/roles/assign_delete/{id}', 'ManagementController@assign_delete')->middleware('auth')->middleware(['checkRole:admin']);

//Employees Database
Route::get('/manage/list/employees', 'EmployeesDatabase@index')->name('emp.database')->middleware('auth');

//HR TOOLS
Route::get('/hrtools/manage/edit/{id}', 'ManagementController@delete')->middleware('auth');
Route::get('/hr/compliance/', 'HrController@index')->middleware('auth');
Route::get('/hr/compliance/timesheet/settings', 'HrController@timesheet')->middleware('auth');
Route::put('/hr/compliance/timesheet/settings/save', 'HrController@timesheet_settings_save')->middleware('auth');

//Department and Position
Route::get('/hrtools/manage/position', 'DepPosController@index')->middleware('auth');
Route::post('/manage/add_department', 'DepPosController@add_department')->middleware('auth');
Route::get('/manage/delete_department/{id}', 'DepPosController@delete_department')->middleware('auth');
Route::post('/manage/add_position', 'DepPosController@add_position')->middleware('auth');
Route::get('/manage/delete_position/{id}', 'DepPosController@delete_position')->middleware('auth');

///Mailer
Route::get('/kirimemail','MailerController@index');

//medical reimburse
Route::get('/medical/history', 'MedicalController@index')->middleware('auth');
Route::get('/medical/entry', 'MedicalController@entry')->middleware('auth');
Route::post('/medical/entry/store', 'MedicalController@store')->middleware('auth');
Route::get('/medical/history/edit/{id}', 'MedicalController@edit')->middleware('auth');


