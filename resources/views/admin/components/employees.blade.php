<table id="employee-datatable" class="display table table-striped dt-responsive nowrap text-center shadow" style="width:100%;">
    <thead class="data-table-sticky-header">
        <tr>
            <th class="no-export"></th>
            <th class="dtr-control-col no-export">{{-- respnsive row button col --}}</th>
            <th>
                <button class="select-all bg-secondary shadow-sm rounded-2xl px-2 py-1">Select All</button>
                <button class="deselect-all bg-secondary shadow-sm rounded-2xl px-2 py-1 hidden">Unselect</button>
            </th>
            <th>{{-- avatar col --}}</th>
            <th>Employee Name</th>
            <th>Birth Date</th>
            <th>Department</th>
            <th>Immediate Suprvisor</th>
            <th>Position</th>
            <th>Employment Type</th>
            <th>Sick Leave</th>
            <th>Vacation Leave</th>
            <th class="w-20 no-export"></th>
        </tr>
    </thead>
    <tbody>
        @php
            $row_index = 0;
        @endphp
        @foreach ($employees as $employee)
        @php
            $emp_id = $employee->id;
            $usr_id = $employee->user->id;
            $avatar = $employee->user->getAvatar();
            $f_name = $employee->first_name;
            $m_name = $employee->middle_name;
            $l_name = $employee->last_name;
            $full_name = $employee->getFullName();
            $email = $employee->user->getEmail();
            $dept = $employee->department;
            $dept_id = $dept->id;
            $dept_name = $dept->getDepartment();
            $supervisor_name = $employee->getSupervisorName();
            $pos = $employee->position;
            $pos_id = $pos ? $pos->id : null;
            $pos_name = $pos ? $pos->position : 'N/A';
            $emp_type = $employee->employment_type ?? 'N/A';
            $sick_leave = $employee->sick_leave;
            $vacay_leave = $employee->vacation_leave;
            $brth_date = $employee->birthdate ?? 'N/A';
            $row_index++;
        @endphp
        <tr class="emp-container emp-{{ $emp_id }}" data-emp-id="{{ $emp_id }}">
            <td>{{ $row_index }}</td>
            <td>{{-- responsive row button --}}</td>
            <td>{{-- checkbox --}}</td>
            <td><img src="{{ $avatar }}" class="avatar"></td>
            <td>
                <span class="emp-{{ $emp_id }} full-name" data-emp-id="{{ $emp_id }}">{{ $full_name }}</span>
                <br>
                <span class="font-sm italic emp-{{ $emp_id }} email">{{ $email }}</span>
                <span class="hidden emp-{{ $emp_id }} first-name">{{ $f_name }}</span>
                <span class="hidden emp-{{ $emp_id }} middle-name">{{ $m_name }}</span>
                <span class="hidden emp-{{ $emp_id }} last-name">{{ $l_name }}</span>
            </td>
            <td><span class="emp-{{ $emp_id }} birth-date">{{ $brth_date }}</span></td>
            <td><span class="emp-{{ $emp_id }} department" data-dept-id="{{ $dept_id }}">{{ $dept_name }}</span></td>
            <td><span class="emp-{{ $emp_id }} supervisor">{{ $supervisor_name }}</span></td>
            <td><span class="emp-{{ $emp_id }} position" data-pos-id="{{ $pos_id }}">{{ $pos_name }}</span></td>
            <td><span class="emp-{{ $emp_id }} emp-type">{{ $emp_type }}</span></td>
            <td><span class="emp-{{ $emp_id }} sick-leave">{{ $sick_leave }}</span></td>
            <td><span class="emp-{{ $emp_id }} vacay-leave">{{ $vacay_leave }}</span></td>
            <td>
                <div class="btn-group dropleft">
                    <button type="button" class="btn btn-lg dropdown-toggle settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        &#9881; Settings
                    </button>
                    <div class="dropdown-menu box">
                        <a class="dropdown-item cursor-pointer timesheets-route" href="{{ route('admin_timesheet_management', $employee->id) }}">&#x23F0; View Timesheets</a>
                        <a class="dropdown-item cursor-pointer schedules-route" href="{{ route('admin_employee_schedule_management', $employee->id) }}">&#x1F4C5; View Schedules</a>
                        <a class="dropdown-item cursor-pointer edit" data-emp-id="{{ $emp_id }}" data-user-id="{{ $usr_id }}">&#x270f;&#xfe0f; Edit</a>
                        <a class="dropdown-item cursor-pointer delete" data-emp-id="{{ $emp_id }}">&#x1F5D1; Delete</a>
                    </div>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>