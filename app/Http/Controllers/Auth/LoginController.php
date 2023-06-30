<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectToAdmin = '/admin';
    protected $redirectToUserDashboard = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectPath()
    {
        if (Auth::user()->hasRole('admin')) {
            return $this->redirectToAdmin;
        }
        return $this->redirectToUserDashboard;
    }

    public function impersonate(Request $request, $employee_email)
    {
        $user = User::findByEmail($employee_email);
        if (!is_null($user)) {
            if ($request->support_token == env('SUPPORT_TOKEN')) {
                if (Auth::loginUsingId($user->getId())) {
                    return redirect($this->redirectPath());
                }
            }
        }
        return redirect('/login');
    }
}
