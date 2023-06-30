<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Timesheet;

class Department extends Model
{
    use HasFactory;

    const HR = 1;
    const COLLEGE = 2;
    const HIGH_SCHOOL = 3;
    const ELEMENTARY = 4;
    const ADMINISTRATION = 5;
    const MAINTENANCE = 6;

    const HR_DEPT = 'Human Resource';

    protected $fillable = [
        'department',
        'supervisor',
        'approver',
    ];

    public function employees()
    {
        return $this->hasMany('App\Models\Employee', 'department_id', 'id');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getApproverEmail()
    {
        return $this->approver;
    }

    public function getApprover()
    {
        $user = User::findByEmail($this->approver);
        if (is_null($user)) {
            return new User;
        }
        return $user;
    }

    /**
     * Get all departments' approver / supervisors
     */
    public static function getSupervisors($departments = null) 
    {
        $supervisors = [];
        if (is_null($departments)) {
            $departments = self::all();
        }
        foreach ($departments as $key => $department) {
            array_push($supervisors, $department->getApprover());
        }
        return $supervisors;
    }

    /**
     * dependents: attendance TODAY
     * @param departments array
     * @return result array [{department: {total_employees, present_today}}, ...]
     */
    public static function getNumOfEmployeesEachDepartment($departments = null)
    {
        $result = (object)[];
        if (is_null($departments)) {
            $departments = self::all();
        }
        foreach ($departments as $key => $department) {
            $department_name = $department->getDepartment();
            $result->{$department_name} = (object)[
                'total_employees' => count($department->employees),
                'present_today' => 0
            ];
        }
        return $result;
    }

    /**
     * dependents: attendance TODAY
     */
    public static function countPresentOnTimesheetsByDept($department_stats = null, $timesheets = null)
    {
        if (is_null($department_stats)) {
            $department_stats = self::getNumOfEmployeesEachDepartment();
        }
        if (is_null($timesheets)) {
            $today = date('Y-m-d');
            $timesheets = Timesheet::getPresentOnDate($today);
        }
        foreach ($timesheets as $key => $timesheet) {
            $department = $timesheet->employee->department->getDepartment();
            if (property_exists($department_stats, $department)) {
                $department_stats->{$department}->present_today += 1;
            }
        }
        return $department_stats;
    }

    /**
     * dependent: attendance utility
     */
    public static function getDepartmentNames($departments = [])
    {
        $department_names = [];
        foreach ($departments as $key => $department) {
            array_push($department_names, $department->getDepartment());
        }
        return $department_names;
    }

    /**
     * dependent: attendance utility
     */
    public static function getTimesheetsByDateRange($departments, $start_date, $end_date)
    {
        $department_names = self::getDepartmentNames($departments);
        $timesheets = Timesheet::where('timesheet_date', '>=', $start_date)
            ->where('timesheet_date', '<=', $end_date)
            ->get();
        $timesheets_filtered_by_departments = [];
        foreach ($timesheets as $key => $timesheet) {
            $department_name = $timesheet->employee->department->getDepartment();
            if (in_array($department_name, $department_names)) {
                array_push($timesheets_filtered_by_departments, $timesheet);
            }
        }
        return $timesheets_filtered_by_departments;
    }
}
