<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Timesheet;
use Auth;


class PunchType implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($scheduleId, $employeeId)
    {
        $this->timesheet = Timesheet::
            where('employee_id', $employeeId)->
            where('schedule_id', $scheduleId)->
            where('timesheet_date', date('Y-m-d'))->first();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $type)
    {
        if (is_null($this->timesheet)) return false;
        return (
            (Timesheet::TIME_IN == $type) &&
            $this->isNullClocks([
                'time-in' => true,
                'time-out' => true,
                'lunch-start' => true,
                'lunch-end' => true,
                'overtime-start' => true,
                'overtime-end' => true,
                'lunch-optional' => false,
            ])
        ) || (
            (Timesheet::TIME_OUT == $type) && 
            $this->isNullClocks([
                'time-in' => false,
                'time-out' => true,
                'lunch-optional' => true,
                'overtime-start' => true,
                'overtime-end' => true,
            ])
        ) || (
            (Timesheet::LUNCH_START == $type) &&
            $this->isNullClocks([
                'time-in' => false,
                'time-out' => true,
                'lunch-start' => true,
                'lunch-end' => true,
                'overtime-start' => true,
                'overtime-end' => true,
                'lunch-optional' => false,
            ])
        ) || (
            (Timesheet::LUNCH_END == $type) &&
            $this->isNullClocks([
                'time-in' => false,
                'time-out' => true,
                'lunch-start' => false,
                'lunch-end' => true,
                'overtime-start' => true,
                'overtime-end' => true,
                'lunch-optional' => false,
            ])
        ) || (
            (Timesheet::OVERTIME_START == $type) &&
            $this->isNullClocks([
                'time-in' => false,
                'time-out' => false,
                'lunch-optional' => true,
                'overtime-start' => true,
                'overtime-end' => true,
            ])
        ) || (
            (Timesheet::OVERTIME_END == $type) &&
            $this->isNullClocks([
                'time-in' => false,
                'time-out' => false,
                'lunch-optional' => true,
                'overtime-start' => false,
                'overtime-end' => true,
            ])
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Punch Request!';
    }

    public function isNullClocks($rule) {
        $isLunchClockValid = false;
        if (
            $rule['lunch-optional'] && 
            (is_null($this->timesheet->lunch_start) === is_null($this->timesheet->lunch_end))
        ) {
            $isLunchClockValid = true;
        } else if (
            ($rule['lunch-optional'] === false) &&
            (is_null($this->timesheet->lunch_start) === $rule['lunch-start']) &&
            (is_null($this->timesheet->lunch_end) === $rule['lunch-end'])
        ) {
            $isLunchClockValid = true;
        }

        return 
            (is_null($this->timesheet->time_in) === $rule['time-in']) &&
            (is_null($this->timesheet->time_out) === $rule['time-out']) && 
            $isLunchClockValid &&
            (is_null($this->timesheet->overtime_start) === $rule['overtime-start']) &&
            (is_null($this->timesheet->overtime_end) === $rule['overtime-end']);
    }
}
