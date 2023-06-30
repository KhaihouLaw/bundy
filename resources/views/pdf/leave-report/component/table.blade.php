<h4>EMPLOYEE LEAVE REQUESTS REPORT</h4>
<h5>{{ $report['date-range'][0] . ' - '  . $report['date-range'][1] }}</h5>
<table class="report" cellspacing="3px" cellpadding="10px">
    <thead>
        <tr>
            <th class="avatar" style="width: 20;"></th>
            <th>Name</th>
            <th>Department</th>
            <th>Position</th>
            <th>Employment Type</th>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Leave Day/s</th>
            <th>Status</th>
            {{-- <th>Reason</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($report['records'] as $record)
            @php
                $initialRow = true;
                $leave_requests = $record['leave_requests'];
                $leave_requests_count = count($leave_requests);
                $employee = $record['employee'];
                $approved_leave_total_days = 0;
            @endphp
            @if ($leave_requests_count)
                @foreach ($leave_requests as $leave_request)
                    @php
                        $avatar = $employee->user->getAvatar();
                        $full_name = $employee->getFullName();
                        $department_name = $employee->department->department ?? null;
                        $position_name = $employee->position->position ?? null;
                        $employment_type = $employee->employment_type ?? null;
                        $start_date = $leave_request->start_date;
                        $end_date = $leave_request->end_date;
                        $leave_type = $leave_request->leaveType->getName();
                        $leave_total_days = null;
                        $status = $leave_request->status;
                        $reason = $leave_request->getReason(true);
                        if ($status === App\Models\LeaveRequest::APPROVED) {
                            $leave_total_days = $leave_request->getTotalDays();
                            $approved_leave_total_days += $leave_total_days;
                        } 
                    @endphp
                    @if ($initialRow)
                        <tr>
                            <td class="avatar" style="border-bottom: 0;">
                                <img style="width: 20px;" src="{{ $avatar }}">
                            </td>
                            <td style="border-bottom: 0; border-top: 0;">{{ $full_name }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{ $department_name }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{ $position_name }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{ $employment_type }}</td>
                            <td style="border-bottom: 0; border-top: 0;">{{ $leave_type }}</td>
                            <td>{{ $start_date }}</td>
                            <td>{{ $end_date }}</td>
                            <td>{{ $leave_total_days }}</td>
                            <td>{{ $status }}</td>
                            {{-- <td>{{ $reason }}</td> --}}
                        </tr>
                        @php
                            $initialRow = false;
                        @endphp
                    @else
                        <tr>
                            <td class="avatar" style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td style="border-bottom: 0; border-top: 0;"></td>
                            <td>{{ $leave_type }}</td>
                            <td>{{ $start_date }}</td>
                            <td>{{ $end_date }}</td>
                            <td>{{ $leave_total_days }}</td>
                            <td>{{ $status }}</td>
                            {{-- <td>{{ $reason }}</td> --}}
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td class="avatar" style="border-right: 0;"></td>
                    <td colspan="7" style="border-left: 0;"></td>
                    <td class="w-28" style="vertical-align: middle;">
                        <div class="overall-total">
                            <div style="font-weight: bolder">Total</div>
                            <div>{{ $approved_leave_total_days }}</div>
                        </div>
                    </td>
                    <td></td>
                </tr>
            @else
                {{-- @php
                    $avatar = $employee->user->getAvatar();
                    $full_name = $employee->getFullName();
                    $department_name = $employee->department->department ?? null;
                    $position_name = $employee->position->position ?? null;
                    $employment_type = $employee->employment_type ?? null;
                @endphp
                <tr>
                    <td class="avatar" style="border-bottom: 0;">
                        <img style="width: 20px;" src="{{ $avatar }}">
                    </td>
                    <td>{{ $full_name }}</td>
                    <td>{{ $department_name }}</td>
                    <td>{{ $position_name }}</td>
                    <td>{{ $employment_type }}</td>
                    <td></td>
                    <td></td>
                    <td class="w-28" style="vertical-align: middle;">
                        <div class="overall-total">
                            <div style="font-weight: bolder">No Leave</div>
                        </div>
                    </td>
                    <td></td>
                </tr> --}}
            @endif
        @endforeach
    </tbody>
</table>