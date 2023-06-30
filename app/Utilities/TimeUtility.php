<?php

namespace App\Utilities;

class TimeUtility
{
    /**
     * @param array [timesheet, timesheet, ...]
     * dependents: timesheet modification email
     */
    public static function format12Hrs($timesheets) {
        return [
            'time-in' => self::getClocks($timesheets, 'getClockIn'),
            'time-out' => self::getClocks($timesheets, 'getClockOut'),
            'lunch-start' => self::getClocks($timesheets, 'getLunchStart'),
            'lunch-end' => self::getClocks($timesheets, 'getLunchEnd'),
            'overtime-start' => self::getClocks($timesheets, 'getOvertimeStart'),
            'overtime-end' => self::getClocks($timesheets, 'getOvertimeEnd'),
        ];
    }

    /**
     * dependents: attendance report
     */
    public static function format24Hrs($timesheets) {
        return [
            'time-in' => self::get24Hrs($timesheets, 'time_in'),
            'time-out' => self::get24Hrs($timesheets, 'time_out'),
            'lunch-start' => self::get24Hrs($timesheets, 'lunch_start'),
            'lunch-end' => self::get24Hrs($timesheets, 'lunch_end'),
            'overtime-start' => self::get24Hrs($timesheets, 'overtime_start'),
            'overtime-end' => self::get24Hrs($timesheets, 'overtime_end'),
        ];
    }

    /**
     * @param timesheets array [timesheet, timesheet, ...]
     * @param getClock string, timesheet method
     * @return array|string
     */
    public static function getClocks($timesheets, $get_clock) {
        $formatted = [];
        if (count($timesheets) === 1) {
            $formatted = $timesheets[0]->$get_clock();
        }
        else if (count($timesheets) > 1) {
            foreach ($timesheets as $timesheet) {
                array_push($formatted, $timesheet->$get_clock());
            }
        }
        return $formatted;
    }
    
    /**
     * 24 hour time format HH:MM
     */
    public static function get24Hrs($timesheets, $get_clock) {
        $formatted = [];
        if (count($timesheets) === 1) {
            $formatted = $timesheets[0]->$get_clock;
            if (!is_null($formatted)) {
                $formatted = explode(':', $formatted);
                $formatted = $formatted[0] . ':' . $formatted[1];
            }
        }
        else if (count($timesheets) > 1) {
            foreach ($timesheets as $timesheet) {
                $temp = $timesheet->$get_clock;
                if (!is_null($temp)) {
                    $temp = explode(":", $temp);
                    $temp = $temp[0] . ':' . $temp[1];
                }
                array_push($formatted, $temp);
            }
        }
        return $formatted;
    }

    /**
     * @return total_days between two dates (INCLUDED)
     */
    public static function getTotalDays($start_date, $end_date)
    {
        return abs(round((strtotime($start_date) - strtotime($end_date)) / 86400)) + 1;
    }

    /**
     * @param integer $hrs Hours
     * @param integer $mins Minutes
     * 
     * @return float $total_rounded in 2 decimals
     */
    public static function getTotalHrs($hrs, $mins)
    {
        $total_float = floatval($hrs) + (floatval($mins) / 60);
        $total_rounded = round($total_float, 2);
        return $total_rounded;
    }
}
