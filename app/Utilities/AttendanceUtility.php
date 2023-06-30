<?php

namespace App\Utilities;

use App\Models\Timesheet;
use App\Models\Department;

class AttendanceUtility
{
    public static function getMonthAttendanceSummaryData($start_date, $end_date, $departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_data = [];
        $timesheets = Department::getTimesheetsByDateRange($departments, $start_date, $end_date);
        foreach ($timesheets as $key => $timesheet) {
            $timesheet_date = $timesheet->timesheet_date;
            $attendance = $timesheet->getAttendanceType();
            if ($attendance != Timesheet::TIMESHEET_INACTIVE) {
                if (
                    !array_key_exists($timesheet_date, $formatted_data) ||
                    !array_key_exists($attendance, $formatted_data[$timesheet_date])
                ) {
                    $formatted_data[$timesheet_date][$attendance] = 1;
                } else {
                    $formatted_data[$timesheet_date][$attendance] += 1;
                }
            }
        }
        return $formatted_data;
    }

    public static function getAttendanceSummary($start_date, $end_date, $departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_data = [];
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $formatted_data[$department_name] = [];
            $employees = $department->employees;
            foreach ($employees as $key => $employee) {
                $employee_name = $employee->getFullName();
                $timesheets = $employee->timesheets
                                ->where('timesheet_date', '>=', $start_date)
                                ->where('timesheet_date', '<=', $end_date);
                $week_attendance = (object)[];
                foreach ($timesheets as $key => $timesheet) {
                    $timesheet_date = $timesheet->timesheet_date;
                    $day_name = strtolower(date('l', strtotime($timesheet_date)));
                    $attendance = $timesheet->getAttendanceType();
                    $attendance_color = $timesheet->getAttendanceColor();
                    if ($attendance == Timesheet::TIMESHEET_INACTIVE) {
                        $attendance = 'N/A';
                    }
                    $week_attendance->{$day_name} = (object)[
                        'attendance' => $attendance,
                        'attendance_color' => $attendance_color,
                    ];
                }
                $formatted_data[$department_name][$employee_name] = $week_attendance;
            }
        }
        return $formatted_data;
    }

    public static function getAttendanceSummaryOnDate($date, $departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_data = [];
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $formatted_data[$department_name] = [
                Timesheet::EMPLOYEE_PRESENT => [],
                Timesheet::EMPLOYEE_LATE => [],
                Timesheet::EMPLOYEE_ON_LEAVE => [],
                Timesheet::EMPLOYEE_ABSENT => [],
                Timesheet::TIMESHEET_INACTIVE => [],
            ];  
            $employees = $department->employees;
            foreach ($employees as $key => $employee) {
                $timesheet = $employee->timesheets->where('timesheet_date', $date)->first();
                if(!is_null($timesheet)) {
                    $attendance = $timesheet->getAttendanceType();
                    array_push($formatted_data[$department_name][$attendance], $employee->getFullName());
                }
            }
        }
        return $formatted_data;
    }

    public static function getTotalAttendanceTypePerDay($start_date, $end_date, $departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_data = [];
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $formatted_data[$department_name] = [];
            $employees = $department->employees;
            $week_attendance = (object)[
                Timesheet::EMPLOYEE_PRESENT => (object)[],
                Timesheet::EMPLOYEE_LATE => (object)[],
                Timesheet::EMPLOYEE_ON_LEAVE => (object)[],
                Timesheet::EMPLOYEE_ABSENT => (object)[],
            ];
            foreach ($employees as $key => $employee) {
                $timesheets = $employee->timesheets
                                ->where('timesheet_date', '>=', $start_date)
                                ->where('timesheet_date', '<=', $end_date);
                foreach ($timesheets as $key => $timesheet) {
                    $timesheet_date = $timesheet->timesheet_date;
                    $day_name = strtolower(date('l', strtotime($timesheet_date)));
                    $attendance = $timesheet->getAttendanceType();
                    $attendance_color = $timesheet->getAttendanceColor();
                    if ($attendance != Timesheet::TIMESHEET_INACTIVE) {
                        if (!property_exists($week_attendance->{$attendance}, $day_name)) {
                            $week_attendance->{$attendance}->{$day_name} = (object)[
                                'attendance_type_count' => 1,
                                'attendance_type_color' => $attendance_color
                            ];
                        } else {
                            $week_attendance->{$attendance}->{$day_name}->attendance_type_count += 1;
                        }
                    }
                    
                }
            }
            $formatted_data[$department_name] = $week_attendance;
        }
        return $formatted_data;
    }

    public static function getTotalAttendanceTypePerEmployee($start_date, $end_date ,$departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_data = [];
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $formatted_data[$department_name] = [];
            $employees = $department->employees;
            foreach ($employees as $key => $employee) {
                $employee_name = $employee->getFullName();
                $timesheets = $employee->timesheets
                                ->where('timesheet_date', '>=', $start_date)
                                ->where('timesheet_date', '<=', $end_date);
                $week_attendance = (object)[];
                foreach ($timesheets as $key => $timesheet) {
                    $attendance = $timesheet->getAttendanceType();
                    $attendance_color = $timesheet->getAttendanceColor();
                    if ($attendance != Timesheet::TIMESHEET_INACTIVE) {
                        if (!property_exists($week_attendance, $attendance)) {
                            $week_attendance->{$attendance} = (object)[
                                'count' => 1,
                                'color' => $attendance_color
                            ];
                        } else {
                            $week_attendance->{$attendance}->count += 1;
                        }
                    }
                    
                }
                $formatted_data[$department_name][$employee_name] = $week_attendance;
            }
        }
        return $formatted_data;
    }
}
