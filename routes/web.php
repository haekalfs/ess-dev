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

//testing
Route::get('/testing', 'TimesheetController@calendar')->middleware('auth');

//Timesheet
    //Editing
Route::post('/timesheet/entry/saves', 'TimesheetController@save_entries')->name('save.entries');//testing

Route::get('/timesheet/entry/{year}/{month}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/entries', 'TimesheetController@save_entries')->name('entries.store');
Route::get('/get-activities/{year}/{month}', 'TimesheetController@getActivities')->name('activities.get-activities');
Route::delete('/activities/{id}', 'TimesheetController@destroy');
    //Preview
Route::get('/timesheet/entry/preview/{year}/{month}', 'TimesheetController@preview')->name('preview-timesheet');
Route::get('/timesheet/entry/preview/print/{year}/{month}', 'TimesheetController@print');
//submit
Route::get('/timesheet/entry/submit/{year}/{month}', 'TimesheetController@submit_timesheet')->name('submit-timesheet');


// Testing
Route::get('/calendar/{year}/{month}', 'TimesheetController@showCalendar');
Route::put('/calendar/{year}/{month}/{day}', 'CalendarController@update')->name('calendar.update');
Route::get('/timesheet', 'TimesheetController@index')->middleware('auth');
Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities');

//Approval
Route::get('/approval/director', 'ApprovalController@approval_director')->name('approval-director');

//myprofile
Route::get('/myprofile', 'MyProfileController@index')->name('myprofile');


//manage users
Route::get('/manage/users', 'UserController@index')->middleware('auth');


