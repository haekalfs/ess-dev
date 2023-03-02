<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/logout', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//testing
Route::get('/testing', 'TimesheetController@calendar')->middleware('auth');

//Timesheet
    //Editing
Route::post('/timesheet/entry/saves', 'TimesheetController@save_entries')->name('save.entries');//testing

Route::get('/timesheet', 'TimesheetController@index')->middleware('auth')->name('timesheet');
Route::get('/timesheet/entry/{year}/{month}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/entries', 'TimesheetController@save_entries')->name('entries.store');
Route::get('/get-activities/{year}/{month}', 'TimesheetController@getActivities')->name('activities.get-activities');
Route::delete('/activities/{id}', 'TimesheetController@destroy');
    //Preview
Route::get('/timesheet/entry/preview/{year}/{month}', 'TimesheetController@preview')->name('preview.timesheet')->middleware('auth');
Route::get('/timesheet/entry/preview/print/{year}/{month}', 'TimesheetController@print')->middleware('auth');
    //submit
Route::get('/timesheet/entry/submit/{year}/{month}', 'TimesheetController@submit_timesheet')->name('submit-timesheet')->middleware('auth');
//Review
Route::get('/timesheet/review/fm', 'ApprovalController@review')->name('review.finance')->middleware(['checkRole:admin']);
Route::get('/timesheet/review/fm/export', 'ApprovalController@export_excel')->middleware(['checkRole:admin']);


// Testing
Route::get('/development', 'HomeController@notification_indev');
Route::get('/calendar/{year}/{month}', 'TimesheetController@showCalendar')->middleware(['checkRole:admin']);
Route::put('/calendar/{year}/{month}/{day}', 'CalendarController@update')->name('calendar.update')->middleware(['checkRole:admin']);
Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry')->middleware('auth')->middleware(['checkRole:admin']);
Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities')->middleware(['checkRole:admin']);

//Approval
Route::get('/approval', 'ApprovalController@index')->name('approval.main')->middleware(['checkRole:admin']);
Route::get('/approval/director', 'ApprovalController@approval_director')->name('approval-director')->middleware(['checkRole:admin']);
Route::get('/approval/director/{user_id}/{year}/{month}', 'ApprovalController@approve_director')->name('approve-director')->middleware(['checkRole:admin']);
Route::get('/reject/director/{user_id}/{year}/{month}', 'ApprovalController@reject_director')->name('reject-director')->middleware(['checkRole:admin']);

//myprofile
Route::get('/myprofile', 'MyProfileController@index')->name('myprofile');

//Project Assignment
Route::get('/myprojects', 'ProjectController@index')->name('myproject');

//manage users
Route::get('/manage/users', 'UserController@index')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/tambah', 'UserController@tambah')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/store', 'UserController@store')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/edit/{id}', 'UserController@edit')->middleware('auth');
Route::put('/users/update/{id}', 'UserController@update')->middleware('auth')->middleware(['checkRole:admin']);
Route::get('/users/hapus/{id}', 'UserController@delete')->middleware('auth')->middleware(['checkRole:admin']);

