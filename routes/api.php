<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdvancePunchClockController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FaceRecognitionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication for mobile app
Route::post('/app/login', [SocialController::class, 'appLogin']);
Route::post('/google/login', [SocialController::class, 'googleLogin']);
Route::post('/validate/token', [SocialController::class, 'validateToken']);

Route::middleware(['validate_token'])->group(function() {

    // Leave Requests
    Route::prefix('leave')->group(function() {
        Route::get('/types', 'App\Http\Controllers\LeaveRequestController@getLeaveTypes');
        Route::get('/approvers', 'App\Http\Controllers\LeaveRequestController@getLeaveApprovers');
        Route::get('/requests', 'App\Http\Controllers\LeaveRequestController@getLeaveRequests');
        Route::get('/request/{id}', 'App\Http\Controllers\LeaveRequestController@getLeaveRequest');
        Route::get('/request/leave-pending', 'App\Http\Controllers\LeaveRequestController@showPending');
        Route::get('/request/{id}/evaluate/{option?}', 'App\Http\Controllers\LeaveRequestController@evaluate');
        Route::get('/request/{id}', 'App\Http\Controllers\LeaveRequestController@showAllById');
        Route::post('/request/approve', 'App\Http\Controllers\LeaveRequestController@approveLeaveRequest');
        Route::post('/request/reject', 'App\Http\Controllers\LeaveRequestController@rejectLeaveRequest');
        Route::post('/request/cancel', 'App\Http\Controllers\LeaveRequestController@cancelLeaveRequest');
        Route::post('/request/submit', 'App\Http\Controllers\LeaveRequestController@submitLeaveRequest');
    });

    Route::prefix('timesheet')->group(function() {
        Route::get('/', [TimesheetController::class, 'getEmployeeTimesheet']);
        Route::post('/', [TimesheetController::class, 'updateTimesheet']);
        Route::post('/punch', [TimesheetController::class, 'punch']);
        Route::post('/punch-by-ids', [TimesheetController::class, 'punchByIds']);
        Route::get('/modification/{request_id}', [TimesheetController::class, 'getTimesheetModificationRequest']);
        Route::get('/modification/{request_id}/compare/{option?}', [TimesheetController::class, 'compareChangesToOriginal']);
        Route::post('/modification/approve', [TimesheetController::class, 'approveTimesheetModificationRequest']);
        Route::post('/modification/reject', [TimesheetController::class, 'rejectTimesheetModificationRequest']);
        Route::post('/modification/cancel', [TimesheetController::class, 'cancelTimesheetModificationRequest']);
        Route::get('/schedule/{schedule_id}', [TimesheetController::class, 'getTimesheetByScheduleId']);
        Route::get('/{timesheet_id}', [TimesheetController::class, 'getTimesheet']);
        Route::get('/employee/clocks', [TimesheetController::class, 'getEmployeeTimesheets']);
        Route::get('/employee/clocks/mobile', [TimesheetController::class, 'getEmployeeTimesheetsMobile']);
        Route::get('/employee/modifications', [TimesheetController::class, 'getEmployeeTimesheetModificationRequests']);
        Route::post('/modification', [TimesheetController::class, 'modifyTimesheet']);
        Route::post('/create', [TimesheetController::class, 'create']);
        Route::post('/update', [TimesheetController::class, 'update']);
        Route::post('/delete', [TimesheetController::class, 'delete']);
        Route::post('/create-for-employees', [TimesheetController::class, 'createForEmployees']);
    });

    Route::prefix('employee')->group(function() {
        Route::post('/create', [EmployeeController::class, 'create']);
        Route::post('/update', [EmployeeController::class, 'update']);
        Route::post('/delete', [EmployeeController::class, 'delete']);
    });

    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleAndPermissionController::class, 'roles']);
        Route::post('/user/{user}', [RoleAndPermissionController::class, 'manageRole']);
    });

    Route::prefix('permissions')->group(function() {
        Route::get('/', [RoleAndPermissionController::class, 'permissions']);
        Route::post('/user/{user}', [RoleAndPermissionController::class, 'userPermission']);
        Route::post('/role/{role}', [RoleAndPermissionController::class, 'rolePermission']);
    });

    Route::prefix('supervisor')->group(function() {
        Route::get('/month-attendance-summary/{start_date}/{end_date}', [SupervisorController::class, 'getMonthAttendanceSummaryData']);
        Route::get('/date-attendance-summary/{date}', [SupervisorController::class, 'getAttendanceSummaryOnDate']);
        Route::get('/present-employees-summary/{date}', [SupervisorController::class, 'getPresentEmployeeNamesOnDate']);
        Route::get('/late-employees-summary/{date}', [SupervisorController::class, 'getLateEmployeeNamesOnDate']);
        Route::get('/on-leave-employees-summary/{date}', [SupervisorController::class, 'getOnLeaveEmployeeNamesOnDate']);
        Route::get('/absent-employees-summary/{date}', [SupervisorController::class, 'getAbsentEmployeeNamesOnDate']);
        Route::get('/week-attendance-summary/{start_date}/{end_date}', [SupervisorController::class, 'getAttendanceSummaryOnWeek']);
        Route::get('/total-per-employee-attendance-summary/{start_date}/{end_date}', [SupervisorController::class, 'getTotalAttendanceTypePerEmployeeOnWeek']);
        Route::get('/total-per-day-attendance-summary/{start_date}/{end_date}', [SupervisorController::class, 'getTotalAttendanceTypePerDayOnWeek']);
    });

    Route::prefix('schedule')->group(function() {
        Route::post('/create', [ScheduleController::class, 'create']);
        Route::post('/update', [ScheduleController::class, 'update']);
        Route::post('/delete', [ScheduleController::class, 'delete']);
    });

    Route::prefix('academic-year')->group(function() {
        Route::post('/create', [AcademicYearController::class, 'create']);
        Route::post('/update', [AcademicYearController::class, 'update']);
        Route::post('/delete', [AcademicYearController::class, 'delete']);
    });

    Route::prefix('department')->group(function() {
        Route::post('/create', [DepartmentController::class, 'create']);
        Route::post('/update', [DepartmentController::class, 'update']);
        Route::post('/delete', [DepartmentController::class, 'delete']);
    });

    Route::prefix('face-recognition')->group(function() {
        Route::post('/save-image', [FaceRecognitionController::class, 'saveUserImage']);
        Route::get('/labeled-images', [FaceRecognitionController::class, 'getLabeledImages']);
    });

    Route::prefix('advance-punch-clock')->group(function() {
        Route::post('/create', [AdvancePunchClockController::class, 'create']);
        Route::post('/update', [AdvancePunchClockController::class, 'update']);
        Route::post('/delete', [AdvancePunchClockController::class, 'delete']);
    });

    // http://localhost:8000/api/new/request/pending
    Route::get('/new/request/pending', 'App\Http\Controllers\NewController@showPending');
    // http://localhost:8000/api/new/request/approved
    Route::get('/new/request/approved', 'App\Http\Controllers\NewController@showApproved');
    // http://localhost:8000/api/new/users
     Route::get('/new/request/cancelled', 'App\Http\Controllers\NewController@showCancelled');
    // http://localhost:8000/api/new/users
    Route::get('/new/users', 'App\Http\Controllers\NewController@getAllUsers');

    //Time Start

    // http://localhost:8000/api/new/updateTimeIn/2
    Route::put('/new/updateTimeIn/{id}', 'App\Http\Controllers\NewController@updateTimeIn');
    // http://localhost:8000/api/new/updateTimeOut/4
    Route::put('/new/updateTimeOut/{id}', 'App\Http\Controllers\NewController@updateTimeOut');
    //http://localhost:8000/api/new/updateLunchStart/4
    Route::put('/new/updateLunchStart/{id}', 'App\Http\Controllers\NewController@updateLunchStart');
    // http://localhost:8000/api/new/updateLunchEnd/4
    Route::put('/new/updateLunchEnd/{id}', 'App\Http\Controllers\NewController@updateLunchEnd');
    //http://localhost:8000/api/new/updateOvertimeStart/4
    Route::put('/new/updateOvertimeStart/{id}', 'App\Http\Controllers\NewController@updateOvertimeStart');
    // http://localhost:8000/api/new/updateovertimeOut/4
    Route::put('/new/updateOvertimeEnd/{id}', 'App\Http\Controllers\NewController@updateOvertimeEnd');
    
    // http://localhost:8000/api/new/getTimesheetByUserId/4
    Route::get('/new/getTimesheetByUserId/{id}', 'App\Http\Controllers\NewController@getTimesheetByUserId');

    // http://localhost:8000/api/new/getTimeInCount/4
    Route::get('/new/getTimesheetsByUserId/{id}', 'App\Http\Controllers\NewController@getTimesheetsByUserId');

    Route::get('/new/getTimeInCount', 'App\Http\Controllers\NewController@getTimeInCount');

    
});
