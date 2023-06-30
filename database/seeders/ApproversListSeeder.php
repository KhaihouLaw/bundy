<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Utilities\LVCCCrypto;

class ApproversListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = array_map('str_getcsv', file('employees.csv'));
        $nowCarbon = Carbon::now();
        $today = $nowCarbon->isoFormat('YYYY-MM-DD');
        foreach ($csv as $employee)
        {
            // error_log(json_encode($employee));
            $department = trim($employee[4]);
            $last_name = trim($employee[0]);
            $first_name = trim($employee[1]);
            $middle_name = trim($employee[2]);
            $email = trim($employee[3]);
            $user = User::findByEmail($email);
            if (!is_null($user)) {
                error_log($email . ' already exists');
                continue;
            }

            $department = Department::where('department', $department)->first();
            if (is_null($department)) {
                $department = Department::where('department', 'College')->first();
            }
            $employee = Employee::firstOrCreate([
                'first_name' => ucfirst($first_name),
                'middle_name' => ucfirst($middle_name),
                'last_name' => ucfirst($last_name),
                'department_id' => $department->getId(),
            ], [
                'approver_id' => 1
            ]);
            if (!is_null($employee)) {
                $random_password = strtoupper(substr(str_shuffle(MD5(microtime())), 0, 7));
                $user = User::firstOrCreate([
                    'email' => $email,
                    'employee_id' => $employee->getId(),
                ], [
                    'name' => $first_name . ' ' . $middle_name . ' ' . $last_name,
                    'password' => app('hash')->make($random_password),
                    'default_password' => LVCCCrypto::encrypt($random_password),
                    'login_token' => Hash::make($email . $today),
                    'token_created_at' => date('Y-m-d')
                ]);
                error_log(' > New User: ' . $employee->getFullName() . ' ' . $user->getEmail());
            }
        }

        foreach (Employee::all() as $employee)
        {
            if (!is_null($employee->department)) {
                $approverUser = User::findByEmail($employee->department->getApproverEmail());
                $employee->setApprover($approverUser->employee->getId());
                error_log($employee->getFullName() . ' approver is ' . $approverUser->employee->getFullName());
            }
        }
    }
}
