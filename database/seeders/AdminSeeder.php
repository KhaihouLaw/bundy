<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Department;
use App\Models\User;
use App\Models\Employee;
use App\Utilities\LVCCCrypto;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dept = Department::firstOrCreate([
            'department' => Department::HR_DEPT,
        ], [
            'supervisor' => env('HR_EMAIL'),
            'approver' => env('HR_EMAIL'),
        ]);
        $employee = Employee::firstOrCreate([
            'first_name' => 'HR',
            'last_name' => 'Admin',
        ], [
            'department_id' => $dept->getId(),
        ]);

        $default_password = 'pR1c3L355!';
        User::firstOrCreate([
            'name' => 'HR Admin',
            'email' => env('HR_EMAIL'),
            'employee_id' => $employee->getId(),
        ], [
            'password' => app('hash')->make($default_password),
            'default_password' => LVCCCrypto::encrypt($default_password),
            'login_token' => '$2y$10$QX/sZVl3gF6fUcSE2Rj/v.LnhNovk6BQrrar.Urbhn3zhNWGemGv2',
            'token_created_at' => '2021-07-03'
        ]);
    }
}
