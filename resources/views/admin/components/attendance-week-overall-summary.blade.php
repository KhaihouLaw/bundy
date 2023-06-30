<div class="week-tabs flex flex-wrap justify-center items-center mb-4">
    <button type="button" class="overall m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Overall</button>
    <button type="button" class="total-per-day m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Day</button>
    <button type="button" class="total-per-employee m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Employee</button>
</div>
<h4 class="text-center">OVERALL ATTENDANCE SUMMARY OF THE WEEK</h4>
<h5 style="margin-bottom: 10px;">{{ $start_date . ' - ' . $end_date }}</h5>
<div class="w-full overflow-auto">
    <div class="table-container flex justify-center">
        <table class="departments not-responsive display table table-striped table-bordered dt-responsive nowrap text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($formatted_data as $department_name => $department)
                @foreach ($department as $employee_name => $week)
                @php
                    $monday = $week->monday ?? (object)[];
                    $tuesday = $week->tuesday ?? (object)[];
                    $wednesday = $week->wednesday ?? (object)[];
                    $thursday = $week->thursday ?? (object)[];
                    $friday = $week->friday ?? (object)[];
                    $saturday = $week->saturday ?? (object)[];
                @endphp
                <tr>
                    <td>{{ $employee_name }}</td>
                    <td class="font-black text-{{ $monday->attendance_color ?? 'gray' }}-500">{{ $monday->attendance ?? 'N/A' }}</td>
                    <td class="font-black text-{{ $tuesday->attendance_color ?? 'gray' }}-500">{{ $tuesday->attendance ?? 'N/A' }}</td>
                    <td class="font-black text-{{ $wednesday->attendance_color ?? 'gray' }}-500">{{ $wednesday->attendance ?? 'N/A' }}</td>
                    <td class="font-black text-{{ $thursday->attendance_color ?? 'gray' }}-500">{{ $thursday->attendance ?? 'N/A' }}</td>
                    <td class="font-black text-{{ $friday->attendance_color ?? 'gray' }}-500">{{ $friday->attendance ?? 'N/A' }}</td>
                    <td class="font-black text-{{ $saturday->attendance_color ?? 'gray' }}-500">{{ $saturday->attendance ?? 'N/A' }}</td>
                    <td>{{ $department_name }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>