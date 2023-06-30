<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timesheet;
use App\Models\Employee;
use App\Models\Department;
use App\Models\AcademicYear;
use Carbon\Carbon;

class TimesheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $date_index = Carbon::parse('2021-08-02');
        // $date_limit = Carbon::parse('2022-05-28');
        // $department_ids = Department::whereIn('department', ['Human Resource'])->pluck('id');
        // $employees = Employee::where('department_id', $department_ids)->get();
        $employees = Employee::all();
        foreach ($employees as $employee) {
            error_log(' > ' . $employee->getId() . ' ' . $employee->getFullName() . ' timesheet generation');
            foreach ($employee->schedules as $employeeSchedule) {
                foreach ($employeeSchedule->schedules as $schedule) {
                    $result = Timesheet::generateTimesheet(
                        $employee->getId(),
                        $employeeSchedule->getId(),
                        $schedule->getId()
                    );
                }
            }
        }
    }
}
