<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Timesheet;
use App\Models\Schedule;
use App\Models\EmployeeSchedule;
use App\Utilities\CustomLogUtility;
use Illuminate\Http\Request;
use Exception;
use Log;
use Auth;


class ScheduleController extends Controller
{
    const DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];
    protected static $validation_attr_names = [
        'employee_id' => 'Employee ID',
        'academic_year_id' => 'Academic Year ID',
        'schedule_id' => 'Schedule ID',
        'day' => 'Day',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
    ];
    protected $default_scheds_rules;
    protected $default_ids_rules;
    
    function __construct()
    {
        $this->default_ids_rules = [
            'employee_id' => 'exists:employees,id',
            'academic_year_id' => 'exists:academic_years,id',
        ];
        $this->default_scheds_rules = [
            'day' => [
                'required',
                Rule::in(self::DAYS),
            ],
            'start_time' => [
                'required', 
                'date_format:H:i', 
                'before:end_time',
            ],
            'end_time' => [
                'required', 
                'date_format:H:i',
                'after:start_time',
            ],
        ];
    }

    public function create(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), $this->default_ids_rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $employee_id = $request->employee_id;
            $acad_yr_id = $request->academic_year_id;
            $period = $request->period;
            $schedules = $request->schedules;
            $generate_timesheet = $request->generate_timesheet;
            $employee_sched = EmployeeSchedule::firstOrCreate([
                'employee_id' => $employee_id,
                'academic_year_id' => $acad_yr_id,
                'period' => $period,
            ]);
            if (!is_null($employee_sched)) {
                $result = [];
                foreach ($schedules as $day => $sched_time) {
                    $day_sched = [
                        'day' => $day,
                        'start_time' => $sched_time['start_time'],
                        'end_time' => $sched_time['end_time'],
                    ];
                    $validator = Validator::make($day_sched, $this->default_scheds_rules, [], self::$validation_attr_names);
                    if ($validator->fails()) {
                        throw new Exception($validator->errors()->first());
                    }
                    $schedule = Schedule::create([
                        'employee_id' => $employee_sched->employee_id,
                        'employee_schedule_id' => $employee_sched->id,
                    ] + $day_sched);
                    if (!is_null($schedule)) {
                        $ay_start_yr = $schedule->employeeSchedule->academicYear->start_year;
                        $ay_end_yr = $schedule->employeeSchedule->academicYear->end_year;
                        $ay_start_date = $schedule->employeeSchedule->academicYear->start_date;
                        $ay_end_date = $schedule->employeeSchedule->academicYear->end_date;
                        $ay_semester = $schedule->employeeSchedule->academicYear->semester;
                        $ay_period = $schedule->employeeSchedule->period;
                        $schedule_data = [
                            'id' => $schedule->id,
                            'day' => $schedule->day,
                            'start_time' => [$schedule->start_time, $schedule->getStartTime()],
                            'end_time' => [$schedule->end_time, $schedule->getEndTime()],
                            'ay_semester' => 'A. Y. ' . $ay_start_yr . '-' . $ay_end_yr . ' Semester ' . $ay_semester,
                            'ay_start_date' => $ay_start_date,
                            'ay_end_date' => $ay_end_date,
                            'ay_period' => $ay_period,
                            'created_at' => date('F j, Y, h:i:s A', strtotime($schedule->created_at)),
                            'updated_at' => date('F j, Y, h:i:s A', strtotime($schedule->updated_at)),
                        ];
                        array_push($result, $schedule_data);
                        if ($generate_timesheet) Timesheet::generateTimesheet($employee_id, $employee_sched->id, $schedule->id);
                    }
                }
                return response()->json($result);
            }
            throw new Exception('Bad Request!');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $schedule_id = $request->schedule_id;
            $schedule_data = $request->schedule_data;
            $rules = [
                'schedule_id' => ['required', 'exists:schedules,id'],
            ] + $this->default_scheds_rules;
            $validate = [
                'schedule_id' => $schedule_id,
            ] + $schedule_data;
            $validator = Validator::make($validate, $rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $is_updated = Schedule::find($schedule_id)->update($schedule_data);
            if ($is_updated) {
                return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $schedule = Schedule::find($request->schedule_id);
            if (!is_null($schedule)) {
                $employee_schedule = $schedule->employeeSchedule;
                $schedules_belong_to_employee_schedule = Schedule::where('employee_schedule_id', $employee_schedule->id)->get();
                $is_deleted = false;
                if (count($schedules_belong_to_employee_schedule) == 1) {
                    $is_deleted = $employee_schedule->delete();
                } else {
                    $is_deleted = $schedule->delete();
                }
                if ($is_deleted) {
                    return response()->json(['success' => true]);
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }
}
