<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utilities\AttendanceUtility;
use App\Models\Timesheet;
use App\Models\TimesheetModificationRequest;
use App\Utilities\CustomLogUtility;
use Auth;
use Exception;

class SupervisorController extends Controller
{
    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

    public function attendanceSummary() {
        return view('user.advance.supervisor.department-attendance');
    }

    /**
     * @return formatted_data array [date: {present: int, late: int, absent: int, on-leave: int}, ...]
     */
    public function getMonthAttendanceSummaryData($start_date, $end_date)
    {
        $validator = Validator::make([
            'start_date' => $start_date, 
            'end_date' => $end_date
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }
        $department = [Auth::user()->employee->department];
        $formatted_data = AttendanceUtility::getMonthAttendanceSummaryData($start_date, $end_date, $department);
        return response()->json($formatted_data);
    }

    /**
     * @param date string format YYYY-MM-DD
     */
    public function getAttendanceSummaryOnDate($date)
    {
        $validator = Validator::make([
            'date' => $date, 
        ], [
            'date' => ['required', 'date_format:Y-m-d'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }
        $department = [Auth::user()->employee->department];
        $formatted_data = AttendanceUtility::getAttendanceSummaryOnDate($date, $department);
        return view('user.advance.supervisor.components.department-attendance-date-summary', compact('formatted_data', 'date'));
    }

    public function getPresentEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_PRESENT;
        $department = [Auth::user()->employee->department];
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $department);
    }

    public function getLateEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_LATE;
        $department = [Auth::user()->employee->department];
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $department);
    }

    public function getOnLeaveEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_ON_LEAVE;
        $department = [Auth::user()->employee->department];
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $department);
    }

    public function getAbsentEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_ABSENT;
        $department = [Auth::user()->employee->department];
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $department);
    }

    public static function getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $departments)
    {
        $validator = Validator::make([
            'date' => $date, 
        ], [
            'date' => ['required', 'date_format:Y-m-d'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        } 
        $formatted_data = Employee::getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $departments);
        return view('user.advance.supervisor.components.department-attendance-type-summary', compact('formatted_data', 'date', 'attendance_type'));
    }
    
    public function getAttendanceSummaryOnWeek($start_date, $end_date)
    {
        $validator = Validator::make([
            'start_date' => $start_date, 
            'end_date' => $end_date
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }
        $department = [Auth::user()->employee->department];
        $formatted_data = AttendanceUtility::getAttendanceSummary($start_date, $end_date, $department);
        return view('user.advance.supervisor.components.department-attendance-week-summary', compact('formatted_data', 'start_date', 'end_date'));
    }

    public function getTotalAttendanceTypePerEmployeeOnWeek($start_date, $end_date)
    {
        $validator = Validator::make([
            'start_date' => $start_date, 
            'end_date' => $end_date
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }
        $department = [Auth::user()->employee->department];
        $formatted_data = AttendanceUtility::getTotalAttendanceTypePerEmployee($start_date, $end_date, $department);
        return view('user.advance.supervisor.components.department-attendance-total-per-employee-summary', compact('formatted_data', 'start_date', 'end_date'));
    }

    public function getTotalAttendanceTypePerDayOnWeek($start_date, $end_date)
    {
        $validator = Validator::make([
            'start_date' => $start_date, 
            'end_date' => $end_date
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        }
        $department = [Auth::user()->employee->department];
        $formatted_data = AttendanceUtility::getTotalAttendanceTypePerDay($start_date, $end_date, $department);
        return view('user.advance.supervisor.components.department-attendance-total-per-day-summary', compact('formatted_data', 'start_date', 'end_date'));
    }

    /**
     * Get leave requests by department
     * Validates whether user is approver|supervisor of that department
     */
    public function departmentLeaveRequests($department_id) {
        try {
            $department = Department::find($department_id);
            if (empty($department)) throw new Exception('Bad Request!');
            $usr_email = Auth::user()->email;
            $not_approver = $usr_email !== $department->approver;
            $not_supervisor = $usr_email !== $department->supervisor;
            if ($not_approver && $not_supervisor) throw new Exception('Bad Request!');
            $department_leave_requests = LeaveRequest::getByDeparment($department_id);
            return view('user.advance.supervisor.department-leave-requests', compact('department_leave_requests', 'department'));
        } catch (Exception $e) {
            CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
            return redirect(route('advance'))->with(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Get timesheet adjustments by department
     * Validates whether user is approver|supervisor of that department
     */
    public function departmentTimesheetAdjustments($department_id)
    {
        try {
            $department = Department::find($department_id);
            if (empty($department)) throw new Exception('Bad Request!');
            $usr_email = Auth::user()->email;
            $not_approver = $usr_email !== $department->approver;
            $not_supervisor = $usr_email !== $department->supervisor;
            if ($not_approver && $not_supervisor) throw new Exception('Bad Request!');
            $department_timesheet_adjustments = TimesheetModificationRequest::getByDeparment($department_id);
            return view('user.advance.supervisor.department-timesheet-adjustments', compact('department_timesheet_adjustments', 'department'));
        } catch (Exception $e) {
            CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
            return redirect(route('advance'))->with(['error' => $e->getMessage()]);
        }
    }
}
