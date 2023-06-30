<?php

namespace App\Http\Controllers;

use App\Models\AdvancePunchClock;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Auth;
use Log;
use Exception;

class AdvancePunchClockController extends Controller
{
    protected static $validation_attr_names = [
        'type' => 'Punch Type',
        'description' => 'Description',
        'access_code' => 'Access Code',
        'day' => 'Day',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
    ];
    protected $scheds_rules;
    protected $id_rules = [
        'apc_id' => 'exists:advance_punch_clocks,id',
    ];
    protected $create_rules = [
        'type' => 'required|string|in:' . Timesheet::TIME_IN . ',' . Timesheet::TIME_OUT,
        'description' => 'required|string',
        'access_code' => 'required|string',
    ];
    protected $update_rules;
    
    function __construct()
    {
        $this->scheds_rules = [
            'day' => [
                'required',
                Rule::in(AdvancePunchClock::DAYS),
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
        $this->update_rules = $this->id_rules + $this->create_rules;
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->create_rules, [], self::$validation_attr_names);
            $access_code_exist = AdvancePunchClock::where('access_code', $request->access_code)->first();
            $schedules = $request->schedules;
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            } elseif ($access_code_exist) {
                throw new Exception('Access Code must be unique!');
            } elseif (empty($schedules)) {
                throw new Exception('Bad Request!');
            }
            foreach ($schedules as $day => $sched_time) {
                $day_sched = [
                    'day' => $day,
                    'start_time' => $sched_time['start_time'] ?? null,
                    'end_time' => $sched_time['end_time'] ?? null,
                ];
                $validator = Validator::make($day_sched, $this->scheds_rules, [], self::$validation_attr_names);
                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }
            }
            $created = AdvancePunchClock::create([
                'type' => $request->type,
                'description' => $request->description,
                'access_code' => $request->access_code,
                'schedules' => $schedules,
            ]);
            if (empty($created)) throw new Exception('Something went wrong');
            return response()->json($created);
        } catch (\Exception $e) {
            $err_log = 'AdvancePunchClockController->create #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            $apc_id = $request->apc_id;
            $apc_data = $request->apc_data;
            $access_code_not_unique = AdvancePunchClock::where('access_code', $request->access_code)
                                                        ->where('id', '!=', $apc_id)
                                                        ->first();
            $validator = Validator::make($apc_data, $this->update_rules, [], self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            } elseif ($access_code_not_unique) {
                throw new Exception('Access Code must be unique!');
            } elseif (empty($apc_data) || empty($apc_data['schedules'])) {
                throw new Exception('Bad Request!');
            }
            foreach ($apc_data['schedules'] as $day => $sched_time) {
                $day_sched = [
                    'day' => $day,
                    'start_time' => $sched_time['start_time'] ?? null,
                    'end_time' => $sched_time['end_time'] ?? null,
                ];
                $validator = Validator::make($day_sched, $this->scheds_rules, [], self::$validation_attr_names);
                if ($validator->fails()) {
                    throw new Exception($validator->errors()->first());
                }
            }
            $updated = AdvancePunchClock::find($apc_id)->update($apc_data);
            if (empty($updated)) return throw new Exception('Something went wrong');
            return response()->json($apc_data);
        } catch (\Exception $e) {
            $err_log = 'AdvancePunchClockController->update #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    

    public function delete(Request $request)
    {
        try {
            $apc = AdvancePunchClock::find($request->apc_id);
            if (!is_null($apc)) {
                $is_deleted = $apc->delete();
                if ($is_deleted) {
                    return response()->json(['success' => true]);
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'AdvancePunchClockController->delete #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
