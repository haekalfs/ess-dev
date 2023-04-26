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
Route::get('/testing', 'TimesheetController@calendar')->middleware('auth')->middleware(['checkRole:admin']);
Route::post('/timesheet/entry/saves', 'TimesheetController@save_entries')->name('save.entries');//testing

//Timesheet
    //Editing
Route::get('/get-data/{year}/{month}/{id}', 'TimesheetController@getActivitiesEntry');
Route::post('/update-entries/{id}', 'TimesheetController@updateActivitiesEntry')->name('entries.update');

Route::get('/timesheet', 'TimesheetController@index')->middleware('auth')->name('timesheet');
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
    //submit
Route::get('/timesheet/entry/submit/{year}/{month}', 'TimesheetController@submit_timesheet')->name('submit-timesheet')->middleware('auth');
    //Review
Route::get('/timesheet/review/fm', 'ApprovalController@review')->name('review.finance')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/timesheet/review/fm/export', 'ApprovalController@export_excel')->middleware('auth')->middleware(['checkRole:admin']);


// Testing
Route::get('/development', 'HomeController@notification_indev');
Route::get('/calendar/{year}/{month}', 'TimesheetController@showCalendar')->middleware(['checkRole:admin']);
Route::put('/calendar/{year}/{month}/{day}', 'CalendarController@update')->name('calendar.update')->middleware(['checkRole:admin']);
Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry')->middleware('auth')->middleware(['checkRole:admin']);
Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities')->middleware(['checkRole:admin']);

//Approval
Route::get('/approval', 'ApprovalController@index')->name('approval.main')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/approval/timesheet/p', 'ApprovalController@timesheet_approval')->name('approval_primary')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/reject/director/{user_id}/{year}/{month}', 'ApprovalController@reject_director')->name('reject-director')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/approval/director/preview/{user_id}/{year}/{month}', 'ApprovalController@ts_preview')->name('preview.timesheet')->middleware('auth')->middleware(['checkRole:admin']);

Route::get('/approval/director/{user_id}/{year}/{month}', 'ApprovalController@approve_fin_ga_dir')->name('approve-director')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/approval/service_dir/{user_id}/{year}/{month}', 'ApprovalController@approve_service_dir')->middleware('auth')->middleware(['checkRole:admin']);
    //Sub Approval
Route::get('/approval/pm/{user_id}/{year}/{month}', 'ApprovalController@approve_pm')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/approval/pa/{user_id}/{year}/{month}', 'ApprovalController@approve_pa')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/approval/hr/{user_id}/{year}/{month}', 'ApprovalController@approve_hr')->middleware('auth')->middleware(['checkRole:admin']);
    //Sub Approval for Finances Dept
Route::get('/approval/finance/{user_id}/{year}/{month}', 'ApprovalController@approve_fm')->middleware('auth')->middleware(['checkRole:admin']);



//myprofile
Route::get('/myprofile', 'MyProfileController@index')->name('myprofile')->middleware('auth');

//Project Assignment
Route::get('/myprojects', 'ProjectController@index')->name('myproject')->middleware('auth')->middleware(['checkRole:employee,consultant,admin']);
Route::get('/assignment', 'ProjectController@assigning')->name('project-assigning')->middleware('auth')->middleware(['checkRole:admin']);
Route::post('/assignment/add_entries', 'ProjectController@add_project_assignment')->name('add_projects')->middleware('auth');
Route::get('/assignment/member/{id}', 'ProjectController@project_assignment_member')->name('project-assigning')->middleware('auth');
Route::post('/assignment/add_member_to_assignment/{assignment_id}', 'ProjectController@add_project_member')->name('add_projects')->middleware('auth');
Route::get('/project_list', 'ProjectController@project_list')->name('project-list')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/assignment/view/details/{id}', 'ProjectController@project_assignment_member_view')->name('project-assigning-view')->middleware('auth');
Route::get('/assignment/member/delete/{id}', 'ProjectController@project_assignment_member_delete')->name('project-assigning-delete')->middleware('auth');
Route::delete('/assignment/delete/{id}', 'ProjectController@project_assignment_delete')->name('project-assignment-delete')->middleware('auth');
Route::post('/client/create', 'ProjectController@create_new_client')->middleware('auth')->name('insert.new.client')->middleware(['checkRole:admin']);
Route::get('/retrieveClients', 'ProjectController@getClientsRows')->name('client-list')->middleware('auth')->middleware(['checkRole:admin']);

Route::post('/project_list/new', 'ProjectController@create_new_project')->name('project-list-create')->middleware('auth')->middleware(['checkRole:admin']);

//manage users
Route::get('/manage/users', 'UserController@index')->middleware('auth')->middleware(['checkRole:admin,manager']);
Route::get('/users/tambah', 'UserController@tambah')->middleware('auth')->middleware(['checkRole:admin']);
Route::post('/users/store', 'UserController@store')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/edit/{id}', 'UserController@edit')->middleware('auth')->middleware(['checkRole:admin']);
Route::put('/users/update/{id}', 'UserController@update')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/hapus/{id}', 'UserController@delete')->middleware('auth')->middleware(['checkRole:admin']);

//Management
Route::get('/management/security_&_roles/', 'ManagementController@roles')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::get('/management/security_&_roles/manage/access', 'ManagementController@manage_access')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::get('/management/security_&_roles/manage/roles', 'ManagementController@manage_roles')->middleware('auth')->middleware(['checkRole:admin,fm']);
    //link non-page
    Route::get('/management/security_&_roles/remove/roles/{id}', 'ManagementController@remove_roles_from_user')->middleware('auth')->middleware(['checkRole:admin,fm']);
    Route::get('/management/security_&_roles/remove/access/{id}', 'ManagementController@remove_access')->middleware('auth')->middleware(['checkRole:admin,fm']);

    Route::post('/manage/roles/assign_roles', 'ManagementController@assign_roles')->middleware('auth')->middleware(['checkRole:admin,fm']);
    Route::post('/management/security_&_roles/add/access/', 'ManagementController@grant_access_to_roles')->middleware('auth')->middleware(['checkRole:admin,fm']);

    Route::post('/manage/roles/add_roles', 'ManagementController@add_roles')->middleware('auth')->middleware(['checkRole:admin,fm']);
    Route::get('/manage/roles/delete/{id}', 'ManagementController@delete_roles')->middleware('auth')->middleware(['checkRole:admin,fm']);
    Route::get('/manage/roles/assign_delete/{id}', 'ManagementController@assign_delete')->middleware('auth')->middleware(['checkRole:admin,fm']);

//HR TOOLS
Route::get('/hrtools/manage/edit/{id}', 'ManagementController@delete')->middleware('auth')->middleware(['checkRole:admin,fm']);

//Department and Position
Route::get('/hrtools/manage/position', 'DepPosController@index')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::post('/manage/add_department', 'DepPosController@add_department')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::get('/manage/delete_department/{id}', 'DepPosController@delete_department')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::post('/manage/add_position', 'DepPosController@add_position')->middleware('auth')->middleware(['checkRole:admin,fm']);
Route::get('/manage/delete_position/{id}', 'DepPosController@delete_position')->middleware('auth')->middleware(['checkRole:admin,fm']);

///Mailer
Route::get('/kirimemail','MailerController@index');

//medical reimburse
Route::get('/medical/history', 'MedicalController@index')->middleware('auth');
Route::get('/medical/entry', 'MedicalController@entry')->middleware('auth');
Route::post('/medical/entry/store', 'MedicalController@store')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/medical/history/edit/{id}', 'MedicalController@edit')->middleware('auth')->middleware(['checkRole:admin']);


