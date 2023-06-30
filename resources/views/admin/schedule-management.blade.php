@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="schedule-management container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header font-black">Employee Schedules Management</div>
            <div class="card-body">
                <div class="flex flex-col justify-center items-center mb-4">
                    <span class="text-lg">{{ $employee->getFullName() }}</span>
                    <span class="text-sm italic">{{ $employee->department->getDepartment() }}</span>
                </div>
                <div role="alert" aria-live="polite" aria-atomic="true" class="ts-alert alert alert-warning h-14 flex flex-row" style="padding: 0 !important;">
                    <div class="h-full w-14 bg-yellow-200 rounded flex justify-center items-center">
                        <span class="material-icons-round text-yellow-500 text-5xl" style="font-size: 40px;">
                            warning_amber
                        </span>
                    </div>
                    <span class="flex items-center w-full font-black pl-10">
                        Deleting a schedule will delete all its timesheets
                    </span>
                </div>
                <div class="flex justify-between mx-4">
                    <a class="flex justify-center items-center px-3 py-1 shadow-sm text-dark font-black cursor-pointer bg-gradient-light rounded-pill hover:no-underline" 
                        href="{{ route('admin_timesheet_management', $employee->id) }}">&#x23F0; View Timesheets</a>
                    <button class="add-schedule px-4 py-1 flex justify-center items-center space-x-2 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black shadow-sm">
                        <span class="text-2xl">+</span>
                        <span>Add Schedule</span>
                    </button>
                </div>
                <div class="mt-2">@include('admin.components.employee-schedules')</div>
                {{-- add schedule --}}
                <div class="add-schedule-modal modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-black text-xl">Add Schedule</h5>
                                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                <div class="form px-10 w-full">
                                    <form class="add-sched flex flex-col items-center justify-center w-full">
                                        <div class="flex flex-wrap items-center justify-center">
                                            <input class="hidden" name="employee_id" value="{{ $employee->id }}" />
                                            <label class="flex flex-col m-2">
                                                <span class="text-base mb-2">Academic Year</span>
                                                <select class="border-2 border-blue-500 rounded-lg" name="academic_year" required>
                                                    {{-- <option class="default" value="" selected disabled hidden>Select an Option</option> --}}
                                                    @foreach ($academic_years as $academic_year)
                                                        <option value="{{ $academic_year->id }}">{{ $academic_year->getDescription() }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                            <label class="flex flex-col m-2">
                                                <span class="text-base mb-2">Period</span>
                                                <input class="border-2 border-blue-500 rounded-lg" type="text" name="period" value="whole year" required>
                                            </label>
                                            <label class="flex m-2">
                                                <span class="text-base">Generate Timesheets</span>
                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 ml-2" name="generate_timesheet" type="checkbox">
                                            </label>
                                        </div>
                                        <div class="set-time flex justify-center mt-4">
                                            <div class="
                                                    flex flex-col items-center
                                                    md:block" 
                                                style="width: 700px;">

                                                @foreach (App\Models\Schedule::WEEK_DAYS as $day)
                                                <div class="{{ $day }} flex items-center
                                                        flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                        md:flex-row md:justify-between md:border-none md:m-0">
                                                    <label class="flex flex-row-reverse justify-center m-2">
                                                        <span class="text-base">{{ ucfirst($day) }}</span>
                                                        <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="{{ ucfirst($day) }}" required>
                                                    </label>
                                                    <div class="flex 
                                                            flex-col items-end
                                                            md:flex-row md:items-center">
                                                        <hr class="border-2 w-4 border-blue-500 mx-2 hidden md:block">
                                                        <label class="flex justify-center items-center m-2">
                                                            <span class="text-base">Time Start</span>
                                                            <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="start-time" disabled>
                                                        </label>
                                                        <label class="flex justify-center items-center m-2">
                                                            <span class="text-base">Time End</span>
                                                            <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled>
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex justify-center md:justify-end mt-5">
                                            <button class="bg-blue-500 rounded-lg font-black text-white w-36 h-14" type="submit">
                                                Save
                                            </button>
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
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.color-animation/1/mainfile"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection