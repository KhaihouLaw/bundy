<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays = [
            ['holiday' => 'New Year', 'type' => 'Regular', 'month' => 1, 'day' => 1, 'working_day' => 0],
            ['holiday' => 'Maundy Thursday', 'type' => 'Regular', 'month' => 4, 'day' => 1, 'working_day' => 0],
            ['holiday' => 'Good Friday', 'type' => 'Regular', 'month' => 4, 'day' => 2, 'working_day' => 0],
            ['holiday' => 'Araw ng Kagitingan', 'type' => 'Regular', 'month' => 4, 'day' => 9, 'working_day' => 0],
            ['holiday' => 'Labor Day', 'type' => 'Regular', 'month' => 5, 'day' => 1, 'working_day' => 0],
            ['holiday' => 'Independence Day', 'type' => 'Regular', 'month' => 6, 'day' => 12, 'working_day' => 0],
            ['holiday' => 'National Heroes Day', 'type' => 'Regular', 'month' => 8, 'day' => 31, 'working_day' => 0],
            ['holiday' => 'Bonifacio Day', 'type' => 'Regular', 'month' => 11, 'day' => 30, 'working_day' => 0],
            ['holiday' => 'Christmas Day', 'type' => 'Regular', 'month' => 12, 'day' => 25, 'working_day' => 0],
            ['holiday' => 'Rizal Day', 'type' => 'Regular', 'month' => 12, 'day' => 30, 'working_day' => 0],
            ['holiday' => 'Chinese New Year', 'type' => 'Special Non-Working', 'month' => 2, 'day' => 12, 'working_day' => 0],
            ['holiday' => 'EDSA Revolution Anniversary', 'type' => 'Special Non-Working', 'month' => 2, 'day' => 25, 'working_day' => 0],
            ['holiday' => 'Black Saturday', 'type' => 'Special Non-Working', 'month' => 4, 'day' => 3, 'working_day' => 0],
            ['holiday' => 'Ninoy Aquino Day', 'type' => 'Special Non-Working', 'month' => 8, 'day' => 21, 'working_day' => 0],
            ['holiday' => 'All Saint Day', 'type' => 'Special Non-Working', 'month' => 11, 'day' => 1, 'working_day' => 0],
            ['holiday' => 'Feast of the Immaculate Conception of Mary', 'type' => 'Special Non-Working', 'month' => 12, 'day' => 8, 'working_day' => 0]
        ];

        foreach ($holidays as $holiday) {
            Holiday::firstOrCreate([
                'holiday' => $holiday['holiday'],
                'type' => $holiday['type'],
                'month' => $holiday['month'],
                'day' => $holiday['day'],
                'working_day' => $holiday['working_day'],
            ]);
        }
    }
}
