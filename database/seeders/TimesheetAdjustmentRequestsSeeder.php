<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Timesheet;
use App\Models\TimesheetModificationRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimesheetAdjustmentRequestsSeeder extends Seeder
{
    /**
     * Creates employee adjustment requests
     *
     * @return void
     */
    public function run()
    {
        $employees_count = 10;
        $timesheets_count = 5;
        $start_date = '2022-01-01';
        $end_date = '2022-01-30';

        // 24 hrs format
        $time_in = '07:00';
        $time_out = '17:00';
        $lunch_start = '12:00';
        $lunch_end = '13:00';
        $overtime_start = '18:00';
        $overtime_end = '19:00';
        $notes = trim(strip_tags('adjustment seeds'));
        $statuses = [
            TimesheetModificationRequest::STATUS_PENDING,
            TimesheetModificationRequest::STATUS_APPROVED,
            TimesheetModificationRequest::STATUS_REJECTED,
            TimesheetModificationRequest::STATUS_CANCELLED,
        ];
        $status_index = 0;
        $employees = Employee::all()->take($employees_count);
        
        foreach ($employees as $employee) {
            error_log(' > ' . $employee->getId() . ' ' . $employee->getFullName());
            $timesheets = $employee->timesheets
                            ->where('timesheet_date', '>=', $start_date)
                            ->where('timesheet_date', '<=', $end_date)
                            ->sortBy('timesheet_date')
                            ->sortBy('id')
                            ->take($timesheets_count);
            foreach ($timesheets as $timesheet) {
                TimesheetModificationRequest::create([
                    'employee_id' => $employee->id,
                    'timesheet_id' => $timesheet->id,
                    'employee_schedule_id' => $timesheet->getEmployeeScheduleId(),
                    'timesheet_date' => $timesheet->timesheet_date,
                    'time_in' => $time_in,
                    'time_out' => $time_out,
                    'lunch_start' => $lunch_start,
                    'lunch_end' => $lunch_end,
                    'overtime_start' => $overtime_start,
                    'overtime_end' => $overtime_end,
                    'status' => $statuses[$status_index],
                    'notes' => $notes,
                ]);
                $status_index++;
                if ($status_index >= count($statuses) ) {
                    $status_index = 0;
                }
            }
        }
    }
}
