<div class="week-tabs flex flex-wrap justify-center items-center mb-4">
    <button type="button" class="overall m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Overall</button>
    <button type="button" class="total-per-day m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Day</button>
    <button type="button" class="total-per-employee m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Employee</button>
</div>
<h4 class="text-center">ATTENDANCE TYPE TOTAL PER EMPLOYEE OF THE WEEK</h4>
<h5 style="margin-bottom: 10px;">{{ $start_date . ' - ' . $end_date }}</h5>
<div class="w-full overflow-auto">
    <div class="table-container flex justify-center">
        <table class="departments display table table-striped table-bordered dt-responsive nowrap text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Present</th>
                    <th>Late</th>
                    <th>On-Leave</th>
                    <th>Absent</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($formatted_data as $department_name => $department)
                @foreach ($department as $employee_name => $week)
                @php
                    $present = $week->{App\Models\Timesheet::EMPLOYEE_PRESENT} ?? (object)[];
                    $late = $week->{App\Models\Timesheet::EMPLOYEE_LATE} ?? (object)[];
                    $on_leave = $week->{App\Models\Timesheet::EMPLOYEE_ON_LEAVE} ?? (object)[];
                    $absent = $week->{App\Models\Timesheet::EMPLOYEE_ABSENT} ?? (object)[];
                @endphp
                <tr>
                    <td>{{ $employee_name }}</td>
                    <td class="font-black text-{{ $present->color ?? 'gray' }}-500">{{ $present->count ?? 0 }}</td>
                    <td class="font-black text-{{ $late->color ?? 'gray' }}-500">{{ $late->count ?? 0 }}</td>
                    <td class="font-black text-{{ $on_leave->color ?? 'gray' }}-500">{{ $on_leave->count ?? 0 }}</td>
                    <td class="font-black text-{{ $absent->color ?? 'gray' }}-500">{{ $absent->count ?? 0 }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>