<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Timesheet;

class Schedule extends Model
{
    use HasFactory;
    
    const MONDAY = 'monday';
    const TUESDAY = 'tuesday';
    const WEDNESDAY = 'wednesday';
    const THURSDAY = 'thursday';
    const FRIDAY = 'friday';
    const SATURDAY = 'saturday';
    const SUNDAY = 'sunday';
    const WEEK_DAYS = [
        self::MONDAY,
        self::TUESDAY,
        self::WEDNESDAY,
        self::THURSDAY,
        self::FRIDAY,
        self::SATURDAY,
        self::SUNDAY,
    ];

    protected $fillable = [
        'employee_id',
        'employee_schedule_id',
        'day',
        'start_time',
        'end_time'
    ];

    public function timesheet()
    {
        return $this->hasOne(Timesheet::class);
    }

    public function employeeSchedule()
    {
        return $this->belongsTo(EmployeeSchedule::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function getStartTime($format = 'g:i A')
    {
        return self::formatTime($format, $this->start_time);
    }

    public function getEndTime($format = 'g:i A')
    {
        return self::formatTime($format, $this->end_time);
    }

    public static function formatTime($format, $time)
    {
        if (!is_null($time)) {
            return date($format, strtotime($time));
        }
        return null;
    }
}
