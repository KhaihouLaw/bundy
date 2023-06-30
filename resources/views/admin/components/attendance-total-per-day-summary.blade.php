<div class="week-tabs flex flex-wrap justify-center items-center mb-4">
    <button type="button" class="overall m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Overall</button>
    <button type="button" class="total-per-day m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Day</button>
    <button type="button" class="total-per-employee m-2 rounded-xl font-black bg-blue-500 shadow-2xl w-40 h-14 text-white">Total Per Employee</button>
</div>
<h4 class="text-center">ATTENDANCE TYPE TOTAL PER DAY OF THE WEEK</h4>
<h5 style="margin-bottom: 10px;">{{ $start_date . ' - ' . $end_date }}</h5>
<div class="w-full overflow-auto">
    <div class="table-container flex justify-center">
        <table class="departments display table table-striped table-bordered dt-responsive nowrap text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>Type</th>
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
                @foreach ($department as $attendance_type => $attendance)
                @php
                    $monday = $attendance->monday ?? (object)[];
                    $tuesday = $attendance->tuesday ?? (object)[];
                    $wednesday = $attendance->wednesday ?? (object)[];
                    $thursday = $attendance->thursday ?? (object)[];
                    $friday = $attendance->friday ?? (object)[];
                    $saturday = $attendance->saturday ?? (object)[];
                @endphp
                <tr>
                    <th>{{ $attendance_type }}</th>
                    <td alt="monday" class="font-black text-{{ $monday->attendance_type_color ?? 'gray' }}-500">{{ $monday->attendance_type_count ?? 0 }}</td>
                    <td class="font-black text-{{ $tuesday->attendance_type_color ?? 'gray' }}-500">{{ $tuesday->attendance_type_count ?? 0 }}</td>
                    <td class="font-black text-{{ $wednesday->attendance_type_color ?? 'gray' }}-500">{{ $wednesday->attendance_type_count ?? 0 }}</td>
                    <td class="font-black text-{{ $thursday->attendance_type_color ?? 'gray' }}-500">{{ $thursday->attendance_type_count ?? 0 }}</td>
                    <td class="font-black text-{{ $friday->attendance_type_color ?? 'gray' }}-500">{{ $friday->attendance_type_count ?? 0 }}</td>
                    <td class="font-black text-{{ $saturday->attendance_type_color ?? 'gray' }}-500">{{ $saturday->attendance_type_count ?? 0 }}</td>
                    <td>{{ $department_name }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>