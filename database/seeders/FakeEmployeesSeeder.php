<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;

class FakeEmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $departments = [
            Department::COLLEGE,
            Department::HIGH_SCHOOL,
            Department::ELEMENTARY,
            Department::MAINTENANCE
        ];

        for ($index_counter = 0; $index_counter < 1; $index_counter++) {
            $department_id = $departments[array_rand($departments, 1)];
            $first_name = $faker->firstName(array_rand(['male', 'female']));
            $last_name = $faker->lastName();
            $employee = Employee::firstOrCreate([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'department_id' => $department_id
            ]);

            $email = 'user' . $index_counter . '@laverdad.edu.ph';
            User::firstOrCreate([
                'name' => "{$first_name} {$last_name}",
                'email' => $email,
                'employee_id' => $employee->getId(),
                'password' => app('hash')->make('secret123'),
            ]);

            $user = User::where('email', $email)->first();
            $user->assignRole([
                User::ROLE_EMPLOYEE,
            ]);
        }
    }
}
