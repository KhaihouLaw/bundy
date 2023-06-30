<?php

namespace App\Http\Traits;
use App\Utilities\WorkCalculator;

trait HasClockRecords {

    public function getId()
    {
        return $this->id;
    }

    public function getClockIn($format = 'g:i A')
    {
        return self::formatTime($format, $this->time_in);
    }

    public function getClockOut($format = 'g:i A')
    {
        return self::formatTime($format, $this->time_out);
    }

    public function getLunchStart($format = 'g:i A')
    {
        return self::formatTime($format, $this->lunch_start);
    }

    public function getLunchEnd($format = 'g:i A')
    {
        return self::formatTime($format, $this->lunch_end);
    }

    public function getOvertimeStart($format = 'g:i A')
    {
        return self::formatTime($format, $this->overtime_start);
    }

    public function getOvertimeEnd($format = 'g:i A')
    {
        return self::formatTime($format, $this->overtime_end);
    }

    public function getTimesheetDate($format = 'Y-m-d')
    {
        return date($format, strtotime($this->timesheet_date));
    }

    public static function formatTime($format, $time)
    {
        if (!is_null($time)) {
            return date($format, strtotime($time));
        }
        return null;
    }

    /**
     * @param hoursSumAsDateTime date time Y-m-d H:i:s where work hours are added
     * @return false|Array
     */
    public function clockInTotalHours($isRegular, $hoursSumAsDateTime = null)
    {
        $clockInDiff = WorkCalculator::dateTimeDifference($this->time_in, $this->time_out);
        if ($clockInDiff) {
            if (is_null($hoursSumAsDateTime)) $hoursSumAsDateTime = date(self::DATE_TIME_FORMAT);
            // dont include lunch (12 nn - 1 pm) when work hours MORE THAN 6 hrs
            $clockInHrDiff = $clockInDiff->h;
            if ($isRegular && ($clockInHrDiff > 6)) {
                $clockInHrDiff -= 1;
            }
            $hoursSumAsDateTime = WorkCalculator::addDurationToDateTime(
                '+' . $clockInHrDiff . ' hour +' . $clockInDiff->i . ' minutes +' . $clockInDiff->s . ' seconds',
                $hoursSumAsDateTime
            );
            return [
                'total_hours' => [$clockInHrDiff, $clockInDiff->i, $clockInDiff->s],
                'sum_as_date_time' => $hoursSumAsDateTime,
            ];
        }
        return false;
    }

    public function overtimeTotalHours($hoursSumAsDateTime = null)
    {
        $overtimeDiff = WorkCalculator::dateTimeDifference($this->overtime_start, $this->overtime_end);
        if ($overtimeDiff) {
            if (is_null($hoursSumAsDateTime)) $hoursSumAsDateTime = date(self::DATE_TIME_FORMAT);
            $hoursSumAsDateTime = WorkCalculator::addDurationToDateTime(
                '+' . $overtimeDiff->h . ' hour +' . $overtimeDiff->i . ' minutes +' . $overtimeDiff->s . ' seconds',
                $hoursSumAsDateTime
            );
            return [
                'total_hours' => [$overtimeDiff->h, $overtimeDiff->i, $overtimeDiff->s],
                'sum_as_date_time' => $hoursSumAsDateTime,
            ];
        }
        return false;
    }
}