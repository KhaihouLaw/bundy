<h4>EMPLOYEES ATTENDANCE</h4>
<h5 style="margin-bottom: 10px;">{{ $date }}</h5>
<div class="w-full overflow-auto">
    <div class="table-container flex justify-center">
        <table class="departments not-responsive display table table-striped table-bordered dt-responsive nowrap text-center">
            <thead>
                <tr>
                    <th>Present</th>
                    <th>Late</th>
                    <th>On-Leave</th>
                    <th>Absent</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($formatted_data as $department_name => $department)
                @php
                    $abundantAttendanceType = max($department);
                    $loopCount = count($abundantAttendanceType);
                @endphp
                @for ($i = 0; $i < $loopCount; $i++)
                <tr>
                    <td>{{ $department[App\Models\Timesheet::EMPLOYEE_PRESENT][$i] ?? null }}</td>
                    <td>{{ $department[App\Models\Timesheet::EMPLOYEE_LATE][$i] ?? null }}</td>
                    <td>{{ $department[App\Models\Timesheet::EMPLOYEE_ON_LEAVE][$i] ?? null }}</td>
                    <td>{{ $department[App\Models\Timesheet::EMPLOYEE_ABSENT][$i] ?? null }}</td>
                </tr>
                @endfor
            @endforeach
            </tbody>
        </table>
    </div>
</div>