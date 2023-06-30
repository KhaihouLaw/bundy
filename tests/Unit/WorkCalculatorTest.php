<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Utilities\WorkCalculator;
use App\Models\Timesheet;
use Tests\TestCase;

class WorkCalculatorTest extends TestCase
{
    
    public function test_employee_timesheets_total_work_hours()
    {

        $testCaseTimesheets = [
            new Timesheet([
                'timesheet_date' => '2021-07-01',
                'time_in' => '07:00:00',
                'time_out' => '17:00:00',
                'lunch_start' => '07:00:00',
                'lunch_end' => '17:00:00',
                'overtime_start' => '17:00:00',
                'overtime_end' => '19:00:00',
            ]),
            new Timesheet([
                'timesheet_date' => '2021-07-02',
                'time_in' => '10:00:00',
                'time_out' => '17:00:00',
                'lunch_start' => '07:00:00',
                'lunch_end' => '17:00:00',
                'overtime_start' => null,
                'overtime_end' => null,
            ]),
            new Timesheet([
                'timesheet_date' => '2021-07-03',
                'time_in' => '07:30:00',
                'time_out' => '16:10:00',
                'lunch_start' => '07:00:00',
                'lunch_end' => '17:00:00',
                'overtime_start' => null,
                'overtime_end' => '20:00:00',
            ]),
            new Timesheet([
                'timesheet_date' => '2021-07-04',
                'time_in' => null,
                'time_out' => '20:00:00',
                'lunch_start' => '07:00:00',
                'lunch_end' => '17:00:00',
                'overtime_start' => '17:00:00',
                'overtime_end' => '18:00:00',
            ]),
            new Timesheet([
                'timesheet_date' => '2021-07-05',
                'time_in' => null,
                'time_out' => '20:00:00',
                'lunch_start' => '07:00:00',
                'lunch_end' => '17:00:00',
                'overtime_start' => '17:00:00',
                'overtime_end' => null,
            ]),
        ];
        $expectedResult = [
            '2021-07-01' => [
                'clock_in' => [
                    'total_hours' => [10, 0, 0],
                ],
                'overtime' => [
                    'total_hours' => [2, 0, 0],
                ],
            ],
            '2021-07-02' => [
                'clock_in' => [
                    'total_hours' => [7, 0, 0],
                ],
            ],
            '2021-07-03' => [
                'clock_in' => [
                    'total_hours' => [8, 40, 0],
                ],
            ],
            '2021-07-04' => [
                'overtime' => [
                    'total_hours' => [1, 0, 0],
                ],
            ],
            '2021-07-05' => [],
            'overall_total_hours' => [28, 40, 0],
        ];

        $tests = WorkCalculator::sumOfWorkHours($testCaseTimesheets, false, true);
        
        $this->assertEquals($expectedResult, $tests);
    }
}
