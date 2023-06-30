<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\AcademicYear;
use App\Models\EmployeeSchedule;
use App\Models\TimesheetModificationRequest;
use App\Utilities\WorkCalculator;
use Carbon\Carbon;
use Auth;
use App\Http\Traits\HasClockRecords;

class Timesheet extends Model
{
    use HasFactory;
    use HasClockRecords;

    const TIME_IN = 'time-in';
    const TIME_OUT = 'time-out';
    const LUNCH_START = 'lunch-start';
    const LUNCH_END = 'lunch-end';
    const OVERTIME_START = 'overtime-start';
    const OVERTIME_END = 'overtime-end';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    const EMPLOYEE_PRESENT = 'present';
    const EMPLOYEE_ABSENT = 'absent';
    const EMPLOYEE_LATE = 'late';
    const EMPLOYEE_ON_LEAVE = 'on-leave';
    const TIMESHEET_INACTIVE = 'inactive';

    protected $fillable = [
        'employee_id',
        'employee_schedule_id',
        'schedule_id',
        'timesheet_date',
        'time_in',
        'time_out',
        'lunch_start',
        'lunch_end',
        'overtime_start',
        'overtime_end',
        'location',
        'has_undertime',
        'undertime_notes'
    ];

    static $array_of_months = [
        "January" => [],
        "February" => [],
        "March" => [],
        "April" => [],
        "May" => [],
        "June" => [],
        "July" => [],
        "August" => [],
        "September" => [],
        "October" => [],
        "November" => [],
        "December" => [],
    ];

    public function getEmployeeScheduleId()
    {
        return $this->employee_schedule_id;
    }

