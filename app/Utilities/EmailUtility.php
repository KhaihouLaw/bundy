<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Notifications\DailyClockOutNotification;
use App\Mail\TimesheetModificationRequestMail;
use App\Mail\TimesheetModificationStatusMail;
use App\Mail\LeaveRequestStatusMail;
use App\Notifications\WeeklyAttendanceSummaryNotification;
use App\Utilities\AttendanceUtility;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\Department;
use App\Models\Timesheet;
use Auth;

class EmailUtility
{
    /**
     * ============================= Timesheet Modification Request =============================
     */
    public static function sendTimesheetModificationEmail($config, $formatted_time_modifications, $modified_timesheet) {
        return false;
        $employee = $modified_timesheet->employee;
        $department_id = $employee->department->id;
        $timesheet_date = date_format(date_create($modified_timesheet->timesheet_date), 'l, F j, Y');
        $modif_notes = $modified_timesheet->notes;
        $requested_at = date_format($modified_timesheet->created_at, 'l, F j, Y h:i A');
        $go_to_request = route('advance');
        if (!empty($department_id)) {
            $go_to_request = route('advance.supervisor.department_timesheet_adjustments', $department_id);
        }
        $details = [
            'type' => $config['type'],
            'subject' => $config['subject'] . ' [ID: ' . $modified_timesheet->id . ']',
            'greetings' => $config['subject'],
            'id' => $modified_timesheet->id,
            'from' => $config['from'],
            'email' => $config['email'],
            'timesheet-date' => $timesheet_date,
            'notes' => $modif_notes,
            'date-request' => $requested_at,
            'button' => (object)[
                'route' => $go_to_request,
                'label' => 'Go to request'
            ]
        ] + $formatted_time_modifications;
        Mail::to($config['recipient'])
            // ->cc($config['cc'])
            ->send(new TimesheetModificationRequestMail($details));
    }

    public static function sendTimesheetModificationStatusNotif($config, $modification_request) {
        return false;
        // requestor details
        $employee = $modification_request->employee;
        $department = $employee->department;
        $department_id = $department->id;
        $requestor_emp_id = $employee->id;
        $requestor_usr_email = User::where('employee_id', $requestor_emp_id)->first()->email;
        $requestor_name = $employee->getFullName();
        $reviewer_email = $employee->department->getApprover()->email;
        // send email to requestor
        self::sendTimesheetModificationStatusEmail([
            'modif-req' => $modification_request,
            'subject' => $config['requestor']['subject'],
            // Approved by | Rejected by
            'from' => [$config['requestor']['from-label'] ?? null, Auth::user()->name],
            'email' => Auth::user()->email,
            'recipient' => $requestor_usr_email,
            'button' => (object)[
                'route' => route('timesheets'),
                'label' => 'Go to request'
            ]
        ]);
        // send email to reviewer / approver
        self::sendTimesheetModificationStatusEmail([
            'modif-req' => $modification_request,
            'subject' => $config['reviewer']['subject'],
            'from' => ['From:', $requestor_name],
            'email' => $requestor_usr_email,
            'recipient' => $reviewer_email,
            'cc' => [env('HR_EMAIL')],
            'button' => (object)[
                'route' => route('advance.supervisor.department_timesheet_adjustments', $department_id),
                'label' => 'Go to request'
            ]
        ]);
    }

    public static function sendTimesheetModificationStatusEmail($config) {
        return false;
        $details = [
            'modif-req' => $config['modif-req'],
            'subject' => $config['subject'],
            'greetings' => $config['subject'],
            'from' => $config['from'],
            'email' => $config['email'],
            'button' => $config['button'],
        ];
        if (isset($config['cc'])) { // when sending email to reviewer
            Mail::to($config['recipient'])
                // ->cc($config['cc'])
                ->send(new TimesheetModificationStatusMail($details));
        } else { // when sending email to requestor
            Mail::to($config['recipient'])->send(new TimesheetModificationStatusMail($details));
        }
    }

