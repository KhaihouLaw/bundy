<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\TimesheetModificationRequest;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee = Auth::user()->employee;
        $timesheet = Timesheet::where([
            ['employee_id', '=', $employee->getId()],
            ['timesheet_date', '=', date('Y-m-d')]
        ])->first();

        $has_timesheet_today = (!is_null($timesheet));
        $adjustments = TimesheetModificationRequest::where('employee_id', $employee->getId())->get();
        $leave_requests = LeaveRequest::where('employee_id', $employee->getId())->get();

        return view('dashboard', compact(
            'timesheet',
            'has_timesheet_today',
            'employee',
            'adjustments',
            'leave_requests'
        ));
    }
}