    public function getTimeIn($is_carbon = false)
    {
        if ($is_carbon) {
            if (!is_null($this->time_in)) {
                return Carbon::parse($this->time_in);
            }
        }
        return $this->time_in;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function modification()
    {
        return $this->hasOne(TimesheetModificationRequest::class);
    }

    public function hasUndertime()
    {
        return ($this->has_undertime == 1);
    }

    public function getUndertimeNotes()
    {
        return $this->undertime_notes;
    }

    /**
     * Returns the Timesheet using the schedule ID
     */
    public static function getByScheduleId($schedule_id)
    {
        if (empty($schedule_id)) {
            return null;
        }
        $employee = Auth::user()->employee;
        if (is_null($employee)) {
            return null;
        }
        return static::where('schedule_id', $schedule_id)
            ->where('employee_id', $employee->getId())
            ->where('timesheet_date', date('Y-m-d'))
            ->first();
    }

    public static function createTimesheet(
        $employee_id,
        $employee_schedule_id,
        $schedule_id,
        $date_param
    )
    {
        $date_param_obj = Carbon::parse($date_param);
        $date_today_obj = Carbon::parse(date('Y-m-d'));
        $timesheet_data = [
            'employee_id' => $employee_id,
            'employee_schedule_id' => $employee_schedule_id,
            'schedule_id' => $schedule_id,
            'timesheet_date' => $date_param
        ];
        // DISABLE DEFAULT DATA
        // if ($date_param_obj->lt($date_today_obj)) {
            $timesheet_data['time_in'] = '08:00:00';
            $timesheet_data['time_out'] = '17:00:00';
            $timesheet_data['lunch_start'] = '12:00:00';
            $timesheet_data['lunch_end'] = '13:00:00';
        // }
        $timesheet = static::create($timesheet_data);
        return (!is_null($timesheet));
    }

    public function punch($type)
    {
        $clock = date('H:i:s');
        $time_now = $clock;
        if (static::TIME_IN == $type) $this->time_in = $time_now;
        if (static::TIME_OUT == $type) $this->time_out = $time_now;
        if (static::LUNCH_START == $type) $this->lunch_start = $time_now;
        if (static::LUNCH_END == $type) $this->lunch_end = $time_now;
        if (static::OVERTIME_START == $type) $this->overtime_start = $time_now;
        if (static::OVERTIME_END == $type) $this->overtime_end = $time_now;

        // Date Today
        $this->timesheet_date = date('Y-m-d');
        if ($this->save()) {
            return date('h:i A', strtotime($clock));
        }
        return false;
    }

    public function saveUndertimeNotes($notes)
    {
        $this->undertime_notes = $notes;
        $this->has_undertime = true;
        return $this->save(); 
    }

    /**
     * Update DTR
     */
    public function updateTimesheet(
        $time_in = null,
        $time_out = null,
        $lunch_start = null,
        $lunch_end = null,
        $overtime_start = null,
        $overtime_end = null
    )
    {
        $this->time_in = $time_in;
        $this->time_out = $time_out;
        $this->lunch_start = $lunch_start;
        $this->lunch_end = $lunch_end;
        $this->overtime_start = $overtime_start;
        $this->overtime_end = $overtime_end;
        return $this->save();
    }

    public static function generateSchedulesForNewEmployee($employee_id, $employee_schedule_id)
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        ];
        $start_time = '08:00:00';
        $end_time = '17:00:00';
        $employee = Employee::find($employee_id);
        if (is_null($employee)) {
            return false;
        }
        foreach ($days as $day) {
            // Hardcoded for initial launch
            if ($day == '2021-08-09' && $employee->department->getDepartment() != 'College') {
                $start_time = '07:00:00';
                $end_time = '16:00:00';
            }
            Schedule::create([
                'employee_id' => $employee_id,
                'employee_schedule_id' => $employee_schedule_id,
                'day' => $day,
                'start_time' => $start_time,
                'end_time' => $end_time
            ]);
        }
    }

    /**
     * Timesheet generator
     */
    public static function generateTimesheet(
        $employee_id,
        $employee_schedule_id = null,
        $schedule_id = null
    )
    {
        $employee = Employee::find($employee_id);
        if (is_null($employee)) return false;
        if (is_null($employee_schedule_id)) return false;
        if (is_null($schedule_id)) return false;

        $ay = AcademicYear::first();
        $date_index = $ay->getStartDate(true);

        // TODO: add period start and end date
        while ($date_index->lte($ay->getEndDate(true))) {
            $date_string = $date_index->format('Y-m-d');
            if ($employee->isScheduledInDate($date_string, $employee_schedule_id)) {
                $schedule = Schedule::find($schedule_id);
                $date_string_obj = Carbon::parse($date_string);
                if ($date_string_obj->format('l') == $schedule->getDay()) {
                    static::createTimesheet(
                        $employee->getId(),
                        $employee_schedule_id,
                        $schedule_id,
                        $date_string
                    );
                }
            }
            $date_index = $date_index->addDays(1);
        }
    }

    /**
     * ATTENDANCE
     */
    public static function countClockedInToday()
    {
        return static::where('timesheet_date', date('Y-m-d'))
            ->whereNotNull('time_in')
            ->count();
    }

    public static function countAbsentToday()
    {
        return static::where('timesheet_date', date('Y-m-d'))
            ->whereNull('time_in')
            ->count();
    }

    public static function getPresentsOnDate($date)
    {
        return self::where('timesheet_date', $date)
                        ->whereNotNull('time_in')
                        ->orderBy('time_in', 'DESC')
                        ->get();
    }

    public static function getLatesOnDate($date)
    {
        $timesheets = self::where('timesheet_date', $date)
                        ->whereNotNull('time_in')
                        ->orderBy('time_in', 'DESC')
                        ->get();
        $late_timesheets = (object)[];
        foreach ($timesheets as $key => $timesheet) {
            $is_late = $timesheet->isLateToday();
            if ($is_late) {
                $late_timesheets->{$key} = $timesheet;
            }
        }
        return $late_timesheets;
    }

    public static function getOnLeaveOnDate($date)
    {
        $timesheets = self::where('timesheet_date', $date)
                        ->whereNull('time_in')
                        ->get();
        $on_leave_timesheets = (object)[];
        foreach ($timesheets as $key => $timesheet) {
            $is_on_leave = $timesheet->isEmployeeOnLeave();
            if ($is_on_leave) {
                $on_leave_timesheets->{$key} = $timesheet;
            }
        }
        return $on_leave_timesheets;
    }

    public static function getAbsentsOnDate($date)
    {
        $today = date('Y-m-d');
        $is_not_active = strtotime($date) > strtotime($today);
        if ($is_not_active) return null;
        $timesheets = self::where('timesheet_date', $date)
                    ->whereNull('time_in')
                    ->get();
        $absent_timesheets = (object)[];
        // filter NOT on leave
        foreach ($timesheets as $key => $timesheet) {
            $not_on_leave = !$timesheet->isEmployeeOnLeave();
            if ($not_on_leave) {
                $absent_timesheets->{$key} = $timesheet;
            }
        }
    }

    public static function getAttendancesOnDate($date)
    {
        $timesheets = self::where('timesheet_date', $date)->get();
        $all_attendances = (object)[
            self::EMPLOYEE_PRESENT => (object)[],
            self::EMPLOYEE_LATE => (object)[],
            self::EMPLOYEE_ON_LEAVE => (object)[],
            self::EMPLOYEE_ABSENT => (object)[],
        ];
        foreach ($timesheets as $key => $timesheet) {
            $attendance_type = $timesheet->getAttendanceType();
            if ((self::EMPLOYEE_ON_LEAVE == $attendance_type) || (self::EMPLOYEE_ABSENT == $attendance_type)) {
                $leave_request = $timesheet->hasLeaveRequest();
                if (!is_null($leave_request)) $timesheet->leave_request = $leave_request;
            }
            $all_attendances->{$attendance_type}->{$key} = $timesheet;
        }
        return $all_attendances;
    }

    public static function autogenerateTimesheetForNewUser(Employee $employee)
    {
        $ay = AcademicYear::getCurrentAcademicYear();
        if (!is_null($ay)) {
            $employee_schedule = EmployeeSchedule::create([
                'employee_id' => $employee->getId(),
                'academic_year_id' => $ay->getId(),
                'period' => 'whole year'
            ]);
            $employee = Employee::find($employee->getId());
            $result = static::generateSchedulesForNewEmployee($employee->getId(), $employee_schedule->getId());
            // Stop if schedule was not generated
            if (!$result) return false;
            $employee = Employee::find($employee->getId());
            foreach ($employee->schedules as $employee_schedule) {
                foreach ($employee_schedule->schedules as $schedule) {
                    static::generateTimesheet(
                        $employee->getId(),
                        $employee_schedule->getId(),
                        $schedule->getId()
                    );
                }
            }
        }
    }

    public function isLateToday()
    {
        $time_in = $this->getTimeIn(true);
        if (!is_null($time_in)) {
            $start_time = Carbon::parse($this->schedule->start_time);
            if ($time_in->lte($start_time)) {
                return false;
            }
        }
        return true;
    }

    public function hasClockedInToday()
    {
        $time_in = $this->getTimeIn(true);
        if (!is_null($time_in) && ($time_in instanceof Carbon)) {
            return true;
        }
        return false;
    }

    public function hasLeaveRequest() {
        $time_in = $this->getTimeIn(true);
        if (is_null($time_in)) {
            return $this->employee->leaveRequests
                    ->where('start_date', '<=', $this->timesheet_date)
                    ->where('end_date', '>=', $this->timesheet_date)
                    ->first();
        }
        return null;
    }

    public function isEmployeeOnLeave() {
        $time_in = $this->getTimeIn(true);
        if (is_null($time_in)) {
            $approved_leave = $this->employee->leaveRequests
                                ->where('status', 'approved')
                                ->where('start_date', '<=', $this->timesheet_date)
                                ->where('end_date', '>=', $this->timesheet_date)
                                ->first();
            if (!is_null($approved_leave)) return true;
        }
        return false;
    }

    public function isActivated() {
        $timesheet_date = date('Y-m-d', strtotime($this->timesheet_date));
        $date_today = date('Y-m-d');
        return $timesheet_date <= $date_today;
    }

    public function getAttendanceType() {
        if (!$this->isActivated()) return self::TIMESHEET_INACTIVE;
        if ($this->hasClockedInToday()) {
            if ($this->isLateToday()) return self::EMPLOYEE_LATE;
            return self::EMPLOYEE_PRESENT;
        }
        else if ($this->isEmployeeOnLeave()) return self::EMPLOYEE_ON_LEAVE;
        else return self::EMPLOYEE_ABSENT;
    }

    public function getAttendanceColor() {
        if (!$this->isActivated()) return 'gray';
        if ($this->hasClockedInToday()) {
            if ($this->isLateToday()) return 'yellow';
            return 'green';
        }
        else if ($this->isEmployeeOnLeave()) return 'gray';
        else return 'red';
    }

    public function saveLocation($location)
    {
        $this->location = json_encode($location);
        return $this->save();
    }

    public static function getTimesheetsByEmployee($employee_id)
    {
        return self::
            where('employee_id', $employee_id)->
            where('timesheet_date', '<=', date('Y:m:d H:i:s'))->get();
    }

    public static function useDatesAsKeys($employee_id)
    {
        $timesheets_with_date_as_key = [];
        $timesheets = self::getTimesheetsByEmployee($employee_id);
        foreach ($timesheets as $timesheet) {
            $timesheets_with_date_as_key[$timesheet['timesheet_date']] = $timesheet;
        }
        return $timesheets_with_date_as_key;
    }

    public static function groupByMonth($employee_id)
    {
        $timesheets_grouped_by_month = self::$array_of_months;
        $timesheets = self::getTimesheetsByEmployee($employee_id);
        foreach ($timesheets as $timesheet) {
            $month = date_format(date_create($timesheet['timesheet_date']), 'F');
            array_push($timesheets_grouped_by_month[$month], $timesheet);
        }
        return $timesheets_grouped_by_month;
    }

    /**
     * @param string Y-m-d date range (start, end) included
     */
    public static function generateReport($date_start, $date_end)
    {
        $report = [];
        $employees = Employee::all()->sortBy('last_name');
        foreach ($employees as $employee) {
            $timesheets = Timesheet::where('employee_id', $employee->id)
                ->where('timesheet_date', '>=', $date_start)
                ->where('timesheet_date', '<=', $date_end)
                ->whereNotNull('time_in')
                ->orderBy('timesheet_date', 'asc')
                ->get();
            $timesheets = self::getApprovedModifications($timesheets);
            $is_regular = false;
            if ($employee->employment_type == 'Regular') {
                $is_regular = true;
            }
            $report[$employee['id']]['timesheets'] = WorkCalculator::sumOfWorkHours($timesheets, $is_regular);
            $report[$employee['id']]['employee'] = $employee;
        }
        return $report;
    }

    public static function getIdsOfNonEmptyTimesheets()
    {
        return static::whereRaw('time_in IS NOT NULL OR time_out IS NOT NULL')
            ->pluck('id');
    }

    public static function getIds()
    {
        return static::all()->pluck('id');
    }

    /**
     * Dependent: Attendance Report Generator
     */
    public static function getApprovedModifications($timesheets) 
    {
        $filtered = [];
        foreach ($timesheets as $key => $timesheet) {
            $modification = $timesheet->modification;
            if (!is_null($modification) && $modification->isApproved()) {
                array_push($filtered, $modification);
            } else {
                array_push($filtered, $timesheet);
            }
        }
        return $filtered;
    }

    /**
     * Dependent: Clock out email
     * @param date string YYYY-MM-DD
     */
    public static function getNotClockedOutTimesheetsByDate($date)
    {
        $timesheets = Timesheet::where('timesheet_date', $date)
                        ->whereNotNull('time_in')
                        ->whereNull('time_out')
                        ->get();                        
        return $timesheets;
    }
}
