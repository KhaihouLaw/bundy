<?php

namespace App\Utilities;

use App\Models\Timesheet;

class WorkCalculator
{
    public static function sumOfWorkHours($timesheets, $is_regular = false, $test = false) {
        $result = [];
        $sum_of_hours_date_time = date(Timesheet::DATE_TIME_FORMAT); 
        foreach ($timesheets as $timesheet) {
            $punch_duration = [];
            $day_total_hours = null;
            $clock_in_duration = $timesheet->clockInTotalHours($is_regular, $sum_of_hours_date_time);
            if ($clock_in_duration) {
                $sum_of_hours_date_time = $clock_in_duration['sum_as_date_time'];
                $day_total_hours = $clock_in_duration['total_hours'];
                // only total hours for unit test
                if(!$test) $punch_duration['clock_in'] = $clock_in_duration;
                else $punch_duration['clock_in']['total_hours'] = $clock_in_duration['total_hours'];
            }
            $overtime_duration = $timesheet->overtimeTotalHours($sum_of_hours_date_time);
            if ($overtime_duration) {
                if (!is_null($day_total_hours)) {
                    list($clock_in_hrs, $clock_in_mins, $clock_in_secs) = $day_total_hours;
                    list($ot_hrs, $ot_mins, $ot_secs) = $overtime_duration['total_hours'];
                    $total_hrs = (int)$clock_in_hrs + (int)$ot_hrs;
                    $total_mins = (int)$clock_in_mins + (int)$ot_mins;
                    // $totalSecs = (int)$clock_in_secs + (int)$ot_secs;
                    if ($total_mins > 60) {
                        $total_hrs += 1;
                        $total_mins -= 60;
                    }
                    $day_total_hours = [$total_hrs, $total_mins];
                }
                $sum_of_hours_date_time = $overtime_duration['sum_as_date_time'];
                // only total hours for unit test
                if(!$test) $punch_duration['overtime'] = $overtime_duration;
                else $punch_duration['overtime']['total_hours'] = $overtime_duration['total_hours'];
            }
            $result[$timesheet['timesheet_date']] = $punch_duration;
            // no need for unit test
            if (!$test) {
                $result[$timesheet['timesheet_date']]['day_total_hrs'] = $day_total_hours;
                $result[$timesheet['timesheet_date']]['timesheet'] = $timesheet;
            }
        }
        $total_hours = self::dateTimeIntervalToHours(date(Timesheet::DATE_TIME_FORMAT), $sum_of_hours_date_time);
        $result['overall_total_hours'] = $total_hours;
        return $result;
    }

    public static function addDurationToDateTime($duration, $date_time) {
        return date(
            Timesheet::DATE_TIME_FORMAT,
            strtotime(
                $duration,
                strtotime($date_time)
            )
        );
    }

    /**
     * @param string Y-m-d H:i:s
     * @return false|DateInterval total year, month, days... no leading zeros
     */
    public static function dateTimeDifference($start_time, $end_time) {
        if (is_null($start_time) || is_null($end_time)) return false;
        $time_in = date_create($start_time);
        $time_out = date_create($end_time);
        $diff = date_diff($time_out, $time_in);
        return $diff;
    }

    /**
     * @param string Y-m-d H:i:s
     * @return false|String total hours:mins:secs no leading zeros
     */
    public static function dateTimeIntervalToHours($start_date_time, $end_date_time) {
        $dt_diff = self::dateTimeDifference($start_date_time, $end_date_time);
        if ($dt_diff) {
            $total_hours = ($dt_diff->days * 24) + $dt_diff->h;
            return [$total_hours, $dt_diff->i, $dt_diff->s];
        }
        return false;
    }
}
