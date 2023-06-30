<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Rules\AllowedDomains;
use Exception;
use Auth;
use Log;

class EmployeeController extends Controller
{
    protected static $validation_attr_names = [
        'emp_data.first_name' => 'first name',
        'emp_data.middle_name' => 'middle name',
        'emp_data.last_name' => 'last name',
        'emp_data.employment_type' => 'employment type',
        'emp_data.department_id' => 'department id',
        'emp_data.position_id' => 'position id',
        'emp_data.birthdate' => 'birth date',
        'emp_data.sick_leave' => 'sick leave credit points',
        'emp_data.vacation_leave' => 'vacation leave credit points',
        'usr_data.email' => 'email',
        'usr_data.password' => 'password',
    ];
    protected $default_required_rules;
    
    function __construct()
    {
        $this->default_required_rules = [
            'emp_data.first_name' => ['required', 'string'],
            'emp_data.middle_name' => ['required', 'string'],
            'emp_data.last_name' => ['required', 'string'],
            'emp_data.employment_type' => ['required', Rule::in(Employee::$employment_types)],
            'emp_data.department_id' => ['required', 'exists:departments,id'],
            'emp_data.position_id' => ['required', 'exists:positions,id'],
            'emp_data.birthdate' => ['nullable', 'date_format:Y-m-d'],
            'emp_data.sick_leave' => ['required', 'numeric', 'gte:0'],
            'emp_data.vacation_leave' => ['required', 'numeric', 'gte:0'],
            'usr_data.email' => ['required', 'email:rfc,dns', 'unique:users,email', new AllowedDomains],
            'usr_data.password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Creates both Employee and User
     * @param request {emp_data: {}, usr_data:{}}
     */
    public function create(Request $request) 
    {
        try {
            $all_data_array = $request->all();
            $employee_data = $request->emp_data;
            $user_data = $request->usr_data;
            $validator = Validator::make($all_data_array, $this->default_required_rules);
            $validator->setAttributeNames(self::$validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            $department = Department::find($employee_data['department_id']);
            $employee_data['approver_id'] = $department->getApprover()->employee->id;
            $created_employee = Employee::create($employee_data);
            if (!is_null($created_employee)) {
                $email = $user_data['email'];
                $password = Hash::make($user_data['password']);
                $employee_id = $created_employee->id;
                $name = $created_employee->getName();
                $user_data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'employee_id' => $employee_id,
                ];
                $created_user = User::create($user_data);
                if (!is_null($created_user)) {
                    $response_data = [
                        'id' => $created_employee->id,
                        'user_id' => $created_employee->user->id,
                        'avatar' => $created_employee->user->getAvatar(),
                        'full_name' => $created_employee->getFullName(),
                        'first_name' => $created_employee->first_name,
                        'middle_name' => $created_employee->middle_name,
                        'last_name' => $created_employee->last_name,
                        'employment_type' => $created_employee->employment_type,
                        'supervisor' => $created_employee->getSupervisorName(),
                        'birthdate' => $created_employee->birthdate ?? '',
                        'sick_leave' => $created_employee->sick_leave,
                        'vacation_leave' => $created_employee->vacation_leave,
                        'email' => $created_employee->user->email,
                        'department' => $created_employee->department->department,
                        'department_id' => $created_employee->department->id,
                        'position' => $created_employee->position->position,
                        'position_id' => $created_employee->position->id,
                        'schedules_route' => route('admin_employee_schedule_management', $created_employee->id),
                        'timesheets_route' => route('admin_timesheet_management', $created_employee->id)
                    ];
                    return response()->json($response_data);
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'EmployeeController->create #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $employee_id = $request->emp_id;
            $user_id = $request->usr_id;
            $employee_data = $request->emp_data;
            $user_data = $request->usr_data;
            $all_data_array = $request->all();
            $rules = [
                'emp_id' => ['required', 'exists:employees,id'],
                'usr_data.email' => ['required', 'email:rfc,dns', 'unique:users,email,' . $user_id, new AllowedDomains],
                'usr_data.password' => ['nullable', 'confirmed', Password::defaults()],
            ] + $this->default_required_rules;
            $validation_attr_names = ['emp_id' => 'employee ID'] + self::$validation_attr_names;
            $validator = Validator::make($all_data_array, $rules);
            $validator->setAttributeNames($validation_attr_names);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $outdated_employee = Employee::find($employee_id);
            $outdated_department = $outdated_employee->department;
            $outdated_user = $outdated_employee->user;

            $is_employee_update_ok = $outdated_employee->update($employee_data);
            $is_user_update_ok = true;
            $is_password_update_ok = true;
            if ($is_employee_update_ok) {
                $user_email = $user_data['email'];
                $user_pass = $user_data['password'];
                $updated_employee = Employee::find($employee_id);
                $updated_name = $updated_employee->getName();
                $is_user_update_ok = $updated_employee->user->update([
                    'name' => $updated_name,
                    'email' => $user_email
                ]);
                if ($is_user_update_ok) {
                    $outdated_employee->tryUpdateDeptSuprvsrEmail($user_email, $outdated_user->email);
                }
                if (!is_null($user_pass) && (strlen($user_pass) != 0)) {
                    $is_password_update_ok = $updated_employee->user->update(['password'=> Hash::make($user_pass)]);
                }
            }
            if ($is_employee_update_ok && $is_user_update_ok && $is_password_update_ok) {
                $response_data = [
                    'full_name' => $updated_employee->getFullName(),
                    'first_name' => $updated_employee->first_name,
                    'middle_name' => $updated_employee->middle_name,
                    'last_name' => $updated_employee->last_name,
                    'employment_type' => $updated_employee->employment_type,
                    'supervisor' => $updated_employee->getSupervisorName(),
                    'birthdate' => $updated_employee->birthdate,
                    'sick_leave' => $updated_employee->sick_leave,
                    'vacation_leave' => $updated_employee->vacation_leave,
                    'email' => $updated_employee->user->email,
                    'department' => $updated_employee->department->department,
                    'department_id' => $updated_employee->department->id,
                    'position' => $updated_employee->position->position,
                    'position_id' => $updated_employee->position->id,
                ];
                return response()->json([ 'data' => $response_data ]);
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'EmployeeController->update #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
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
            $employee = Employee::find($request->emp_id); // also deletes User
            if (!empty($employee)) {
                $is_hr = $employee->user->isHrByEmail();
                if ($is_hr) throw new Exception('Bad Request');
                $is_deleted = $employee->delete();
                if ($is_deleted) {
                    $employee->tryDetachDepartmentRoles();
                    return response()->json(['success' => true]);
                }
            }
            throw new Exception('Bad Request');
        } catch (Exception $e) {
            $err_log = 'EmployeeController->delete #User ID - ' . Auth::user()->id . ': ' . $e->getMessage();
            Log::error($err_log);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
