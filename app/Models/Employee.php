<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\Timesheet;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Auth;

class Employee extends Model
{
    use HasFactory;

    const PART_TIME = 'Part Time';
    const PROBATIONARY = 'Probationary';
    const REGULAR = 'Regular';

    protected static $teaching_department_ids = [
        Department::COLLEGE,
        Department::HIGH_SCHOOL,
        Department::ELEMENTARY,
    ];

    public static $employment_types = [
        self::PART_TIME,
        self::PROBATIONARY,
        self::REGULAR
    ];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'department_id',
        'id_code',
        'position_id',
        'employment_type',
        'birthdate',
        'sick_leave',
        'vacation_leave',
        'approver_id',
    ];

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function position()
    {
        return $this->hasOne('App\Models\Position', 'id', 'position_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id', 'employee_id');
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function workSchedules()
    {
        return $this->hasMany('App\Models\Schedule', 'employee_id', 'id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function approver()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'approver_id');
    }

    public function colleagues()
    {
        return $this->hasMany('App\Models\Employee', 'department_id', 'department_id');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function isClockedInToday()
    {
        return Timesheet::where([
                ['employee_id', '=', $this->getId()],
                ['timesheet_date', '=', date('Y-m-d')]
            ])
            ->whereNull('time_in')
            ->count() == 0;
    }

    public static function isTeaching($department_id) {
        if (in_array($department_id, self::$teaching_department_ids)) {
            return true;
        }
        return false;
    }

    /**
     * dependent: [Department Leave Requests] Button
     */
    public static function isApprover() {
        $email = Auth::user()->email;
        $approver = Department::where('approver', $email)->first();
        if (is_null($approver)) {
            return false;
        }
        return true;
    }

    public static function createFromGoogleUser($user)
    {
        if (is_null($user)) return false;
        $department = Department::firstOrCreate([
            'department' => 'College',
        ], [
            'approver' => env('HR_EMAIL'),
        ]);
        $supervisor_emp = $department->getApprover()->employee;
        $employee = static::create([
            'first_name' => $user->user['given_name'],
            'last_name' => $user->user['family_name'],
            'department_id' => $department->id,
            'approver_id' => $supervisor_emp->id,
        ]);
        return $employee;
    }

    public function isScheduledInDate($date_param, $employee_schedule_id)
    {
        if (empty($date_param)) return false;
        $date_obj = Carbon::parse($date_param);
        $is_rostered = ($this->workSchedules->where('day', $date_obj->format('l'))->where('employee_schedule_id', $employee_schedule_id)->count() > 0);
        if ($is_rostered) {
            return true;
        }
        return false;
    }

    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullName($format = 'last_name, first_name middle_initial')
    {
        $middle = $this->middle_name;
        $middle_to_replace = 'middle_name';
        // if using middle initial instead of middle name
        if (strpos($format, 'middle_initial') !== false) {
            if (!is_null($middle)) {
                $middle = substr($this->middle_name, 0, 1) . '.'; // middle initial
            }
            $middle_to_replace = 'middle_initial';
        }
        return str_replace(
            ['first_name', $middle_to_replace, 'last_name'], 
            [$this->first_name, $middle, $this->last_name],
            $format
        );
    }

    public function setApprover($employee_id)
    {
        $this->approver_id = $employee_id;
        return $this->save();
    }

    public function getApprover()
    {
        $approver = $this->department->approver;
        $userModel = User::findByEmail($approver);
        $employeeModel = $userModel->employee;
        return $employeeModel;
    }

    public function getTimesheetsByDateRange($start_date, $end_date)
    {
        return  $this->timesheets
                    ->where('timesheet_date', '>=', $start_date)
                    ->where('timesheet_date', '<=', $end_date);
    }

    public function getSupervisorName()
    {
        $supervisor = $this->department->getApprover()->employee;
        $supervisor_name = $supervisor ? $supervisor->getFullName() : 'N/A';
        return $supervisor_name;
    }

    public function tryDetachDepartmentRoles()
    {
        $departments = Department::where('supervisor', $this->user->email)->get();
        foreach ($departments as $department) {
            $department->update(['supervisor' => env('HR_EMAIL')]);
        }

        $departments = Department::where('approver', $this->user->email)->get();
        foreach ($departments as $department) {
            $department->update(['approver' => $department->supervisor]);
        }

    }

    public function tryUpdateDeptSuprvsrEmail($new_email, $old_email = null )
    {
        if (empty($old_email)) $old_email = $this->user->email;
        $departments = Department::where('approver', $old_email)->get();
        foreach ($departments as $department) {
            $department->update(['approver' => $new_email]);
        }
    }
    
    public static function getEmployeesByAttendanceTypeOnDate($date, $attendance_type, $departments = [])
    {
        if (count($departments) == 0) {
            $departments = Department::all();
        }
        $formatted_employees_data = [];
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $formatted_employees_data[$department_name] = [];
            $employees = $department->employees;
            foreach ($employees as $key => $employee) {
                $timesheet = $employee->timesheets->where('timesheet_date', $date)->first();
                if(!is_null($timesheet)) {
                    $attendance = $timesheet->getAttendanceType();
                    if ($attendance == $attendance_type) {
                        $employee->attendance_clocks = $timesheet;
                        array_push($formatted_employees_data[$department_name], $employee);
                    }
                }
            }
        }
        return $formatted_employees_data;
    }

    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

     public function getSchedulesByDay($day)
     {
        return Schedule::where('employee_id', $this->id)->where('day', $day)->get();
     }

    /**
     * =================================================================================================================================
     * November 18, 2021
     * =================================================================================================================================
     */

     public function isRegular()
     {
         return $this->employment_type === self::REGULAR;
     }
}
