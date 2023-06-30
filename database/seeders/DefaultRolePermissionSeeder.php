<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DefaultRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //employee_type employee_role

        $defaultRoles = [
            User::ROLE_ADMIN,
            User::ROLE_EMPLOYEE,
            User::ROLE_LEAVE_APPROVER,
            User::ROLE_TIMESHEET_APPROVER
        ];

        $defaultPermissions = [
            User::PERMISSION_RECORD_TIME,
            User::PERMISSION_APPROVE_LEAVE,
            User::PERMISSION_APPROVE_TIMESHEET
        ];

        foreach ($defaultRoles as $defaultRole) {
            Role::firstOrCreate(['name' => $defaultRole]);
        }

        foreach ($defaultPermissions as $defaultPermission) {
            Permission::firstOrCreate(['name' => $defaultPermission]);
        }

        $leaveApprover = Role::where('name', User::ROLE_LEAVE_APPROVER)->first();
        $leaveApprovePermission = Permission::where('name', User::PERMISSION_APPROVE_LEAVE)->first();
        $leaveApprover->givePermissionTo($leaveApprovePermission);

        $timesheetApprover = Role::where('name', User::ROLE_TIMESHEET_APPROVER)->first();
        $timesheetApprovePermission = Permission::where('name', User::PERMISSION_APPROVE_TIMESHEET)->first();
        $timesheetApprover->givePermissionTo($timesheetApprovePermission);

        $employee_role = Role::where('name', User::ROLE_EMPLOYEE)->first();
        $record_time_permission = Permission::where('name', User::PERMISSION_RECORD_TIME)->first();
        $employee_role->givePermissionTo($record_time_permission);

        $hr = User::where('email', env('HR_EMAIL'))->first();
        $hr->assignRole([
            User::ROLE_ADMIN,
            User::ROLE_EMPLOYEE,
            User::ROLE_LEAVE_APPROVER,
            User::ROLE_TIMESHEET_APPROVER
        ]);
    }
}