    /**
     * ============================= Leave Request =============================
     */
    public static function sendLeaveRequestStatusNotif($config, $leave_request, $recipient = [ 'requestor' => true, 'reviewer' => true]) {
        return false;
        // requestor details
        $employee = $leave_request->employee;
        $department = $employee->department;
        $department_id = $department->id;
        $leave_reason = $leave_request->getReason(true);
        $requestor_emp_id = $employee->id;
        $requestor_usr_email = User::where('employee_id', $requestor_emp_id)->first()->email;
        $requestor_name = $employee->getFullName();
        $leave_type_name = LeaveType::find($leave_request->leave_type_id)->leave;
        $reviewer_email = $employee->department->getApprover()->email;
        // send email to requestor
        if ($recipient['requestor']) {
            self::sendLeaveRequestStatusEmail([
                'leave-request' => $leave_request,
                'subject' => $config['requestor']['subject'],
                // Approved by | Rejected by
                'from' => [$config['requestor']['from-label'] ?? null, Auth::user()->name],
                'email' => Auth::user()->email,
                'recipient' => $requestor_usr_email,
                'leave-type' => $leave_type_name,
                'button' => (object)[
                    'route' => route('all_leave_requests'),
                    'label' => 'Go to request'
                ]
            ]);
        }
        // send email to reviewer / approver
        if ($recipient['reviewer']) {
            self::sendLeaveRequestStatusEmail([
                'leave-request' => $leave_request,
                'subject' => $config['reviewer']['subject'],
                'from' => ['From:', $requestor_name],
                'email' => $requestor_usr_email,
                'recipient' => $reviewer_email,
                'cc' => env('HR_EMAIL'),
                'leave-type' => $leave_type_name,
                'leave-reason' => $leave_reason,
                'button' => (object)[
                    'route' => route('advance.supervisor.department_leave_requests', $department_id),
                    'label' => 'Go to request'
                ]
            ]);
        }
    }

    public static function sendLeaveRequestStatusEmail($config) {
        return false;
        $start_date = date_format(date_create($config['leave-request']->start_date), 'l, F j, Y');
        $end_date = date_format(date_create($config['leave-request']->end_date), 'l, F j, Y');
        $details = [
            'leave-request' => $config['leave-request'],
            'subject' => $config['subject'],
            'greetings' => $config['subject'],
            'from' => $config['from'],
            'email' => $config['email'],
            'leave-type' => $config['leave-type'],
            'leave-reason' => $config['leave-reason'] ?? null,
            'date-range' => $start_date . ' - ' . $end_date,
            'button' => $config['button']
        ];
        if (isset($config['cc'])) {
            Mail::to($config['recipient'])
                ->cc($config['cc'])
                ->send(new LeaveRequestStatusMail($details));
        } else {
            Mail::to($config['recipient'])->send(new LeaveRequestStatusMail($details));
        }
    }

    /**
     * ============================= Attendance Summary =============================
     */

    public static function sendWeeklyAttendanceSummaryEmail() {
        return false;
        $day = date('w');
        // current week
        $monday = date('Y-m-d', strtotime('+' . (1-$day) . ' days'));
        $saturday = date('Y-m-d', strtotime('+' . (6-$day) . ' days'));
        // EmailUtility::sendWeeklyAttendanceSummaryEmailToHR($monday, $saturday);
        EmailUtility::sendWeeklyAttendanceSummaryEmailToSupervisors($monday, $saturday);
    }

    public static function sendWeeklyAttendanceSummaryEmailToHR($monday, $saturday)
    {
        return false;
        $departments_data = AttendanceUtility::getTotalAttendanceTypePerDay($monday, $saturday);
        $details = (object)[
            'subject' => 'Weekly Attendance Summary',
            'departments' => $departments_data,
            'start_date' => $monday,
            'end_date' => $saturday
        ];
        $user = User::where('email', env('HR_EMAIL'))->first();
        $user->notify(new WeeklyAttendanceSummaryNotification($details));
    }

    public static function sendWeeklyAttendanceSummaryEmailToSupervisors($monday, $saturday)
    {
        return false;
        $supervisors = Department::getSupervisors();
        foreach ($supervisors as $key => $supervisor) {
            if ($supervisor->email != env('HR_EMAIL')) {
                $department = $supervisor->employee->department;
                $department_data = AttendanceUtility::getTotalAttendanceTypePerDay($monday, $saturday, [$department]);
                $details = (object)[
                    'subject' => 'Weekly Attendance Summary',
                    'departments' => $department_data,
                    'start_date' => $monday,
                    'end_date' => $saturday
                ];
                $user = User::where('email', $supervisor->email)->first();
                $user->notify(new WeeklyAttendanceSummaryNotification($details));
            }
        }
    }
    
    /*
     * ============================= Clock Out Email =============================
     */
    
    public static function sendDailyClockOutEmail()
    {
        return false;
        $now = date('Y-m-d');
        $timesheets = Timesheet::getNotClockedOutTimesheetsByDate($now);
        $users = User::getUsersByTimesheets($timesheets);
        $details = (object)[ 
            'subject' => "You haven't clocked out yet",
            'message' => "Please clock out on Bundy.",
        ];
        Notification::send($users, new DailyClockOutNotification($details));
    }
    
}
