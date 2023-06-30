<div class="m-2">
    <div class="grid justify-items-center mb-4">
        <div class="text-gray-400 font-black text-sm">{{ date('l F j, Y') }}</div>
    </div>
    <div class="departments flex flex-wrap space-x-2 space-y-2 justify-center items-center mb-10 px-2- pt-3 pb-4 rounded-lg bg-gray-50 shadow-inner">
        @foreach ($department_stats as $department => $stats)
        @php
            $total_emp = $stats->total_employees;
            $prsnt_emp = $stats->present_today;
            $progress =  $prsnt_emp ? ($prsnt_emp / $total_emp) * 100 : 0;
        @endphp
            <div class="dept-card flex flex-col justify-between items-center text-gray-500 rounded w-28 h-28 py-3 px-1 shadow-sm bg-gradient-to-r from-white via-white to-gray-100"
                data-department="{{ $department }}">
                <span class="text-center font-black">{{ $department }}</span>
                <div>
                    <span class="text-center">{{ $prsnt_emp . '/' . $total_emp  }} Present</span>
                    <div class="progress progress-sm progress-striped active w-full">
                        <div class="progress-bar bg-info" role="progressbar" aria-valuenow="{{ $prsnt_emp }}" aria-valuemin="0" aria-valuemax="{{ $total_emp }}" style="width: {{ $progress }}%;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="p-3 rounded-lg shadow-inner">
        <div class="clock-tabs-cont mb-3">
            <button class="clock-ins bg-blue-300 text-white rounded-lg w-28 h-12 shadow-sm hidden">Clock Ins</button>
            <button class="not-clocked-in bg-gray-300 text-white rounded-lg w-28 h-12 shadow-sm">Not Clocked In</button>
        </div>
        <div class="table-cont">
            <div class="table-label text-center font-black text-gray-500 underline mb-2">Clocked In Scheduled Employees</div>
            <div class="present-and-late-dt-cont">
                <table id="present-and-late-datatable" class="display table table-striped dt-responsive nowrap text-center shadow" style="width:100%;">
                    <thead class="data-table-sticky-header">
                        <tr>
                            <th class="avatar"></th>
                            <th class="w-30">Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Employment Type</th>
                            <th>Work Time</th>
                            <th>Lunch</th>
                            <th>OT</th>
                            <th>Undertime / Time-Release</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($present_and_late as $timesheet)
                        @php
                            $avatar = $timesheet->employee->user->getAvatar();
                            $attendance_type = $timesheet->getAttendanceType();
                            $attendance_type = App\Models\Timesheet::EMPLOYEE_PRESENT == $attendance_type ? 'on-time' : $attendance_type;
                            $attendance_color = $timesheet->getAttendanceColor();
                            $full_name = $timesheet->employee->getFullName();
                            $email = $timesheet->employee->user->getEmail();
                            $department = $timesheet->employee->department->department;
                            $position = $timesheet->employee->position ? $timesheet->employee->position->position : '';
                            $employment_type = $timesheet->employee->employment_type;
                            $clock_in = $timesheet->getClockIn();
                            $clock_out = $timesheet->getClockOut();
                            $lunch_start = $timesheet->getLunchStart();
                            $lunch_end = $timesheet->getLunchEnd();
                            $ot_start = $timesheet->getOvertimeStart();
                            $ot_end = $timesheet->getOvertimeEnd();
                            $has_undertime = $timesheet->hasUndertime();
                            $undertime_notes = $timesheet->getUndertimeNotes();
                        @endphp
                        <tr>
                            <td>
                                <img class="avatar" src="{{ $avatar }}">
                            </td>
                            <td>
                                {{ $full_name }}
                                <br>
                                <small>{{ $email }}</small>
                            </td>
                            <td>{{ $department }}</td>
                            <td>{{ $position }}</td>
                            <td>{{ $employment_type }}</td>
                            <td>
                                <div class="flex flex-col items-center justify-center">
                                    <span>{{ $clock_in }} - {{ $clock_out }}</span>
                                    <span class="flex items-center justify-center bg-{{ $attendance_color }}-500 rounded-lg w-16 h-6 font-black text-white">{{ $attendance_type }}</span>
                                </div>
                            </td>
                            <td>{{ $lunch_start }} - {{ $lunch_end }}</td>
                            <td>{{ $ot_start }} - {{ $ot_end }}</td>
                            <td>
                                @if ($has_undertime)
                                    {{ $undertime_notes }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="on-leave-and-absent-dt-cont hidden">
                <table id="on-leave-and-absent-datatable" class="display table table-striped dt-responsive nowrap text-center shadow" style="width:100%;">
                    <thead class="data-table-sticky-header bg-gray-300 shadow-sm">
                        <tr>
                            <th class="avatar"></th>
                            <th class="w-30">Employee</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Employment Type</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($on_leave_and_absent as $timesheet)
                        @php
                            $avatar = $timesheet->employee->user->getAvatar();
                            $attendance_type = $timesheet->getAttendanceType();
                            $attendance_color = $timesheet->getAttendanceColor();
                            $full_name = $timesheet->employee->getFullName();
                            $email = $timesheet->employee->user->getEmail();
                            $department = $timesheet->employee->department->department;
                            $position = $timesheet->employee->position ? $timesheet->employee->position->position : '';
                            $employment_type = $timesheet->employee->employment_type;
                            $has_reason = $timesheet->leave_request;
                            $leave_request_id = $has_reason ? $has_reason->id : null;
                        @endphp
                        <tr>
                            <td>
                                <img class="avatar" src="{{ $avatar }}">
                            </td>
                            <td>
                                {{ $full_name }}
                                <br>
                                <small>{{ $email }}</small>
                            </td>
                            <td>{{ $department }}</td>
                            <td>{{ $position }}</td>
                            <td>{{ $employment_type }}</td>
                            <td><span class="font-black text-base text-{{ $attendance_color }}-500">{{ $attendance_type }}</span></td>
                            <td>
                                @if ($has_reason)
                                    <button class="view-reason w-28 h-12 bg-blue-400 text-white rounded-lg shadow-sm" 
                                            data-leave-request-id="{{ $leave_request_id }}"
                                            data-toggle="modal" data-target="#view-leave-reason-modal">
                                        View Reason
                                    </button>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>