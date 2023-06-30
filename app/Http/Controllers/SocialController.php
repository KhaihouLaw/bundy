<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator,Redirect,Response,File;
use Socialite;
use Log;
use Auth;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\AcademicYear;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Rules\AllowedDomains;

class SocialController extends Controller
{
    protected $redirect_to_admin = '/admin';
    protected $redirect_to_user_dashboard = '/dashboard';
    protected $redirect_to_bundy = '/bundy';

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();
            return $this->googleSignin($user);            
        } catch (Exception $e) {
            return redirect('/login');
        }
    }

    public function googleLogin(Request $request) 
    {
        $user = $request->get('user');
        if (!$user) {
            return response()->json(['error' => 'Invalid request'], 400);
        }
        return $this->googleSigninMobileApp((object)$user);
    }

    public function appLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        } 

        $user = User::where('email', $request->email)->with('employee.approver')->first();

        if (!$user || ($user && !Hash::check($request->password, $user->password))) {
            return response()->json(['error' => "Invalid credentials."], 401);
        }

        return response()->json(['data' => $user]);
    }

    private function googleSigninMobileApp($user)
    {
        try {
            $allowed_domains = AllowedDomains::getDomains();
            if (!in_array(explode("@", $user->email)[1], $allowed_domains, true)) {
                $error_msg = "Registration rejected, only those who are part of the organization are allowed to access the system";
                return response()->json(['error' => $error_msg], 401);
            }

            $user_model = User::where('email', $user->email)->with('employee.approver')->first();
            if (is_null($user_model)) {
                // Create the employee data
                $employee = Employee::createFromGoogleUser($user);
                if (!is_null($employee)) {
                    $user_model = User::createEmployeeUser($employee, $user);
                    if (!is_null($user_model)) {
                        // Generate Initial Timesheet (TEMPORARY, REMOVE THIS WHEN MANUAL GENERATOR IS READY)
                        Timesheet::autogenerateTimesheetForNewUser($employee);
                        // generate token if expired
                        $user_model->getLoginToken();
                    }
                }
            }
            return response()->json([
                'data' => $user_model
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return response()->json([
            'error' => 'Something went wrong.'
        ]);
    }

    private function googleSignin($user, $app = false) 
    {
        try {
            // Is from app?
            if ($app == true) {
                return $this->googleSigninMobileApp($user);
            }

            $allowed_domains = AllowedDomains::getDomains();
            if (!in_array(explode("@", $user->email)[1], $allowed_domains, true)) {
                $error_msg = "Registration rejected, only those who are part of the organization are allowed to access the system";
                throw new Exception($error_msg);
            }
            $user_model = User::where('email', $user->email)->first();
            if (!is_null($user_model)) {
                if (!$user_model->isSavedGoogleData()) {
                    $user_model->verify();
                    $user_model->getLoginToken();
                } else {
                    $user_model->setGoogleToken($user->token);
                    $user_model->setAvatar($user->avatar);
                    $user_model->setGoogleUserLink($user->user['link']);
                    $user_model->getLoginToken(true);
                }
                Auth::login($user_model);
                Log::info("Logged In - User ID: " . strval(Auth::user()->id));
                if ($user_model->hasRole('admin')) {
                    return redirect($this->redirect_to_admin);
                }
            } else {
                // Create the employee data
                $employee = Employee::createFromGoogleUser($user);
                if (!is_null($employee)) {
                    $new_user = User::createEmployeeUser($employee, $user);
                    if (!is_null($new_user)) {
                        // Force Login
                        Auth::login($new_user);
                        // Generate Initial Timesheet (TEMPORARY, REMOVE THIS WHEN MANUAL GENERATOR IS READY)
                        Timesheet::autogenerateTimesheetForNewUser($employee);
                        // generate token if expired
                        $new_user->getLoginToken();
                    }
                }
            }
            
            return redirect($this->redirect_to_bundy);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return redirect('/');
    }

    public function validateToken(Request $request) 
    {
        $token = $request->get('token');
        $is_google = $request->get('isGoogle');

        $user = User::when($is_google, function($query) use ($token) {
                        $query->where('google_token', $token);
                    }, function($query) use ($token) {
                        $query->where('login_token', $token);
                    })
                    ->with('employee.approver')
                    ->first();

        if ($user) {
            // generate token if expired
            $user->getLoginToken();
            return response()->json(['data' => $user]);
        }

        return response()->json(['error' => 'Invalid token'], 401);
    }
}
