<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Department;
use Exception;
use Log;
use Auth;

class DepartmentController extends Controller
{
    protected $default_rules;
    
    function __construct()
    {
        $this->default_rules = [
            'department' => [
                'required',
                'string',
            ],
            'supervisor' => [
                'required',
                'email:rfc,dns',
                'exists:users,email',
            ],
            'approver' => [
                'required',
                'email:rfc,dns',
                'exists:users,email',
            ],
        ];
    }

    public function create(Request $request) 
    {
        try {
            $dept_data = $request->all();
            $dept_data = [
                'department' => $dept_data['department'],
                'supervisor' => $dept_data['supervisor'],
                'approver' => $dept_data['supervisor'],
            ];
            $validator = Validator::make($dept_data, $this->default_rules);
            $is_department_exist = !empty(Department::where('department', $dept_data['department'])->first());
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            } elseif ($is_department_exist) {
                throw new Exception('Department already exist!');
            }
            $created = Department::create($dept_data);
            if (!is_null($created)) {
                return response()->json($created);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'DepartmentController->create #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $dept_id = $request->dept_id;
            $dept_data = $request->dept_data;
            $dept_data = [
                'department' => $dept_data['department'] ?? 'Human Resource',
                'supervisor' => $dept_data['supervisor'],
                'approver' => $dept_data['approver'],
            ];
            $rules = [
                'department_id' => ['required', 'exists:departments,id'],
            ] + $this->default_rules;
            $validate = [
                'department_id' => $dept_id,
            ] + $dept_data;
            $validator = Validator::make($validate, $rules);
            $has_duplicate = Department::where('department', '=', $dept_data['department'])
                                       ->where('id', '!=', $dept_id)->get()->toArray();
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            } elseif (!empty($has_duplicate)) {
                throw new Exception('Department name must be unique!');
            }
            $department = Department::find($dept_id);
            $department_name = $department->department;
            if ($department_name === Department::HR_DEPT) {
                // restrict changing Human Resource name
                unset($dept_data['department']);
                $is_updated = $department->update($dept_data);
                if ($is_updated) return response()->json(['success' => true]);
            } else {
                $is_updated = $department->update($dept_data);
                if ($is_updated) return response()->json(['success' => true]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'DepartmentController->update #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $department = Department::find($request->dept_id);
            $department_name = $department->department;
            if (!empty($department) && !empty($password) && ($department_name !== Department::HR_DEPT)) {
                $adminUserPsswrd = Auth::user()->password;                
                if (Hash::check($password, $adminUserPsswrd)) {
                    foreach ($department->employees as $employee) {
                        $employee->tryDetachDepartmentRoles();
                    }
                    $is_deleted = $department->delete();
                    if ($is_deleted) {
                        return response()->json(['success' => true]);
                    }
                } else {
                    throw new Exception('Incorrect Password!');
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'DepartmentController->delete #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
