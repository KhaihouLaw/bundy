<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\AllowedDomains;
use App\Models\Employee;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            // 'name' => ['required', 'string', 'max:255'],
            'first-name' => ['required', 'string', 'max:255', 'alpha'],
            'middle-name' => ['required', 'string', 'max:255', 'alpha'],
            'last-name' => ['required', 'string', 'max:255', 'alpha'],
            'birth-date' => ['required', 'before:15 years ago'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', new AllowedDomains],
            'department' => ['required', 'integer', 'between:1,6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'between' => 'The department does not exist',
            'before' => 'The birth date must be at least 15 years old.'
        ], [
            'first-name' => 'first name',
            'middle-name' => 'middle name',
            'last-name' => 'last name',
            'birth-date' => 'birth date',
        ]);
        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $employee = Employee::create([
            'first_name' => $data['first-name'],
            'middle_name' => $data['middle-name'],
            'last_name' => $data['last-name'],
            'birthdate' => $data['birth-date'],
            'department_id' => $data['department']
        ]);
        return User::create([
            'name' => $data['first-name'] . " " . $data['middle-name'] . " " . $data['last-name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'employee_id' => $employee->id,
        ]);
    }
}
