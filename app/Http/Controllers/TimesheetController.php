<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Timesheet;
use App\Models\TimesheetModificationRequest;
use App\Models\Schedule;
use App\Rules\PunchType;
use App\Utilities\CustomLogUtility;
use App\Utilities\EmailUtility;
use App\Utilities\TimeUtility;
use Auth;
use Log;
use Exception;

class TimesheetController extends Controller
{
    protected static $validation_attr_names = [
        'time_in' => 'Time In',
        'time_out' => 'Time Out',
        'lunch_start' => 'Lunch Start',
        'lunch_end' => 'Lunch End',
        'overtime_start' => 'Overtime Start',
        'overtime_end' => 'Overtime End',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'timesheets' => 'Timesheet',
    ];
    protected $default_rules;
    
    function __construct()
    {
        $this->default_rules = [
            'time_in' => [
                'required_with:time_out', 
                'nullable', 
                'date_format:H:i', 
                'before:time_out',
                'before:lunch_start',
                'before:overtime_start',
            ],
            'time_out' => [
                'required_with:time_in', 
                'nullable', 
                'date_format:H:i',
            ],
            'lunch_start' => [
                'required_with:lunch_end', 
                'nullable', 
                'date_format:H:i',
                'before:time_out',
                'before:lunch_end',
            ],
            'lunch_end' => [
                'required_with:lunch_start', 
                'nullable', 
                'date_format:H:i',
                'before:time_out',
                'before:overtime_start',
            ],
            'overtime_start' => [
                'required_with:overtime_end', 
                'nullable', 
                'date_format:H:i',
                'after:time_out',
                'before:overtime_end',
            ],
            'overtime_end' => [
                'required_with:overtime_start', 
                'nullable', 
                'date_format:H:i'
            ],
        ];
    }

    public function index()
    {
        return view('user.timesheet');
    }

    public function bundy()
    {
        $timesheet = Timesheet::where([
            ['employee_id', '=', Auth::user()->employee_id],
            ['timesheet_date', '=', date('Y-m-d')]
        ])->first();

        return view('bundy', [
            'timesheet' => $timesheet
        ]);
    }

    public function getEmployeeTimesheet(Request $request) 
    {
        $current_user = $request->get('currentUser');
        $timesheet = Timesheet::where([
            ['employee_id', '=', $current_user->employee_id],
            ['timesheet_date', '=', date('Y-m-d')]
        ])->first();

        return response()->json(['data' => $timesheet]);
    }

    public function punch(Request $request)
    {
        $is_mobile_app = $request->get('isMobileApp');
        $type_validator = Validator::make($request->all(), [
            'type' => [new PunchType($request->schedule_id, Auth::user()->employee_id)]
        ]);
        if ($type_validator->fails()) {
            return response()->json([
                'error' => 'Invalid Punch Request!'
            ], 400);
        }
        if ($request->has('undertime_notes') && ($request->type != Timesheet::TIME_OUT)) {
            return response()->json([
                'error' => 'Invalid Undertime Request!'
            ], 400);
        }
        $timesheet = Timesheet::getByScheduleId($request->schedule_id);
        $punch = $timesheet->punch($request->type);
        // Save undertime notes
        if ($request->has('undertime_notes')) {
            $timesheet->saveUndertimeNotes($request->undertime_notes);
        }
        // Save GeoLocation if existing
        if (($request->has('location') && $request->type == Timesheet::TIME_IN)
            || (
                $request->has('location')
                && $request->type == Timesheet::TIME_OUT
                && ($timesheet->location == '{"lat": 0, "long": 0}' || is_null($timesheet->location))
                && $request->location != '{"lat": 0, "long": 0}')
        ) {
            $timesheet->saveLocation($request->location);
        }

        if ($is_mobile_app) {
            return response()->json(['data' => $punch]);
        } else {
            return $punch;
        }
        
    }

    public function getTimesheet($timesheet_id)
    {
        $timesheet = Timesheet::find($timesheet_id);
        return response()->json($timesheet->toArray());
    }

    public function getTimesheetByScheduleId($schedule_id)
    {
        $timesheet = Timesheet::getByScheduleId($schedule_id);
        return response()->json($timesheet->toArray());
    }

