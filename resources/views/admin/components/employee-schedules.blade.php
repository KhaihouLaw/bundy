<form class="edit-schedule">
    <table id="schedule-datatable" class="display table table-striped dt-responsive nowrap text-center shadow">
        <thead class="data-table-sticky-header">
            <tr>
                <th class="no-export"></th>
                <th class="dtr-control-col no-export"></th>
                <th>Day</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Academic Year Semester</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Period</th>
                <th>Created At</th>
                <th class="w-20 no-export"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $row_index = 0;
            @endphp
            @foreach ($schedules as $schedule)
                @php
                    $row_index++;
                    $sched_id = $schedule->id;
                    $start_time_12hrs = $schedule->getStartTime();
                    $start_time_24hrs = $schedule->start_time;
                    $end_time_12hrs = $schedule->getEndTime();
                    $end_time_24hrs = $schedule->end_time;
                    $ay_start_yr = $schedule->employeeSchedule->academicYear->start_year;
                    $ay_end_yr = $schedule->employeeSchedule->academicYear->end_year;
                    $ay_start_date = $schedule->employeeSchedule->academicYear->start_date;
                    $ay_end_date = $schedule->employeeSchedule->academicYear->end_date;
                    $ay_semester = $schedule->employeeSchedule->academicYear->semester;
                    $ay_period = $schedule->employeeSchedule->period;
                    $created_at = date('F j, Y, h:i:s A', strtotime($schedule->created_at));
                @endphp
                <tr class="schedule-container sched-{{ $sched_id }}">
                    <td>{{ $row_index }}</td>
                    <td>{{-- responsive row button --}}</td>
                    <td>
                        {{-- November 16 2021, schedule day is not editable
                        to make it editable, just add class: "editable" --}}
                        <span class="sched-{{ $sched_id }} day">{{ $schedule->day }}</span>
                        <select class="sched-{{ $sched_id }} border-blue-500 rounded-2xl shadow-xl hidden" name="day" required>
                            <option value="{{ $schedule->day }}" selected disabled hidden>{{ $schedule->day }}</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </td>
                    <td>
                        <div class="flex justify-center items-center">
                            <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                                <span class="editable sched-{{ $sched_id }} start-time">{{ $start_time_12hrs }}</span>
                                <input class="editable sched-{{ $sched_id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="start_time" value="{{ $start_time_24hrs }}" />
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex justify-center items-center">
                            <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                                <span class="editable sched-{{ $sched_id }} end-time">{{ $end_time_12hrs }}</span>
                                <input class="editable sched-{{ $sched_id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="end_time" value="{{ $end_time_24hrs }}" />
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="academic-year-semester">A. Y. {{ $ay_start_yr }} - {{ $ay_end_yr }} Semester {{ $ay_semester }}</span>
                    </td>
                    <td>
                        <span class="ay-start-date">{{ $ay_start_date }}</span>
                    </td>
                    <td>
                        <span class="ay-end-date">{{ $ay_end_date }}</span>
                    </td>
                    <td>
                        <span class="period">{{ $ay_period }}</span>
                    </td>
                    <td>
                        <span class="period">{{ $created_at }}</span>
                    </td>
                    <td>
                        <div class="sched-{{ $sched_id }} actions flex justify-end items-center">
                            <div class="absolute">
                                <div class="cont-a flex justify-end items-center">
                                    <div>
                                        <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data-schedule-id="{{ $schedule->id }}" type="button">x</button>
                                        <button class="save btn btn-success shadow-sm hidden" data-schedule-id="{{ $schedule->id }}" type="submit">
                                            <i class="far fa-save"></i>
                                        </button>
                                        <button class="edit btn btn-info shadow-sm" data-schedule-id="{{ $schedule->id }}" type="button">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                    </div>
                                    <div class="flex justify-center items-center w-16 h-10">
                                        <button class="delete btn btn-danger shadow-sm absolute" data-schedule-id="{{ $schedule->id }}" type="button">
                                            <i class="far fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</form>