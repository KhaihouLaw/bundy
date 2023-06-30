<h4>{{ strtoupper($attendance_type) }} EMPLOYEES</h4>
<h5 style="margin-bottom: 10px;">{{ $date }}</h5>
<div class="w-full">
    <table class="departments display table table-striped table-bordered dt-responsive nowrap text-center" style="width: 100%;">
        <thead>
            <tr>
                <th>Employee</th>
                @if (
                    ($attendance_type != App\Models\Timesheet::EMPLOYEE_ON_LEAVE) &&
                    ($attendance_type != App\Models\Timesheet::EMPLOYEE_ABSENT)
                )
                    <th>Time In</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($formatted_data as $department_name => $department)
            @foreach ($department as $employee)
            <tr>
                <td>{{ $employee->getFullName() }}</td>
                @if (
                    ($attendance_type != App\Models\Timesheet::EMPLOYEE_ON_LEAVE) &&
                    ($attendance_type != App\Models\Timesheet::EMPLOYEE_ABSENT)
                )
                    <td>{{ $employee->attendance_clocks->getClockIn() }}</td>
                @endif
            </tr>
            @endforeach
            @endforeach
    </tbody>
    </table>
</div>