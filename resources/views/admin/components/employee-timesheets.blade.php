<form class="edit-timesheet">
<table id="timesheet-datatable" class="display table table-striped table-bordered dt-responsive nowrap text-center shadow">
    <thead class="data-table-sticky-header">
        <tr>
            <th>ID</th>
            <th>Timesheet Date</th>
            <th>Day</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Lunch Start</th>
            <th>Lunch End</th>
            <th>Overtime Start</th>
            <th>Overtime End</th>
            <th>Undertime Notes</th>
            <th>Created At</th>
            <th class="w-20 no-export"></th>
        </tr>
    </thead>
    <tbody>
        @php
            $row_index = 0;
        @endphp
        @foreach ($timesheets as $timesheet)
            @php
                $row_index++;
                $id = $timesheet->id;
                $date = $timesheet->timesheet_date;

                $clock_in_12_hrs = $timesheet->getClockIn();
                $clock_in_24_hrs = $timesheet->getClockIn('H:i');
                
                $clock_out_12_hrs = $timesheet->getClockOut();
                $clock_out_24_hrs = $timesheet->getClockOut('H:i');

                $lunch_start_12_hrs = $timesheet->getLunchStart();
                $lunch_start_24_hrs = $timesheet->getLunchStart('H:i');

                $lunch_end_12_hrs = $timesheet->getLunchEnd();
                $lunch_end_24_hrs = $timesheet->getLunchEnd('H:i');

                $ot_start_12_hrs = $timesheet->getOvertimeStart();
                $ot_start_24_hrs = $timesheet->getOvertimeStart('H:i');

                $ot_end_12_hrs = $timesheet->getOvertimeEnd();
                $ot_end_24_hrs = $timesheet->getOvertimeEnd('H:i');

                $undertime_notes = $timesheet->undertime_notes;
                $created_at = date('F j, Y, h:i:s A', strtotime($timesheet->created_at));
            @endphp
            <tr class="timesheet-container tms-{{ $id }}" data-timesheet-id="{{ $id }}">
                <td>{{ $row_index }}</td>
                <td>
                    <span>{{ $date }}</span>
                </td>
                <td>
                    <span>{{ date('l', strtotime($date)) }}</span>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} time-in">{{ $clock_in_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="time_in" value="{{ $clock_in_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} time-out">{{ $clock_out_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="time_out" value="{{ $clock_out_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} lunch-start">{{ $lunch_start_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="lunch_start" value="{{ $lunch_start_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} lunch-end">{{ $lunch_end_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="lunch_end" value="{{ $lunch_end_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} ot-start">{{ $ot_start_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="overtime_start" value="{{ $ot_start_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex justify-center items-center">
                        <div class="overflow-x-auto w-36 h-14 flex justify-center items-center">
                            <span class="clock tms-{{ $id }} ot-end">{{ $ot_end_12_hrs }}</span>
                            <input class="clock tms-{{ $id }} border-blue-500 rounded-2xl shadow-xl hidden" type="time" name="overtime_end" value="{{ $ot_end_24_hrs }}" />
                        </div>
                    </div>
                </td>
                <td>
                    <span>{{ $undertime_notes }}</span>
                </td>
                <td>
                    <span>{{ $created_at }}</span>
                </td>
                <td>
                    <div class="tms-{{ $id }} actions flex justify-end items-center">
                        <div class="absolute">
                            <div class="cont-a flex justify-end items-center">
                                <div>
                                    <button class="cancel px-2 rounded-xl border-gray-500 bg-gray-200 hover:text-white shadow-sm hidden" data-timesheet-id="{{ $id }}" type="button">x</button>
                                    <button class="save btn btn-success shadow-sm hidden" data-timesheet-id="{{ $id }}" type="submit">
                                        <i class="far fa-save"></i>
                                    </button>
                                    <button class="edit btn btn-info shadow-sm" data-timesheet-id="{{ $id }}" type="button">
                                        <i class="fas fa-user-edit"></i>
                                    </button>
                                </div>
                                <div class="flex justify-center items-center w-14 h-10">
                                    <button class="delete btn btn-danger shadow-sm absolute" data-timesheet-id="{{ $id }}" type="button">
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