<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\AdvanceController;

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

require __DIR__.'/auth.php';

// Auth::routes();

// Home
Route::get('/', function () {
    return view('bundy_homepage');
});

// PUBLIC FACING
Route::group(['middleware' => ['auth', 'original_host_only']], function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bundy', [TimesheetController::class, 'bundy'])->name('bundy');
    Route::get('/timesheets', [TimesheetController::class, 'index'])->name('timesheets');
    Route::group(['prefix' => 'leave-request'], function() {
        Route::get('/add', [LeaveRequestController::class, 'index'])->name('add_leave_request');
        // leave Request List
        Route::get('/list', [LeaveRequestController::class,'showAll'])->name('all_leave_requests');
        Route::get('/approved', [LeaveRequestController::class,'showApproved'])->name('approved_leave_requests');
        Route::get('/pending', [LeaveRequestController::class,'showPending'])->name('pending_leave_requests');
        Route::get('/cancelled', [LeaveRequestController::class,'showCancelled'])->name('cancelled_leave_requests');
        Route::get('/rejected', [LeaveRequestController::class,'showRejected'])->name('rejected_leave_requests');
        //modal
        Route::get('/{id}', [LeaveRequestController::class,'modal_view']);
        Route::get('/approved/{id}', [LeaveRequestController::class,'modal_view']);
        Route::get('/pending/{id}', [LeaveRequestController::class,'modal_view']);
        Route::get('/rejected/{id}', [LeaveRequestController::class,'modal_view']);
        Route::get('/cancelled/{id}', [LeaveRequestController::class,'modal_view']);
    });

    // @remind ON PROGRESS - being used as reference for now, can be cleared soon
    Route::group(['prefix' => 'face-recognition'], function() {
        Route::get('/clock', [FaceRecognitionController::class, 'index']);
        Route::get('/register', [FaceRecognitionController::class, 'register']);
    }); 

    Route::group(['prefix' => 'advance'], function() {
        Route::get('/', [AdvanceController::class, 'index'])->name('advance');
        Route::group(['prefix' => 'face-recognition'], function() {
            Route::get('/clock', [AdvanceController::class, 'clockFace'])->name('face-recognition.clock');
            Route::get('/register', [AdvanceController::class, 'registerFace'])->name('face-recognition.register');
        });
        Route::get('punch-clock/authorize', [AdvanceController::class, 'authorizePunchClockInstance'])->name('punch_clock.authorize');
        Route::get('punch-clock/access', [AdvanceController::class, 'accessPunchClockInstance'])->name('punch_clock.access');

        Route::group(['middleware' => ['supervisor'], 'prefix' => 'supervisor'], function() {
            Route::get('/department/{department_id}/leave-requests', [SupervisorController::class,'departmentLeaveRequests'])->name('advance.supervisor.department_leave_requests');
            Route::get('/department-attendance', [SupervisorController::class, 'attendanceSummary'])->name('advance.supervisor.department_attendance_summary');
            Route::get('/department/{department_id}/timesheet-adjustments', [SupervisorController::class,'departmentTimesheetAdjustments'])->name('advance.supervisor.department_timesheet_adjustments');
        });
    });
});

// Google
Route::get('auth/redirect', [SocialController::class, 'redirect']);
Route::get('auth/callback', [SocialController::class, 'callback']);

// Admin
Route::group(['middleware' => ['original_host_only', 'role:' . App\Models\User::ROLE_ADMIN], 'prefix' => 'admin'], function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin_dashboard');
    Route::get('/attendance-modifications', [AdminController::class, 'timesheetModifications'])->name('admin_timesheet_modifications');
    Route::get('/attendance-today', [AdminController::class, 'attendanceToday'])->name('admin_attendance_today');
    Route::get('/leave-requests', [AdminController::class, 'leaveRequests'])->name('admin_leave_requests');
    Route::get('/approvers', [AdminController::class, 'approvers'])->name('admin_approvers');
    Route::get('/holidays', [AdminController::class, 'holidays'])->name('admin_holidays');
    // attedance report
    Route::get('/attendance-reports', [AdminController::class, 'attendanceReport'])->name('admin_attendance_reports');
    Route::post('/generate-attendance-report', [AdminController::class, 'generateAttendanceReport']);
    Route::get('/attendance-report/start-date/{startDate}/end-date/{endDate}/pdf', [AdminController::class, 'createAttendanceReportPDF'])->name('admin_attendance_report_pdf');
    // attedance summary
    Route::get('/attendance-summary', [AdminController::class, 'attendanceSummary'])->name('admin_attendance_summary');
    Route::get('/month-attendance-summary/{start_date}/{end_date}', [AdminController::class, 'getMonthAttendanceSummaryData'])->name('admin_month_attendance_summary');
    Route::get('/date-attendance-summary/{date}', [AdminController::class, 'getAttendanceSummaryOnDate'])->name('admin_date_attendance_summary');
    Route::get('/present-employees-summary/{date}', [AdminController::class, 'getPresentEmployeeNamesOnDate']);
    Route::get('/late-employees-summary/{date}', [AdminController::class, 'getLateEmployeeNamesOnDate']);
    Route::get('/on-leave-employees-summary/{date}', [AdminController::class, 'getOnLeaveEmployeeNamesOnDate']);
    Route::get('/absent-employees-summary/{date}', [AdminController::class, 'getAbsentEmployeeNamesOnDate']);
    Route::get('/week-attendance-summary/{start_date}/{end_date}', [AdminController::class, 'getAttendanceSummaryOnWeek']);
    Route::get('/total-per-employee-attendance-summary/{start_date}/{end_date}', [AdminController::class, 'getTotalAttendanceTypePerEmployeeOnWeek']);
    Route::get('/total-per-day-attendance-summary/{start_date}/{end_date}', [AdminController::class, 'getTotalAttendanceTypePerDayOnWeek']);
    // managements
    Route::get('/timesheet-management/{employee_id}', [AdminController::class, 'timesheetManagement'])->name('admin_timesheet_management');
    Route::get('/schedule-management/{employee_id}', [AdminController::class, 'scheduleManagement'])->name('admin_employee_schedule_management');
    Route::get('/academic-year-management', [AdminController::class, 'academicYearManagement'])->name('admin_academic_year_management');
    Route::get('/department-management', [AdminController::class, 'departmentManagement'])->name('admin_department_management');
    Route::get('/employee-management', [AdminController::class, 'employeeManagement'])->name('admin_employee_management');
    Route::get('/advance-clock-management', [AdminController::class, 'advancePunchClockManagement'])->name('admin_advance_punch_clock_management');
    // leave report
    Route::get('/leave-report', [AdminController::class, 'leaveReport'])->name('admin_leave_report');
    Route::post('/generate-leave-report', [AdminController::class, 'generateLeaveReport']);
});

// HELP
Route::get('/help',function(){
    return view('help');
});

// SUPPORT
Route::get('/support/{employee_email}', [LoginController::class, 'impersonate']);
