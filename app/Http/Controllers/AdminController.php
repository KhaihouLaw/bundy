<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utilities\TimeUtility;
use App\Utilities\AttendanceUtility;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\AdvancePunchClock;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\Timesheet;
use App\Models\TimesheetModificationRequest;
use App\Models\User;
use PDF;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.home');
    }

    public function approvers()
    {
        $departments = Department::all();
        return view('admin.approvers', compact('departments'));
    }

    public function attendanceToday()
    {
        $today = date('Y-m-d');
        $attendance_today = Timesheet::getAttendancesOnDate($today);
        $present = $attendance_today->{Timesheet::EMPLOYEE_PRESENT};
        $late = $attendance_today->{Timesheet::EMPLOYEE_LATE};
        $on_leave = $attendance_today->{Timesheet::EMPLOYEE_ON_LEAVE};
        $absent = $attendance_today->{Timesheet::EMPLOYEE_ABSENT};
        $present_and_late = clone $present;
        $on_leave_and_absent = clone $on_leave;
        foreach($late as $k => $v) $present_and_late->$k = $v;
        foreach($absent as $k => $v) $on_leave_and_absent->$k = $v;
        $department_stats = Department::countPresentOnTimesheetsByDept(null, $present_and_late);
        return view('admin.attendance-today', compact('present_and_late', 'on_leave_and_absent', 'department_stats'));
    }

    public function leaveRequests()
    {
        $leave_requests = LeaveRequest::all();
        return view('admin.leave-requests', compact('leave_requests'));
    }

    public function holidays()
    {
        $holidays = Holiday::orderBy('month')->get();
        return view('admin.holidays', compact('holidays'));
    }

    public function timesheetModifications(Request $request)
    {
        $filter_keywords = $request->keywords;
        $all_timesheet_adjustments = TimesheetModificationRequest::select('timesheet_modification_requests.*')
                                        ->join('employees','employees.id','=','timesheet_modification_requests.employee_id')
                                        ->orderBy('timesheet_date', 'DESC')
                                        ->orderBy('timesheet_modification_requests.id', 'DESC');
        $filtered_timesheet_adjustments = clone $all_timesheet_adjustments;
       
        if (!empty($filter_keywords)) {
            $keywords = explode(' ', $filter_keywords);
            foreach ($keywords as $keyword) {
                $filtered_timesheet_adjustments = $filtered_timesheet_adjustments
                    ->where(function($query) use ($keyword){
                        $query->where('timesheet_modification_requests.timesheet_date', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.time_in', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.time_out', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.lunch_start', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.lunch_end', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.overtime_start', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.overtime_end', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.status', 'like', '%' . $keyword . '%')
                            ->orWhere('timesheet_modification_requests.notes', 'like', '%' . $keyword . '%')
                            ->orWhere('employees.first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('employees.middle_name', 'like', '%' . $keyword . '%')
                            ->orWhere('employees.last_name', 'like', '%' . $keyword . '%');
                    });
            }
        }
        // filter next batch
        if (!empty($request->last_timesheet_date)) {
            $batch_condition = $request->batch === 'next' ? '<=' : '>=';
            $filtered_timesheet_adjustments = $filtered_timesheet_adjustments->where('timesheet_date', $batch_condition, $request->last_timesheet_date);
        }

        // $timesheet_ids = Timesheet::getIdsOfNonEmptyTimesheets();
        $timesheet_ids = Timesheet::getIds();
        $timesheet_adjustments = $filtered_timesheet_adjustments
            ->whereIn('timesheet_id', $timesheet_ids)
            ->limit(100)
            ->get();

        $status_counts = [
            'pending' => TimesheetModificationRequest::countPending(),
            'approved' => TimesheetModificationRequest::countApproved(),
            'rejected' => TimesheetModificationRequest::countRejected(),
            'cancelled' => TimesheetModificationRequest::countCancelled(),
        ];
            
        return view(
            'admin.timesheet-modifications', 
            compact(
                'all_timesheet_adjustments', 
                'filtered_timesheet_adjustments', 
                'timesheet_adjustments',
                'filter_keywords',
                'status_counts',
            )
        );
    }

    /**
     * ========================== Attendance Report Generator ==========================
     */
    public function attendanceReport()
    {
        return view('admin.attendance-report');
    }

    public function generateAttendanceReport(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        } 
        $reportData = $this->getAttendanceDataReport($request->start_date, $request->end_date);
        return view('pdf.attendance-report.component.table', [
            'report' => $reportData
        ]);
    }

    public function getAttendanceDataReport($start_date, $end_date) 
    {
        $report = Timesheet::generateReport($start_date, $end_date);
        $formatted = [];
        $start = date('F d, Y', strtotime($start_date));
        $end = date('F d, Y', strtotime($end_date));
        $uid = 0;
        foreach($report as $record) {

            $timesheets = $record['timesheets'];
            $overAllTotalHrs = [];
            
            if (isset($timesheets['overall_total_hours'])) {
                $total_hrs = 0;
                $total_mins = 0;
                if (isset($timesheets['overall_total_hours'][0])) {
                    $total_hrs = $timesheets['overall_total_hours'][0];
                }
                if (isset($timesheets['overall_total_hours'][1])) {
                    $total_mins = $timesheets['overall_total_hours'][1];
                }
                $total_rounded = TimeUtility::getTotalHrs($total_hrs, $total_mins);
                $total_str = strval($total_rounded) . ' Hours';
                $overAllTotalHrs = ['overall-total-hours' => [$total_str]];
            }
            
            unset($timesheets['overall_total_hours']);
            $formattedTimesheet = [];
            
            foreach($timesheets as $date => $timesheet) {
                // timesheet['timesheet'] may be an approved modification
                $formattedTimesheet[$date]['formatted-clock'] = TimeUtility::format24Hrs([$timesheet['timesheet']]);
                $formattedTimesheet[$date]['note'] = '';
                // report notes
                if (
                    is_null($formattedTimesheet[$date]['formatted-clock']['time-in']) ||
                    is_null($formattedTimesheet[$date]['formatted-clock']['time-out'])
                ) {
                    $formattedTimesheet[$date]['note'] = 'Incomplete Timesheet';
                }
                if (get_class($timesheet['timesheet']) == 'App\Models\Timesheet') {
                    $modification = $timesheet['timesheet']->modification;
                    if (!is_null($modification) && $modification->isPending()) {
                        $formattedTimesheet[$date]['note'] = 'Pending Adjustment Request';
                    }
                }
                if (!is_null($timesheet['day_total_hrs'])) {
                    list($hrs, $mins) = $timesheet['day_total_hrs'];
                    $formattedTimesheet[$date]['day-total-hrs'] = [$hrs . ' Hours', $mins . ' Minutes'];
                } else {
                    $formattedTimesheet[$date]['day-total-hrs'] = ['0 Hours', '0 Minutes'];
                }
                $formattedTimesheet[$date]['uid'] = $uid;
                if (property_exists($timesheet['timesheet'], 'has_undertime')) {
                    if ($timesheet['timesheet']->hasUndertime()) {
                        $formattedTimesheet[$date]['note'] .= " | " . $timesheet['timesheet']->getUndertimeNotes();
                    }
                }
                $uid++;
            }

            $record['employee']['is-teaching'] = Employee::isTeaching($record['employee']->department_id);
            array_push($formatted, ['employee' => $record['employee'], 'timesheets' => $formattedTimesheet + $overAllTotalHrs]);
        }
        $formatted = [ 'records' => $formatted, 'date-range' => [$start, $end]];
        return $formatted;
    }

    public function createAttendanceReportPDF($start_date, $end_date) 
    {
        $validator = Validator::make([
            'start_date' => $start_date,
            'end_date' => $end_date
        ], [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 400);
        } 
        $dataReport = $this->getAttendanceDataReport($start_date, $end_date);
        view()->share('report', $dataReport);
        $pdf = PDF::loadView('pdf.attendance-report.index', $dataReport)->setPaper('A4', 'landscape');
        // return $pdf->stream(); // open to browser
        return $pdf->download('attendance_report_lvcc_bundy.pdf');
    }


    /**
     * ========================== Attendance Summary [START] ==========================
     */

    public function attendanceSummary() 
    {
        return view('admin.attendance-summary');
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
        $formatted_data = AttendanceUtility::getMonthAttendanceSummaryData($start_date, $end_date);
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
        $formatted_data = AttendanceUtility::getAttendanceSummaryOnDate($date);
        return view('admin.components.attendance-date-summary', compact('formatted_data', 'date'));
    }

    public function getPresentEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_PRESENT;
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type);
    }

    public function getLateEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_LATE;
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type);
    }

    public function getOnLeaveEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_ON_LEAVE;
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type);
    }

    public function getAbsentEmployeeNamesOnDate($date)
    {
        $attendance_type = Timesheet::EMPLOYEE_ABSENT;
        return self::getEmployeesByAttendanceTypeOnDate($date, $attendance_type);
    }

    public static function getEmployeesByAttendanceTypeOnDate($date, $attendance_type)
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
        $formatted_data = Employee::getEmployeesByAttendanceTypeOnDate($date, $attendance_type);
        return view('admin.components.attendance-type-summary', compact('formatted_data', 'date', 'attendance_type'));
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
        $formatted_data = AttendanceUtility::getAttendanceSummary($start_date, $end_date);
        return view('admin.components.attendance-week-overall-summary', compact('formatted_data', 'start_date', 'end_date'));
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
        $formatted_data = AttendanceUtility::getTotalAttendanceTypePerEmployee($start_date, $end_date);
        return view('admin.components.attendance-total-per-employee-summary', compact('formatted_data', 'start_date', 'end_date'));
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
        $formatted_data = AttendanceUtility::getTotalAttendanceTypePerDay($start_date, $end_date);
        return view('admin.components.attendance-total-per-day-summary', compact('formatted_data', 'start_date', 'end_date'));
    }

    /**
     * ========================== Attendance Summary [END] ==========================
     */
    

    /**
     * ========================== Management Views [START] ==========================
     */

    public function timesheetManagement($employee_id)
    {
        $employee = Employee::find($employee_id);
        $timesheets = Timesheet::where('employee_id', $employee_id)
                        ->orderBy('timesheet_date', 'asc')
                        ->get();
        return view('admin.timesheet-management', compact('employee', 'timesheets'));
    }

    public function scheduleManagement($employee_id) 
    {
        $academic_years = AcademicYear::all();
        $employee = Employee::find($employee_id);
        $schedules = Schedule::where('employee_id', $employee_id)->get();
        return view('admin.schedule-management', compact('employee', 'schedules', 'academic_years'));
    }

    public function academicYearManagement()
    {
        $academic_years = AcademicYear::all();
        return view('admin.academic-year-management', compact('academic_years'));
    }

    public function departmentManagement()
    {
        $departments = Department::all();
        $employees = User::orderBy('email', 'asc')->get();
        return view('admin.department-management', compact('departments', 'employees'));
    }

    public function employeeManagement()
    {
        $hr = User::findByEmail(env('HR_EMAIL'));
        $employees = Employee::where('id', '!=', $hr->employee_id)->get();
        $departments = Department::all();
        $positions = Position::all();
        $employment_types = Employee::$employment_types;
        return view('admin.employee-management', compact('employees', 'departments', 'positions', 'employment_types'));
    }

    public function advancePunchClockManagement()
    {
        $apc_instances = AdvancePunchClock::all();
        return view('admin.advance-punch-clock-management', compact('apc_instances'));
    }

    /**
     * ========================== Management Views [END] ==========================
     */

    /**
     * ========================== Leave Report [START] ==========================
     */
    public function leaveReport()
    {
        return view('admin.leave-report');
    }

    public function generateLeaveReport(Request $request) 
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date) || !empty($end_date)) {
            $validator = Validator::make($request->all(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 400);
            } 
            $report = LeaveRequest::generateReport($request->start_date, $request->end_date);
        } else {
            $report = LeaveRequest::generateReport();
        }
        return view('pdf.leave-report.component.table', compact('report'));
    }
    /**
     * ========================== Leave Report [END] ==========================
     */
}
