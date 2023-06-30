<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Position;

class EmployeePosition extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = array_map('str_getcsv', file('employee-position-status.csv'));
        foreach ($csv as $employee)
        {
            $email = trim($employee[0]);
            $position = trim($employee[1]);
            $status = trim($employee[2]);

            $positionModel = Position::firstOrCreate(['position' => $position]);
            // $user = User::where('email', $email)->with('employee')->first();
            // if (!is_null($user)) {
            //     if (is_null($user->employee->position_id) && is_null($user->employee->employment_type)) {
            //         $employeeModel = $user->employee;
            //         $employeeModel->position_id = $positionModel->id;
            //         $employeeModel->employment_type = empty($status) ? null : $status;
            //         $employeeModel->save();

            //         error_log(' > ' . $user->email . ' is set to ' . $positionModel->position . ' and ' . $status);
            //     } else {
            //         error_log(' > ' . $user->email . ' already have position and depaertment set');
            //     }
            // } else {
            //     error_log($email . ' not found');
            // }
        }
    }
}
