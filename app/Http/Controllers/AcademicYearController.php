<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Exception;
use Log;
use Auth;

class AcademicYearController extends Controller
{
    protected $default_rules;
    
    function __construct()
    {
        $this->default_rules = [
            'description' => [
                'required',
                'string',
            ],
            'semester' => [
                'required',
                'numeric',
                'gt:0'
            ],
            'start_year' => [
                'required',
                'date_format:Y',
                'before:end_year',
            ],
            'end_year' => [
                'required',
                'date_format:Y',
                'after:start_year',
            ],
            'start_date' => [
                'required',
                'date_format:Y-m-d',
                'before:end_date',
            ],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:start_date',
            ],
        ];
    }

    public function create(Request $request) 
    {
        try {
            $ay_data_arr = $request->all();
            $validator = Validator::make($ay_data_arr, $this->default_rules);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $result = AcademicYear::create($ay_data_arr);
            if (is_null($result)) {
                throw new Exception('Bad Request');
            }
            return response()->json($result);
        } catch (Exception $e) {
            $err_log = 'AcademicYearController->create #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $ay_id = $request->ay_id;
            $ay_data = $request->ay_data;
            $rules = [
                'academic_year_id' => ['required', 'exists:academic_years,id'],
            ] + $this->default_rules;
            $validate = [
                'academic_year_id' => $ay_id,
            ] + $ay_data;
            $validator = Validator::make($validate, $rules);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $is_updated = AcademicYear::find($ay_id)->update($ay_data);
            if($is_updated) {
                return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'AcademicYearController->update #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $password = $request->password;
            $ay = AcademicYear::find($request->ay_id);
            if (!empty($ay) && !empty($password)) {
                $adminUserPsswrd = Auth::user()->password;                
                if (Hash::check($password, $adminUserPsswrd)) {
                    $is_deleted = $ay->delete();
                    if($is_deleted) {
                        return response()->json(['success' => true]);
                    }
                } else {
                    throw new Exception('Incorrect Password!');
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'AcademicYearController->delete #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
