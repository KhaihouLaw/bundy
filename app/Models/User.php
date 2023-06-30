<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    const PERMISSION_RECORD_TIME = 'record time';
    const PERMISSION_APPROVE_LEAVE = 'approve leave';
    const PERMISSION_APPROVE_TIMESHEET = 'approve timesheet';

    const ROLE_LEAVE_APPROVER = 'leave approver';
    const ROLE_TIMESHEET_APPROVER = 'timesheet approver';
    const ROLE_ADMIN = 'admin';
    const ROLE_EMPLOYEE = 'employee';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'google_token',
        'password',
        'default_password',
        'status',
        'avatar',
        'google_user_link',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setGoogleToken($token)
    {
        $this->google_token = $token;
        return $this->save();
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this->save();
    }

    public function setGoogleUserLink($link)
    {
        $this->google_user_link = $link;
        return $this->save();
    }

    public function verify()
    {
        $this->email_verified_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    public function isHR()
    {
        $employee = $this->employee;
        if (!is_null($employee)) {
            $department = $employee->department;
            if (!is_null($department)) {
                if ($department->getId() == Department::HR) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isSavedGoogleData()
    {
        if (empty($this->google_token)) {
            return false;
        }
        return true;
    }

    public static function getByEmployeeId($employee_id)
    {
        return static::where('employee_id', $employee_id)->first();
    }

    public function getLoginToken($is_force = false) 
    {
        // Generate new token for first login and more than 30 days token
        if (
            $is_force || 
            !$this->login_token || 
            (
                $this->token_created_at && 
                (strtotime($this->token_created_at) < strtotime('-30 days'))
            )
        ) {
            $now_carbon = Carbon::now();
            $today = $now_carbon->isoFormat('YYYY-MM-DD');
            $this->login_token = Hash::make($this->email . $today);
            $this->token_created_at = $today;
            $this->save();
        }

        return $this->login_token;
    }

    public static function createEmployeeUser(Employee $employee, $google_user)
    {
        $password = bin2hex(random_bytes(7)); // Random Password
        $user = static::create([
            'name' => $google_user->name,
            'email' => $google_user->email,
            'password' => Hash::make($password),
            'employee_id' => $employee->getId(),
        ]);
        if (!is_null($user)) {
            $user->setGoogleToken($google_user->token);
            $user->setAvatar($google_user->avatar);
            $user->setGoogleUserLink($google_user->user['link']);
            $user->verify();
            return $user;
        }
        return null;
    }

    public function getAvatar()
    {
        if (empty($this->avatar)) {
            return 'https://www.gravatar.com/avatar/' . md5($this->email);
        }
        return $this->avatar;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function isSupervisor() 
    {
        $email = $this->email;
        $supervisor = Department::where('supervisor', $email)->first();
        return !empty($supervisor);
    }

    public function isApprover() {
        $email = $this->email;
        $approver = Department::where('approver', $email)->first();
        return !empty($approver);
    }

    public function isHrByEmail()
    {
        return $this->email === env('HR_EMAIL');
    }
    
    /**
     * Dependent: Clock out email
     */
    public static function getUsersByTimesheets($timesheets)
    {
        $users = [];
        foreach ($timesheets as $timesheet) {
            $employee = $timesheet->employee;
            $user = $employee->user;
            array_push($users, $user);
        }
        return $users;
    }

    public function getApproverAssignedDepartments()
    {
        $email = Auth::user()->email;
        $assigned_depts = Department::where('approver', $email)->where('supervisor', '!=', $email)->get();
        return $assigned_depts;
    }

    public function getSupervisedDepartments()
    {
        $email = Auth::user()->email;
        $depts = Department::where('supervisor', $email)->get();
        return $depts;
    }
}
