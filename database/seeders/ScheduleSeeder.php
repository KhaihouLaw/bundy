<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\EmployeeSchedule;
use App\Models\AcademicYear;
use App\Models\Employee;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
        foreach (Employee::all() as $employee) {
            foreach ($employee->schedules as $employeeSchedule) {
                foreach ($days as $day) {
                    if ($day == '2021-08-09' && $employee->department->getDepartment() != 'College') {
                        $start_time = '07:00:00';
                        $end_time = '16:00:00';
                    }
                    Schedule::create([
                        'employee_id' => $employee->getId(),
                        'employee_schedule_id' => $employeeSchedule->getId(),
                        'day' => $day,
                        'start_time' => $start_time,
                        'end_time' => $end_time
                    ]);
                }
            }
        }
    }
}
