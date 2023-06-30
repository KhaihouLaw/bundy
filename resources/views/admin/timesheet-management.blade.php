@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="timesheet-management container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Employee Timesheets Management</div>
            <div class="card-body">
                <div class="flex flex-col justify-center items-center mb-4">
                    <span class="text-lg">{{ $employee->getFullName() }}</span>
                    <span class="text-sm italic">{{ $employee->department->getDepartment() }}</span>
                </div>
                <div class="flex justify-between mx-4">
                    <a class="flex justify-center items-center px-3 py-1 shadow-sm text-dark font-black cursor-pointer bg-gradient-light rounded-pill hover:no-underline" 
                        href="{{ route('admin_employee_schedule_management', $employee->id) }}">&#x1F4C5; View Schedules</a>
                    <button class="add-timesheet px-4 py-1 flex justify-center items-center space-x-2 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black shadow-sm">
                        <span class="text-2xl">+</span>
                        <span>Add Timesheet</span>
                    </button>
                </div>
                <div class="mt-2">@include('admin.components.employee-timesheets')</div>
                {{-- add timesheet --}}
                <div class="add-timesheet-modal modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-black text-xl">Add Timesheet</h5>
                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                <div class="form flex flex-col items-center justify-center px-10 w-full">
                                    <form class="add-timesheet flex flex-col space-y-2 justify-center items-center">
                                        <div class="p-2 flex flex-wrap justify-center items-center space-x-4">
                                            <label class="font-black text-lg mt-2" for="start_date">Start Date:</label>
                                            <input id="start_date" class="m-2 border-2 border-blue-300 rounded-xl" type="date" name="start_date" required />
                                            <label class="font-black text-lg mt-2" for="end_date">End Date:</label>
                                            <input id="end_date" class="m-2 border-2 border-blue-300 rounded-xl" type="date" name="end_date" required />
                                        </div>                                        
                                        @foreach (App\Models\Schedule::WEEK_DAYS as $day)
                                        @php
                                            $daySchedules = $employee->getSchedulesByDay($day);
                                        @endphp
                                        <div class="{{ $day }} flex flex-col justify-center items-center">
                                            <div class="flex items-center">
                                                <input class="day-checkbox border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="{{ $day }}" required>
                                                <div class="bg-gradient-info shadow font-black rounded-2xl px-4 py-2 m-2 cursor-pointer" data-toggle="collapse" data-target="#{{ $day }}-collapse" aria-expanded="false" aria-controls="{{ $day }}-collapse">
                                                    {{ ucfirst($day) }}
                                                </div>
                                            </div>
                                            <div class="collapse mx-2 mt-2" id="{{ $day }}-collapse">
                                                <div class="card card-body">
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="schedule">Schedule:</label>
                                                        <select id="schedule" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" name="schedule_id" disabled required>
                                                            @if (count($daySchedules) !== 0)
                                                                @foreach ($daySchedules as $schedule)
                                                                <option value="{{ $schedule->id }}">{{ $schedule->day }} - {{ date('h:i A', strtotime($schedule->start_time)) }} to {{ date('h:i A', strtotime($schedule->end_time)) }}</option>
                                                                @endforeach
                                                            @else
                                                                <option value="" selected disabled hidden>No Schedule</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="flex flex-wrap justify-center items-center">
                                                        <div class="flex flex-col justify-center items-center">
                                                            <label class="font-black text-lg mt-3" for="time-in">Time In:</label>
                                                            <input id="time-in" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="time_in" disabled/>
                                                            <label class="font-black text-lg mt-3" for="time-out">Time Out:</label>
                                                            <input id="time-out" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="time_out" disabled/>
                                                        </div>
                                                        <div class="flex flex-col justify-center items-center">
                                                            <label class="font-black text-lg mt-3" for="lunch-start">Lunch Start:</label>
                                                            <input id="lunch-start" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="lunch_start" disabled/>
                                                            <label class="font-black text-lg mt-3" for="lunch-end">Lunch End</label>
                                                            <input id="lunch-end" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="lunch_end" disabled/>
                                                        </div>
                                                        <div class="flex flex-col justify-center items-center">
                                                            <label class="font-black text-lg mt-3" for="ot-start">Overtime Start</label>
                                                            <input id="ot-start" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="overtime_start" disabled/>
                                                            <label class="font-black text-lg mt-3" for="ot-end">Overtime End</label>
                                                            <input id="ot-end" class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="overtime_end" disabled/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="border-2 border-gray-200 w-full shadow" />
                                        @endforeach
                                        <div class="w-full flex justify-end">
                                            <button class="add-timesheet w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    @include('admin.shared.data-table.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js" integrity="sha256-dOvlmZEDY4iFbZBwD8WWLNMbYhevyx6lzTpfVdo0asA=" crossorigin="anonymous"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.color-animation/1/mainfile"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection
