<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            DepartmentSeeder::class,
            HolidaySeeder::class,
            LeaveTypeSeeder::class,
            AdminSeeder::class,
            ApproversListSeeder::class,
            EmployeePosition::class,
            DefaultRolePermissionSeeder::class,
            // FakeEmployeesSeeder::class,
            AcademicYearSeeder::class,
            EmployeeScheduleSeeder::class,
            ScheduleSeeder::class,
            TimesheetSeeder::class,
        ]);
    }
}
