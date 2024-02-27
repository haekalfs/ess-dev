<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\HolidayController;
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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth');

Route::get('login/google', [LoginController::class, 'redirectToProvider']);
Route::get('login/google/callbackESS', [LoginController::class, 'handleProviderCallback']);

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::middleware(['suspicious'])->group(function () {
        //Commands
        Route::get('/fetch-and-format-holidays', [HolidayController::class, 'fetchAndFormatHolidays']);
        Route::get('/cut-leave-based-on-joint-holidays', 'CommandsController@cut_leave_based_on_joint_holidays');
        Route::get('/send-timesheet-entry-reminder', 'CommandsController@send_reminder_timesheet_entry');
        Route::get('/send-timesheet-approval-reminder', 'CommandsController@send_approval_timesheet_entry');
        Route::get('/check-employment-status', 'CommandsController@notify_hr_employment_status');

        //Notification Center
        Route::get('/notification-center/{id}', 'NotificationsController@index');
        Route::post('/notification/read/true/{id}', 'HomeController@changeStatus')->name('status.read');

        Route::get('/home/{yearSelected?}', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/logout', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        //testing
        // Route::get('/testing', 'TimesheetController@calendar');
        // Route::post('/timesheet/entry/saves', 'TimesheetController@save_entries')->name('save.entries');

        //Timesheet
        //Editingg
        Route::get('/get-data/{year}/{month}/{id}', 'TimesheetController@getActivitiesEntry');
        Route::post('/update-entries/{id}', 'TimesheetController@updateActivitiesEntry')->name('entries.update');

        Route::get('/timesheet/{yearSelected?}', 'TimesheetController@index')->name('timesheet');
        Route::get('/timesheet/entry/{year}/{month}', 'TimesheetController@timesheet_entry');
        Route::post('/entries', 'TimesheetController@save_entries')->name('entries.store');

        Route::post('/save_entries/holiday', 'TimesheetController@save_entries_on_holiday')->name('entries.store.holiday');
        Route::post('/multiple_entries', 'TimesheetController@save_multiple_entries')->name('multiple.entries.store');
        Route::get('/get-activities/{year}/{month}', 'TimesheetController@getActivities')->name('activities.get-activities');
        //DELETE
        Route::delete('/activities/{id}', 'TimesheetController@destroy');
        Route::delete('/activities/all/{year}/{month}', 'TimesheetController@destroy_all');
        //Preview
        Route::get('/timesheet/entry/preview/{year}/{month}', 'TimesheetController@preview')->name('preview.timesheet');
        Route::get('/timesheet/entry/preview/print/{year}/{month}', 'TimesheetController@print');
        Route::get('/timesheet/entry/preview/surat_penugasan/download/{timesheet_id}/{id}', 'TimesheetController@download_surat');
        //submit
        Route::get('/timesheet/entry/submit/{year}/{month}', 'TimesheetController@submit_timesheet')->name('submit-timesheet');
        Route::get('/timesheet/entry/cancel_submit/{year}/{month}', 'TimesheetController@cancel_submit_timesheet')->name('submit-timesheet');
        //Review
        Route::get('/timesheet/review/fm', 'ReviewController@review')->name('review.finance');
        Route::get('/timesheet/review/fm/export/{month}/{year}', 'ExportTimesheet@export_excel');
        Route::get('/timesheet/review/fm/review/{user_id}/{year}/{month}', 'ReviewController@ts_preview')->name('preview.fm.timesheet');
        Route::get('/timesheet/review/fm/preview/print/{year}/{month}/{user_timesheet}', 'ReviewController@print_selected');
        Route::get('/timesheet/summary/preview/timesheet/{user_id}/{year}/{month}', 'ReviewController@ts_preview');

        Route::get('/timesheet/summary/all', 'TimesheetController@summary')->name('summary');
        Route::get('/timesheet/summary/remind/{id}/{year}/{month}', 'TimesheetController@remind')->name('remind');

        Route::get('/timesheet/user/preview/surat_penugasan/download/{timesheet_id}', 'TimesheetController@download_surat');
        Route::get('/retrieveRolesHO', 'ManagementController@retrieveRoles');

        // // Testing
        Route::get('/development', 'HomeController@notification_indev');
        // Route::get('/calendar/{year}/{month}', 'TimesheetController@showCalendar');
        // Route::put('/calendar/{year}/{month}/{day}', 'CalendarController@update')->name('calendar.update');
        // Route::get('/timesheet/entry/{id}', 'TimesheetController@timesheet_entry');
        // Route::post('/timesheet/entry/save-activities', 'TimesheetController@save')->name('save_activities');

        //Approval
        Route::get('/approval', 'ApprovalController@index')->name('approval.main');
        Route::get('/approval-history', 'ApprovalController@history')->name('approval.history');
        Route::get('/approval/timesheet/p', 'ApprovalController@timesheet_approval')->name('approval_primary');
        Route::get('/approval/leave/{yearSelected?}', 'LeaveApprovalController@leave_approval')->name('approval.leave');
        Route::get('/approval/reimburse/{yearSelected?}', 'ReimbursementApprovalController@reimbursement_approval')->name('approval.reimburse');
        Route::get('/approval/medical', 'ApprovalController@medical_approval')->name('approval.medical');

        Route::match(['get', 'post'], '/approval/leave/approve/{id}', 'LeaveApprovalController@approve')->name('leave.approve');
        Route::match(['get', 'post'], '/approval/leave/reject/{id}', 'LeaveApprovalController@reject')->name('leave.reject');

        Route::get('/approval/timesheet/preview/{user_id}/{year}/{month}', 'ApprovalController@ts_preview')->name('preview.timesheet');
        Route::get('/approval/timesheet/preview/print/{year}/{month}/{user_timesheet}', 'TimesheetController@print_selected');
        // Route::get('/reject/director/{user_id}/{year}/{month}', 'ApprovalController@reject_director')->name('reject-director');

        Route::match(['get', 'post'], '/approval/timesheet/approve/{user_id}/{year}/{month}', 'ApprovalController@approve');
        Route::match(['get', 'post'], '/approval/timesheet/reject/{user_id}/{year}/{month}', 'ApprovalController@reject');

        //Approval Project
        Route::get('/approval/project/assignment/', 'ApprovalProjectController@index')->name('approval.project');
        Route::get('/approval/project/assignment/preview/{id}', 'ApprovalProjectController@preview_assignment')->name('preview.project.assignment');
        Route::get('/approval/project/assignment/approve/{id}', 'ApprovalProjectController@approve_assignment')->name('approve.project.assignment');
        Route::get('/approval/project/assignment/reject/{id}', 'ApprovalProjectController@reject_assignment')->name('reject.project.assignment');

        //myprofile
        Route::get('/myprofile', 'MyProfileController@index')->name('myprofile');


        //leave
        Route::get('/leave/history/{yearSelected?}', 'LeaveController@history')->name('leave');
        Route::post('/leave/request/entry', 'LeaveController@leave_request_entry')->name('leave.entry');
        Route::get('/leave/request/details/{id}', 'LeaveController@leave_request_details')->name('leave_req_details')->where('id', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');
        Route::get('/leave/history/cancel/{id}', 'LeaveController@cancel_request')->name('cancel_leave');
        Route::get('/leave/request/manage/all', 'LeaveController@manage_request')->name('manage_leave_requests');
        Route::get('/leave/request/manage/id/{id}/{month}/{year}', 'LeaveController@emp_leave_request')->name('emp_requests');

        Route::get('/leave/manage/all', 'LeaveController@manage')->name('manage_leave');
        Route::get('/leave/manage/{id}', 'LeaveController@manage_leave_emp')->name('manage_leave_user');
        Route::get('/leave/manage/edit/{id}', 'LeaveController@get_leave_emp');
        Route::post('/leave/manage/update/{id}', 'LeaveController@update_leave_emp');
        Route::post('/leave/manage/add_quota/{id}', 'LeaveController@add_leave_quota')->name('addLeaveEmp');
        Route::post('/leave/manage/add_employee', 'LeaveController@add_leave_employee')->name('addLeaveEmployee');
        Route::get('/leave/manage/delete/{id}', 'LeaveController@delete_leave_emp');



        Route::get('/leave/request/manage/id/{id}/{month}/{year}/{idLeave}/approve', 'LeaveController@approve_by_admin');
        Route::get('/leave/request/manage/id/{id}/{month}/{year}/{idLeave}/reject', 'LeaveController@reject_by_admin');
        Route::get('/leave/request/manage/id/{idLeave}/delete', 'LeaveController@delete_by_admin');
        Route::put('/leave/request/manage/id/{idLeave}/update', 'LeaveController@update_by_admin');
        Route::get('/leave/request/manage/id/{id}/{month}/{year}/{idLeave}', 'LeaveController@get_leave_request_data');

        //Project Assignment
        Route::get('/myprojects', 'ProjectController@index')->name('myproject');
        Route::get('/assignment/{yearSelected?}', 'ProjectController@assigning')->name('project-assigning');
        Route::post('/assignment/add_entries', 'ProjectController@add_project_assignment')->name('add_projects');
        Route::get('/assignment/member/{id}', 'ProjectController@project_assignment_member')->name('project-assigning');
        Route::post('/assignment/add_member_to_assignment/{assignment_id}', 'ProjectController@add_project_member')->name('add_projects');
        Route::get('/project_list', 'ProjectController@project_list')->name('project-list');
        Route::get('/assignment/view/details/{id}', 'ProjectController@project_assignment_member_view')->name('project-assigning-view');
        Route::get('/assignment/member/delete/{id}', 'ProjectController@project_assignment_member_delete')->name('project-assigning-delete');

        Route::get('/project_list/delete/assignment/member/{id}/{project_assignment_id}', 'ProjectController@project_assignment_member_delete_two')->name('project-assigning-delete-two');

        Route::delete('/assignment/delete/{id}', 'ProjectController@project_assignment_delete')->name('project-assignment-delete');
        Route::post('/client/create', 'ProjectController@create_new_client')->name('insert.new.client');
        Route::get('/retrieveClients', 'ProjectController@getClientsRows')->name('client-list');
        Route::delete('/project_list/delete/client/{id}', 'ProjectController@delete_client')->name('client-delete');

        Route::post('/project_list/new', 'ProjectController@create_new_project')->name('project-list-create');
        Route::get('/retrieveLocations', 'ProjectController@listLocations')->name('list-location');
        Route::post('/location/create', 'ProjectController@create_new_location')->name('insert.new.location');
        Route::delete('/project_list/delete/location/{id}', 'ProjectController@delete_location')->name('location-delete');
        Route::get('/project_list/view/details/{id}', 'ProjectController@company_project_view')->name('company-project-view');
        Route::delete('/project_list/delete/{id}', 'ProjectController@project_delete')->name('project-delete');
        Route::get('/retrieveProjectRoles', 'ProjectController@listProjectRoles')->name('list-project-roles');
        Route::post('/projectRole/create', 'ProjectController@create_new_project_roles')->name('insert.new.role');
        Route::delete('/project_list/delete/project_role/{id}', 'ProjectController@delete_project_role')->name('role-delete');

        Route::get('/retrieveProjectData/{id}', 'ProjectController@retrieveProjectData');
        Route::put('/project_list/edit/save/{project}', 'ProjectController@updateProjectData');
        Route::get('/retrieveUsrPeriodData/{id}', 'ProjectController@retrieveUsrPeriodData');
        Route::put('/project_list/edit/usr/save/{usr_id}', 'ProjectController@updateUserPeriod');

        Route::get('/assignment/requested/by/user', 'ProjectController@requested_assignment')->name('myproject');
        Route::post('/assignment/request', 'ProjectController@requested_assignment_entry')->name('req.ass');
        Route::get('/assignment/requested/by/user/view/{id}', 'ProjectController@requested_assignment_view');
        Route::get('/assignment/requested/by/user/approve/{id}', 'ProjectController@requested_assignment_approve');
        Route::get('/assignment/requested/by/user/reject/{id}', 'ProjectController@requested_assignment_reject');
        Route::post('/assignment/add_entries/based_on/request/{id}', 'ProjectController@add_project_assignment_from_request');

        //manage users
        Route::get('/manage/users', 'UserController@index')->middleware(['checkRole:admin,manager']);
        Route::get('/manage/users/new-user-registration', 'UserController@user_creation')->middleware(['checkRole:admin,manager']);
        Route::post('/users/store', 'UserController@store')->middleware(['checkRole:admin,manager']);
        Route::get('/users/edit/{id}', 'UserController@edit');
        Route::put('/users/update/{id}', 'UserController@update');
        Route::get('/users/hapus/{id}', 'UserController@delete');
        Route::post(
            '/check-user-id',
            function () {
                $userId = request('usr_id');
                $user = App\Models\User::where('id', $userId)->first();

                if ($user) {
                    return response()->json(['exists' => true]);
                } else {
                    return response()->json(['exists' => false]);
                }
            }
        );


        //Management
        Route::get('/management/security_&_roles/', 'ManagementController@roles');
        Route::get('/management/security_&_roles/manage/access', 'ManagementController@manage_access')->middleware(['checkRole:admin,manager']);
        Route::get('/management/security_&_roles/manage/roles', 'ManagementController@manage_roles');
        //link non-page
        Route::get('/management/security_&_roles/remove/roles/{id}', 'ManagementController@remove_roles_from_user');
        Route::get('/management/security_&_roles/remove/access/{id}', 'ManagementController@remove_access');

        Route::post('/manage/roles/assign_roles', 'ManagementController@assign_roles');
        Route::match(['get', 'post'], '/management/security_&_roles/add/access/', 'ManagementController@grant_access_to_roles');

        Route::post('/manage/roles/add_roles', 'ManagementController@add_roles');
        Route::get('/manage/roles/delete/{id}', 'ManagementController@delete_roles');

        //Employees Database
        Route::get('/manage/list/employees', 'EmployeesDatabase@index')->name('emp.database');
        Route::get('/manage/list/export-users', 'EmployeesDatabase@exportData')->name('export.users');

        // API KEY Setting
        Route::get('/manage/api_key', 'ManagementController@index_api')->name('API_KEY_setting');
        Route::post('/manage/api_key/add_api', 'ManagementController@add_api')->name('API_KEY_setting');
        Route::put('/manage/api_key/update/{id}', 'ManagementController@update_api')->name('API_KEY_update');

        //Company Regulation
        Route::get('/hrtools/manage/edit/{id}', 'ManagementController@delete');
        Route::get('/hr/compliance/', 'HrController@index');
        Route::post('/post-data/to/fingerprint/machine', 'HrController@add_user_fingerprint');
        Route::get('/hr/compliance/integration/delete/{id}', 'HrController@delete_user_fingerprint');

        Route::get('/hr/compliance/timesheet/settings', 'HrController@timesheet');
        Route::put('/hr/compliance/timesheet/settings/save', 'HrController@timesheet_settings_save');
        Route::put("/hr/compliance/update/regulations", 'HrController@update_regulation');
        Route::put("/hr/compliance/update/cutoff-date", 'HrController@update_cutoffdate');
        // Exit Clearance
        Route::get('/hr/exit_clearance/', 'HrController@exit_clear');
        Route::get('/hr/exit_clearance/print/{id}', 'HrController@print');
        Route::put('/hr/exit_clearance/resign_emp', 'HrController@resign_emp');

        //Department and Position
        Route::get('/hrtools/manage/position', 'DepPosController@index');
        Route::post('/manage/add_department', 'DepPosController@add_department');
        Route::get('/manage/delete_department/{id}', 'DepPosController@delete_department');
        Route::post('/manage/add_position', 'DepPosController@add_position');
        Route::get('/manage/delete_position/{id}', 'DepPosController@delete_position');

        ///Mailer
        // Route::get('/kirimemail','MailerController@index');

        //medical reimburse
        Route::get('/medical/history/{yearSelected?}', 'MedicalController@index')->middleware('auth');
        Route::get('/medical/entry', 'MedicalController@entry')->middleware('auth');

        Route::get('/medical/edit/{id}', 'MedicalController@edit')->middleware('auth');
        Route::get('/medical/edit/{id}/download', 'MedicalController@download')->middleware('auth');
        Route::put('/medical/edit/{id}/resubmit', 'MedicalController@resubmit')->middleware('auth');
        Route::get('/medical/delete/{id}', 'MedicalController@delete_med_all')->middleware('auth');
        Route::put('/medical/edit/{id}/update/{mdet_id}', 'MedicalController@update_medDetail')->middleware('auth');
        Route::get('/medical/edit/{id}/delete/{mdet_id}', 'MedicalController@delete_medDetail')->middleware('auth');

        //medical manage
        Route::get('/medical/manage', 'MedicalController@index_manage')->middleware('auth');
        Route::post('/medical/manage/add_balance', 'MedicalController@add_balance')->middleware('auth');
        Route::put('/medical/manage/edit_balance/{id}', 'MedicalController@edit_balance')->middleware('auth');

        // medical review FM
        Route::get('/medical/review', 'MedicalController@review_fm')->middleware('auth');
        Route::get('/medical/review/view/{id}', 'MedicalController@detail_review')->middleware('auth');
        Route::put('/medical/review/{id}', 'MedicalController@paid')->middleware('auth');

        //medical approval
        Route::get('/medical/approval/{id}', 'ApprovalController@approval_edit')->middleware('auth');
        Route::put('/medical/approval/{id}/update/{mdet_id}', 'ApprovalController@update_approval')->middleware('auth');
        Route::put('/medical/approval/{id}/approve', 'ApprovalController@approve_medical')->middleware('auth');
        Route::put('/medical/approval/{id}/reject', 'ApprovalController@reject_medical')->middleware('auth');

        Route::get('/reimbursement/history/{yearSelected?}', 'ReimburseController@history')->name('reimburse-history');
        Route::get('/reimbursement/create/request', 'ReimburseController@create_request')->name('reimburse-new-req');
        Route::get('/reimbursement/view/{id}', 'ReimburseController@view_details')->name('reimburse-view-req');
        Route::get('/retrieveReimburseData/{id}', 'ReimburseController@retrieveReimburseData');
        Route::get('/retrieveReimburseDataApproval/{id}', 'ReimburseController@retrieveReimburseDataApproval');
        Route::post('/reimbursement/edit/save/{usr_id}', 'ReimburseController@updateReimburseData');

        Route::get('/reimbursement/finance/confirm-receivable/{id}', 'ReimburseController@confirmReceivableReceipt');
        Route::post('/reimbursement/finance/approve/{item_id}', 'ReimburseController@approveReimburseDataFinance');
        Route::post('/reimbursement/finance/reject/{item_id}', 'ReimburseController@rejectReimburseDataFinance');
        Route::delete('reimbursement/history/cancel/{id}', 'ReimburseController@cancel_request')->name('cancel_reimburse');
        Route::get('reimbursement/view/preview/{id}', 'ReimburseController@previewPdf')->name('pdf.preview');
        Route::get('/download-receipt/reimbursement/{id}', 'ReimburseController@downloadReceipt');

        Route::match(['get', 'post'], '/approval/reimburse/view/approve/{id}', 'ReimbursementApprovalController@approve')->name('reimburse.approve');
        Route::match(['get', 'post'], '/approval/reimburse/view/reject/{id}', 'ReimbursementApprovalController@reject')->name('reimburse.reject');
        Route::get('/approval/reimburse/view/{id}', 'ReimbursementApprovalController@view_details')->name('reimburse.approval-view-req');

        Route::get('/retrieveApproverList/{id}', 'ReimbursementApprovalController@listApprover')->name('list-approver');
        Route::get('/retrieveApproverHistory/{id}', 'ReimbursementApprovalController@approvalFlow');

        Route::get('/reimbursement/manage/', 'ReimburseController@manage')->name('manage.reimbursement');
        Route::post('/reimbursement/export/selected-items', 'ReimburseController@export_selected');
        Route::get('/reimbursement/export/request/{id}', 'ReimburseController@export');

        Route::get('/getLocationProject/{id}', 'TimesheetController@getLocationProject')->name('list-location');

        //vendor List
        Route::get('/vendor-list', 'VendorController@index')->name('index-vendor');
        Route::post('/vendor-list/new-entry', 'VendorController@new_entry')->name('add_vendor');
        Route::get('/vendor-list/item/delete/{id}', 'VendorController@delete');


        //News Feed
        Route::get('/news-feed/manage', 'NewsController@index')->name('manage-news');
        Route::get('/news-feed/manage/create', 'NewsController@create')->name('create-news');
        Route::post('/news-feed/store', 'NewsController@store')->name('news-feed.store');
        Route::get('/news-feed/get-id-pic/{id}', 'NewsController@get_id_headline')->name('get_id_headline');
        Route::post('/news-feed/update-headline/{id}', 'NewsController@updateHeadlineData');

        Route::get('/news-feed/manage/edit-post/{id}', 'NewsController@edit_post')->name('edit_post');
        Route::post('/news-feed/manage/update-post/{id}', 'NewsController@update_post')->name('news-feed.update');
        Route::get('/news-feed/manage/delete-post/{id}', 'NewsController@delete_post')->name('delete_post');



        Route::post('/reimbursement/manage/disbursed/all', 'ReimburseController@disbursed_all');
        Route::get('/reimbursement/manage/disbursed-item/{formId}', 'ReimburseController@disbursed_item');
        Route::get('/reimbursement/manage/view/{id}', 'ReimburseController@manage_view_details');
        Route::get('/reimbursement/create_order_letter/{id}', 'ReimburseController@create_order_letter');

        //Execution
        Route::get('/company-regulation/commands', 'CommandsController@index')->name('commands');
        Route::get('/get-data/timesheet-absence', 'AttendanceController@downloadLogData')->name('getData');
        Route::get('/send-data/timesheet-absence', 'AttendanceController@sendData')->name('sendData');
        Route::put('/myprofile/cv_upload/{id}', 'MyProfileController@upload_cv')->name('Upload CV');
    });
    //Non
    Route::post('/medical/entry/store', 'MedicalController@store')->middleware('auth');
    Route::post('/reimbursement/create/submit', 'ReimburseController@submit_request')->name('reimburse-submission');
});
