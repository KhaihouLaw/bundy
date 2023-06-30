<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'semester',
        'start_year',
        'end_year',
        'start_date',
        'end_date',
    ];

    public function employeeSchedule()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStartYear()
    {
        return $this->start_year;
    }

    public function getEndYear()
    {
        return $this->end_year;
    }

    public function getStartDate($is_carbon_date = false)
    {
        if ($is_carbon_date) {
            return Carbon::parse($this->start_date);
        }
        return $this->start_date;
    }

    public function getEndDate($is_carbon_date = false)
    {
        if ($is_carbon_date) {
            return Carbon::parse($this->end_date);
        }
        return $this->end_date;
    }

    public function add(
        $description,
        $start_year,
        $end_year,
        $start_date,
        $end_date
    )
    {
        if (empty($start_year)) {
            return false;
        }
        if (empty($end_year)) {
            return false;
        }
        $obj = new static;
        $obj->description = $description;
        $obj->start_year = $start_year;
        $obj->end_year = $end_year;
        $obj->start_date = $start_date;
        $obj->end_date = $end_date;
        if ($obj->save()) {
            return $obj;
        }
        return false;
    }

    public static function addEmployeeToAcademicPeriod(
        $employee_id,
        $academic_year_id,
        $period
    )
    {
        $obj = new EmployeeSchedule;
        $obj->employee_id = $employee_id;
        $obj->academic_year_id = $academic_year_id;
        $obj->period = $period;
        if ($obj->save()) {
            return $obj;
        }
        return false;
    }

    public static function getCurrentAcademicYear() {
        $year = date("Y");
        return static::where('start_year', "<=", $year)
                    ->where('end_year', ">=", $year + 1)
                    ->first();
    }
}
