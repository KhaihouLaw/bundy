<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leaveTypes = [
            'Sick Leave',
            'Holiday (Public/Special)',
            'Maternity Leave',
            'Bereavement Leave',
            'Unpaid Leave',
            'Annual Leave',
            'Religious Holidays',
            'Paternity Leave',
            'Study Leave'
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate([
                'leave' => $type
            ]);
        }
    }
}
