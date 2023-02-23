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

//Timesheet
Route::get('/timesheet', 'TimesheetController@index')->middleware('auth');
Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry')->middleware('auth');
Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities');
