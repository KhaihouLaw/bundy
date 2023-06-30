<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeSchedule;
use App\Models\AcademicYear;
use App\Models\Employee;

class EmployeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $periods = [
            'whole year',
            '1st semester',
            '2nd semester'
        ];
        foreach (AcademicYear::all() as $ay) {
            foreach (Employee::all() as $employee) {
                EmployeeSchedule::create([
                    'employee_id' => $employee->getId(),
                    'academic_year_id' => $ay->getId(),
                    'period' => $periods[0]
                ]);
            }
        }
    }
}
