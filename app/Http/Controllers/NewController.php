<?php

namespace App\Http\Controllers;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewController extends Controller
{
    public function showPending()
    {
        $pendingLeaveRequest = LeaveRequest::where('status', 'pending')->get();
        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        return response()->json([
            'data' => $pendingLeaveRequest,
            'count' => $pendingCount
        ]);
    }

    public function showApproved()
    {
        $approvedLeaveRequest = LeaveRequest::where('status', 'approved')->get();
        $approvedCount = LeaveRequest::where('status', 'approved')->count();

        return response()->json([
            'data' => $approvedLeaveRequest,
            'count' => $approvedCount
        ]);
    }

    public function showCancelled()
    {
        $cancelledLeaveRequest = LeaveRequest::where('status', 'cancelled')->get();
        $cancelledCount = LeaveRequest::where('status', 'cancelled')->count();

        return response()->json([
            'data' => $cancelledLeaveRequest,
            'count' => $cancelledCount
        ]);
    }

    public function getAllUsers()
    {
        $users = User::count();

        return response()->json($users);
    }

      public function updateTimeIn(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['time_in' => $request->time_in]);
    
      return response()->json(['message', 'time in updated']);
    }
    
    public function updateTimeOut(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['time_out' => $request->time_out]);
    
      return response()->json(['message', 'time out updated']);
    }

    public function updateLunchStart(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['lunch_start' => $request->lunch_start]);
    
      return response()->json(['message', 'lunch start updated']);
    }

    public function updateLunchEnd(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['lunch_end' => $request->lunch_end]);
    
      return response()->json(['message', 'lunch end updated']);
    }

    public function updateOvertimeStart(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['overtime_start' => $request->overtime_start]);
    
      return response()->json(['message', 'overtime start updated']);
    }

    public function updateOvertimeEnd(Request $request, $id) {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
    
      Timesheet::where('employee_id', $id)
        ->where('timesheet_date', '=', $formattedDate)
        ->update(['overtime_end' => $request->overtime_end]);
    
      return response()->json(['message', 'overtime end updated']);
    }

    public function getTimesheetByUserId($id) {
      $currentDate = Carbon::now();
      $formattedDate = $currentDate->format('Y-m-d');

      $timesheet = TimeSheet::where('employee_id', '=', $id)
      ->where('timesheet_date', '=', $formattedDate)
      ->first();

      return response()->json($timesheet);
    }

    public function getTimesheetsByUserId($id) {
      $currentDate = Carbon::now();
      $formattedDate = $currentDate->format('Y-m-d');

      $timesheet = TimeSheet::where('employee_id', '=', $id)
      ->where('timesheet_date', '=', $formattedDate)
      ->get();

      return response()->json($timesheet);
    }

    public function getTimeInCount() {
      $time_in_count = Timesheet::whereNotNull('time_in')->count();

      return response()->json($time_in_count);
    }
}