    public function updateTimesheet(Request $request)
    {
        $timesheet = Timesheet::getByScheduleId($request->timesheet_id);
        $timesheet->updateTimesheet(
            $time_in = $request->time_in,
            $time_out = $request->time_out,
            $lunch_start = $request->lunch_start,
            $lunch_end = $request->lunch_end,
            $overtime_start = $request->overtime_start,
            $overtime_end = $request->overtime_end
        );
        if ($timesheet->save()) {
            return response()->json($timesheet->toArray());
        }
        return false;
    }

    public function getTimesheetModificationRequest($request_id)
    {
        $modification_request = TimesheetModificationRequest::find($request_id);
        return response()->json($modification_request->toArray());
    }

    public function approveTimesheetModificationRequest(Request $request)
    {
        try {
            $modification_request = TimesheetModificationRequest::find($request->request_id);
            if (empty($modification_request) || !($modification_request->isPending())) {
                throw new Exception('Bad Request!');
            }
            $result = $modification_request->approve();
            EmailUtility::sendTimesheetModificationStatusNotif([
                'requestor' => [
                    'subject' => 'Your Timesheet Modification Request is Approved',
                    'from-label' => 'Approved by:',
                ],
                'reviewer' => [
                    'subject' => 'You Approved Timesheet Modification Request',
                ]
            ], $modification_request);
            return response()->json([
                'success' => $result
            ]);
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function rejectTimesheetModificationRequest(Request $request)
    {
        try {
            $modification_request = TimesheetModificationRequest::find($request->request_id);
            if (empty($modification_request) || !($modification_request->isPending())) {
                throw new Exception('Bad Request!');
            }
            $result = $modification_request->reject();
            EmailUtility::sendTimesheetModificationStatusNotif([
                'requestor' => [
                    'subject' => 'Your Timesheet Modification Request is Rejected',
                    'from-label' => 'Rejected by:',
                ],
                'reviewer' => [
                    'subject' => 'You Rejected Timesheet Modification Request',
                ]
            ], $modification_request);
            return response()->json([
                'success' => $result
            ]);    
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function cancelTimesheetModificationRequest(Request $request)
    {
        try {
            $modification_request = TimesheetModificationRequest::find($request->request_id);
            if (empty($modification_request) || !($modification_request->isPending())) {
                throw new Exception('Bad Request!');
            }
            $result = $modification_request->cancel();
            EmailUtility::sendTimesheetModificationStatusNotif([
                'requestor' => [
                    'subject' => 'You Cancelled Your Timesheet Modification Request',
                ],
                'reviewer' => [
                    'subject' => 'A Timesheet Modification Request is Cancelled',
                ]
            ], $modification_request);
            return response()->json([
                'success' => $result
            ]);
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function getEmployeeTimesheets() 
    {
        // only get timesheet of today and previous days
        $timesheets_with_date_as_key = Timesheet::useDatesAsKeys(Auth::user()->employee_id);
        $timesheets_grouped_by_month = Timesheet::groupByMonth(Auth::user()->employee_id);
        return response()->json([
            'by-date' => $timesheets_with_date_as_key, 
            'by-month' => $timesheets_grouped_by_month
        ]);
    }

    public function getEmployeeTimesheetsMobile(Request $request) 
    {
        $day = $request->get('day');
        $week_start = $request->get('week_start');
        $week_end = $request->get('week_end');
        $month = $request->get('month');
        $year = $request->get('year');
        $current_user = $request->get('currentUser');

        $timesheets = Timesheet::where('employee_id', $current_user->employee_id)
                            ->when($day, function($query) use ($day) {
                                $query->where('timesheet_date', $day);
                            })
                            ->when($week_start && $week_end, function($query) use ($week_start, $week_end) {
                                $query->whereBetween('timesheet_date', [$week_start, $week_end]);
                            })
                            ->when($month, function($query) use ($month, $year) {
                                $query->whereMonth('timesheet_date', $month)
                                    ->whereYear('timesheet_date', $year);
                            })
                            ->whereDate('timesheet_date', '<=', date('Y-m-d'))
                            ->with(['modification'])
                            ->get();

        return response()->json(['data' => $timesheets]);
    }

    public function modifyTimesheet(Request $request)
    {
        try {
            if (is_null($request->timesheet_id)) {
                throw new Exception('Invalid timesheet record');
            }
            if (TimesheetModificationRequest::isModified($request->timesheet_id) && $request->modification_type == 'add') {
                throw new Exception('Timesheet is already modified');
            }
            $modified = false;
            $modified_timesheet = null;
            // 24 hrs format
            $time_in = ($request->has("time_in") && $request->time_in != '--:-- --') ? $request->time_in : null;
            $time_out = ($request->has("time_out") && $request->time_out != '--:-- --') ? $request->time_out : null;
            $lunch_start = ($request->has("lunch_start") && $request->lunch_start != '--:-- --') ? $request->lunch_start : null;
            $lunch_end = ($request->has("lunch_end") && $request->lunch_end != '--:-- --') ? $request->lunch_end : null;
            $overtime_start = ($request->has("overtime_start") && $request->overtime_start != '--:-- --') ? $request->overtime_start : null;
            $overtime_end = ($request->has("overtime_end") && $request->overtime_end != '--:-- --') ? $request->overtime_end : null;
            $notes = null;
            if ($request->has('notes')) {
                $notes = trim(strip_tags($request->notes));
            }
            $modifications = [
                'timesheet_date' => $request->timesheet_date,
                'time_in' => $time_in,
                'time_out' => $time_out,
                'lunch_start' => $lunch_start,
                'lunch_end' => $lunch_end,
                'overtime_start' => $overtime_start,
                'overtime_end' => $overtime_end,
                'status' => 'pending',
                'notes' => $notes
            ];
            // As of July 18, "Add Timesheet Adjustment Request" has been disabled
            //      There will only be an "Edit Timesheet" request
            //      "Edit Timesheet Method" is RENAMED AS "requestTimesheetAdjustment"
            // $subject = 'Add Timesheet Request';
            // if ($request->modification_type === "add") {
            //     $modified = $this->addTimesheet($request->timesheet_id, $modifications);
            // } else if ($request->modification_type === "edit") {
            //     $subject = 'Edit Timesheet Request';
            //     $modified = $this->editTimesheet($request->timesheet_id, $modifications);
            // }
            $subject = 'Timesheet Adjustment Request';
            $modified = $this->requestTimesheetAdjustment($request->timesheet_id, $modifications);
            if (!$modified) throw new Exception('Timesheet adjustment failed');
            $modifications = $modified['modifications'];
            $modified_timesheet = $modified['modified-timesheet'];
            // send email to HR / reviewer
            $reviewer_email = $modified_timesheet->employee->department->getApprover()->email;
            EmailUtility::sendTimesheetModificationEmail([
                'type' => $request->modification_type,
                'subject' => $subject,
                'from' => ['From:', Auth::user()->name],
                'email' => Auth::user()->email,
                'recipient' => $reviewer_email,
                'cc' => env('HR_EMAIL')
            ], $modifications, $modified_timesheet);
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function requestTimesheetAdjustment($timesheet_id, $modifications)
    {
        $timesheet = Timesheet::find($timesheet_id);
        $user = Auth::user();
        if (is_null($user)) return false;
        $employee_id = $user->employee->getId();
        $modified_timesheet = TimesheetModificationRequest::updateOrCreate([
            'employee_id' => $employee_id,
            'timesheet_id' => $timesheet_id,
            'employee_schedule_id' => $timesheet->getEmployeeScheduleId()
        ], $modifications);
        $actual_timesheet = $modified_timesheet->timesheet;
        if (($modified_timesheet->count() === 0) || ($actual_timesheet->count() === 0)) return false;
        return [
            'modifications' => TimeUtility::format12Hrs([$actual_timesheet, $modified_timesheet]),
            'modified-timesheet' => $modified_timesheet
        ];
    }

    public function getEmployeeTimesheetModificationRequests()
    {
        $date_as_key = [];
        $modification_requests = TimesheetModificationRequest::
            where('employee_id', Auth::user()->employee_id)->get()->toArray();
        foreach ($modification_requests as $request) {
            $date_as_key[$request['timesheet_date']] = $request;
        }
        return response()->json($date_as_key);
    }


    /**
     * ====================================== Timesheet Management [START] ======================================
     */

    public function update(Request $request)
    {
        try {
            $timesheet_data = $request->timesheet_data;
            $timesheet_id = $request->timesheet_id;
            $validate = $timesheet_data + ['timesheet_id' => $timesheet_id];
            $rules = [
                'timesheet_id' => ['required', 'exists:timesheets,id'],
            ] + $this->default_rules;
            $validator = Validator::make($validate, $rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $timesheet = Timesheet::find($timesheet_id);
            if (!is_null($timesheet)) {
                $is_updated = $timesheet->update($request->timesheet_data);
                if ($is_updated) return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $timesheet = Timesheet::find($request->timesheet_id);
            if (!is_null($timesheet)) {
                $is_deleted = $timesheet->delete();
                if ($is_deleted) return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $all_data_array = $request->all();
            $rules = [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'timesheets' => ['required'],
            ] + $this->default_rules;
            $validator = Validator::make($all_data_array, $rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            foreach ($request->timesheets as $day => $timesheet) {
                $timesheet_rules = [
                    'schedule_id' => ['required', 'exists:schedules,id'],
                ] + $this->default_rules;
                $validator = Validator::make($timesheet, $timesheet_rules, [], self::$validation_attr_names);
                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }
            }
            $start_date = strtotime($request->start_date);
            $end_date = strtotime($request->end_date);
            $timesheets_created = [];

            foreach ($request->timesheets as $day => $timesheet) {
                $timesheet = (object)$timesheet;
                $schedule = Schedule::find($timesheet->schedule_id);
                if (!empty($schedule)) {
                    for (
                        $date = strtotime(ucfirst($day), $start_date);
                        $date <= $end_date;
                        $date = strtotime('+1 week', $date)
                    ) {
                        $timesheet_created = Timesheet::create([
                            'employee_id' => $schedule->employee_id,
                            'employee_schedule_id' => $schedule->employee_schedule_id,
                            'schedule_id' => $schedule->id,
                            'timesheet_date' => date('Y-m-d', $date),
                            'time_in' => $timesheet->time_in,
                            'time_out' => $timesheet->time_out,
                            'lunch_start' => $timesheet->lunch_start,
                            'lunch_end' => $timesheet->lunch_end,
                            'overtime_start' => $timesheet->overtime_start,
                            'overtime_end' => $timesheet->overtime_end,
                            'location' => null,
                            'has_undertime' => 0,
                            'undertime_notes' => null,
                        ]);
                        if (!empty($timesheet_created)) {
                            $result = [
                                'id' => $timesheet_created->id,
                                'date' => $timesheet_created->timesheet_date,
                                'day' => date('l', strtotime($timesheet_created->timesheet_date)),
                                'time_in' => [$timesheet_created->time_in ?? '', $timesheet_created->getClockIn() ?? ''],
                                'time_out' => [$timesheet_created->time_out ?? '', $timesheet_created->getClockOut() ?? ''],
                                'lunch_start' => [$timesheet_created->lunch_start ?? '', $timesheet_created->getLunchStart() ?? ''],
                                'lunch_end' => [$timesheet_created->lunch_end ?? '', $timesheet_created->getLunchEnd() ?? ''],
                                'overtime_start' => [$timesheet_created->overtime_start ?? '', $timesheet_created->getOvertimeStart() ?? ''],
                                'overtime_end' => [$timesheet_created->overtime_end ?? '', $timesheet_created->getOvertimeEnd() ?? ''],
                                'created_at' => date('F j, Y, h:i:s A', strtotime($timesheet_created->created_at)),
                                'updated_at' => date('F j, Y, h:i:s A', strtotime($timesheet_created->updated_at)),
                            ];
                            array_push($timesheets_created, $result)                        ;
                        }
                    }
                }
            }
            return response()->json($timesheets_created);
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }

    public function createForEmployees(Request $request)
    {
        try {
            $all_data_array = $request->all();
            $rules = [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'timesheets' => ['required'],
                'employees' => ['required'],
            ];
            $validator = Validator::make($all_data_array, $rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            foreach ($request->timesheets as $day => $timesheet) {
                $validator = Validator::make($timesheet, $this->default_rules, [], self::$validation_attr_names);
                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }
            }
            $employees = [];
            foreach ($request->employees as $employee_id) {
                $emp = Employee::find($employee_id);
                if (empty($emp)) {
                    throw new Exception("Employee doesn't exist!");
                } else {
                    array_push($employees, $emp);
                }
            }
            $start_date = strtotime($request->start_date);
            $end_date = strtotime($request->end_date);
            $failed = [];
            foreach ($employees as $employee) {
                $failed[$employee->getFullName()] = [];
                foreach ($request->timesheets as $day => $timesheet) {
                    $timesheet = (object)$timesheet;
                    $day_schedule = $employee->workSchedules->where('day', ucfirst($day))->first();
                    if (!empty($day_schedule)) {
                        for (
                            $date = strtotime(ucfirst($day), $start_date);
                            $date <= $end_date;
                            $date = strtotime('+1 week', $date)
                        ) {
                            $timesheet_date = date('Y-m-d', $date);
                            $date_has_timesheet = $employee->timesheets->where('timesheet_date', $timesheet_date)->count();
                            if(empty($date_has_timesheet)) {   
                                Timesheet::create([
                                    'employee_id' => $employee->id,
                                    'employee_schedule_id' => $day_schedule->employee_schedule_id,
                                    'schedule_id' => $day_schedule->id,
                                    'timesheet_date' => $timesheet_date,
                                    'time_in' => $timesheet->time_in,
                                    'time_out' => $timesheet->time_out,
                                    'lunch_start' => $timesheet->lunch_start,
                                    'lunch_end' => $timesheet->lunch_end,
                                    'overtime_start' => $timesheet->overtime_start,
                                    'overtime_end' => $timesheet->overtime_end,
                                    'location' => null,
                                    'has_undertime' => 0,
                                    'undertime_notes' => null,
                                ]);
                            }
                        }
                    } else {
                        array_push($failed[$employee->getFullName()], $day);
                    }
                }
            }
            return response()->json($failed);
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }
    /**
     * ====================================== Timesheet Management [END] ======================================
     */


    /**
     * @param request array[user_ids]
     */
    public function punchByIds(Request $request) {
        $usersStatus = [];
        // check if it's pass 4 PM
        $punchType = (int)date('G') < 17 ? Timesheet::TIME_IN : Timesheet::TIME_OUT;
        $user_ids = $request->user_ids;
        foreach ($user_ids as $key => $user_id) {
            $failed = true;
            $ignore = false; // ignore face when already clocked in
            $user = User::find($user_id); // @remind check if user exist first
            $employee = $user->employee;
            $timesheet = Timesheet::where([ // @remind create method for this, redundant in: bundy getEmployeeTimesheet
                ['employee_id', '=', $employee->id],
                ['timesheet_date', '=', date('Y-m-d')]
            ])->first();
            if (!is_null($timesheet)) {
                $typeValidator = Validator::make(['type' => $punchType], [
                    'type' => [new PunchType($timesheet->schedule_id, $employee->id)]
                ]);
                if (!$typeValidator->fails()) {
                    $punch = $timesheet->punch($punchType);
                    if ($punch) {
                        array_push($usersStatus, [
                            'user_id' => $user_id,
                            'user_name' => $employee->getFullName(),
                            'success' => true,
                            'punch_type' => $punchType,
                            'clock' => $punch,
                        ]);
                        $failed = false;
                    }
                } else {
                    $ignore = true;
                }
            }
            if ($failed && !$ignore) {
                array_push($usersStatus, [
                    'user_id' => $user_id,
                    'user_name' => $employee->getFullName(),
                    'punch_type' => $punchType,
                    'success' => false,
                ]);
            }
        }
        return response()->json(['data' => $usersStatus]);
    }

    /**
     * =================================================================================================================================
     * November 11, 2021
     * =================================================================================================================================
     */

    /**
     * @return view
     */
    public function compareChangesToOriginal($request_id, $option = 1)
    {
        try {
            $timesheet_adjustment_model = TimesheetModificationRequest::find($request_id);
            if (empty($timesheet_adjustment_model)) throw new Exception('Bad Request!');
            if ($option == 1) {
                return view(
                    'user.advance.supervisor.components.timesheet-adjustment-comparison', 
                    compact('timesheet_adjustment_model')
                );
            } elseif ($option == 2) {
                return view(
                    'admin.components.timesheet-modification-comparison', 
                    compact('timesheet_adjustment_model')
                );
            }
        } catch (Exception $e) {
            return CustomLogUtility::error(Auth::user()->id, __METHOD__, $e->getMessage());
        }
    }
}
