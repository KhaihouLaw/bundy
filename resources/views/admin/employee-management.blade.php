@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
    <div class="emp-management container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header font-black">Employees</div>
                <div class="card-body">
                    <div class="flex justify-between">
                        <div class="btn-group flex items-center space-x-2">
                            <button type="button" class="config-emps dropdown-toggle focus:outline-none" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                                <i class="fas fa-cogs font-xl" style="text-shadow: 0 15px 30px rgba(0,0,0,0.11), 0 5px 15px rgba(0,0,0,0.08);"></i>
                                <span>Configure Selected</span>
                            </button>
                            <div class="dropdown-menu box">
                                <a class="dropdown-item cursor-pointer add-timesheets">&#x23F0; Timesheets</a>
                                {{-- <a class="dropdown-item cursor-pointer add-schedules" href="{{ route('admin_employee_schedule_management', 1) }}">&#x1F4C5; Schedules</a> --}}
                                {{-- <a class="dropdown-item cursor-pointer delete" data-emp-id="{{ 1 }}">&#x1F5D1; Delete</a> --}}
                            </div>
                            <div class="selected-emps flex flex-col bg-gray-100 text-dark rounded-xl font-black resize-y overflow-auto p-2 shadow-sm"></div>
                        </div>
                        <div class="flex items-end">
                            <button class="add-emp px-4 py-1 flex justify-center items-center space-x-2 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black">
                                <span>Add Employee</span>
                                <span class="text-2xl">+</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">@include('admin.components.employees')</div>
                    {{-- employee form --}}
                    <div class="emp-form-modal modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-black text-xl">Employee</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                    <div class="form flex flex-col items-center justify-center px-10 w-full">
                                        <form class="emp flex flex-col space-y-2 justify-center items-center">
                                            <div class="p-2 flex flex-wrap justify-center items-center">
                                                <fieldset class="emp-data flex flex-col justify-center items-end m-1 rounded-2xl">
                                                    <legend class="font-sm italic w-auto">Employee Data:</legend>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="fname">First Name:</label>
                                                        <input id="fname" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="text" name="first_name" required />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="mname">Middle Name:</label>
                                                        <input id="mname" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="text" name="middle_name" required />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="lname">Last Name:</label>
                                                        <input id="lname" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="text" name="last_name" required />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="bdate">Birth Date:</label>
                                                        <input id="bdate" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="date" name="birth_date" />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="dept">Department</label>
                                                        <select id="dept" class="m-2 border-2 border-blue-300 rounded-xl w-80" name="department" required>
                                                            <option value="" selected disabled hidden>Select an Option</option>
                                                            @foreach ($departments as $department)
                                                                <option value="{{ $department->id }}">{{ $department->getDepartment() }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="emp-type">Employment Type:</label>
                                                        <select id="emp-type" class="m-2 border-2 border-blue-300 rounded-xl w-80" name="employment_type" required>
                                                            <option value="" selected disabled hidden>Select an Option</option>
                                                            @foreach ($employment_types as $types)
                                                                <option value="{{ $types }}">{{ $types }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="position">Position:</label>
                                                        <select id="position" class="m-2 border-2 border-blue-300 rounded-xl w-80" name="position" required>
                                                            <option value="" selected disabled hidden>Select an Option</option>
                                                            @foreach ($positions as $position)
                                                                <option value="{{ $position->id }}">{{ $position->position }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="vleave">Vacation Leave:</label>
                                                        <input id="vleave" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="number" name="vacation_leave" value="15" required />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="sleave">Sick Leave:</label>
                                                        <input id="sleave" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="number" name="sick_leave" value="15" required />
                                                    </div>
                                                </fieldset>
                                                <fieldset class="user-account flex flex-col justify-center items-end m-1 mt-4 rounded-2xl">
                                                    <legend class="font-sm italic w-auto">User Account:</legend>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="email">Email:</label>
                                                        <input id="email" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="email" name="email" required />
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="pass">New Password:</label>
                                                        <input id="pass" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="password" name="password" required />
                                                    </div>
                                                    <div class="passw-rules hidden w-full flex justify-center">
                                                        <div class="flex flex-col items-center bg-gray-50 rounded-xl w-4/5 py-3">
                                                            <span>Password must contain the following:</span>
                                                            <div>
                                                                <p class="letter invalid m-0">A <i>lowercase</i> letter</p>
                                                                <p class="capital invalid m-0">A <i>capital (uppercase)</i> letter</p>
                                                                <p class="number invalid m-0">A <i>number</i></p>
                                                                <p class="symbol invalid m-0">A <i>special character</i></p>
                                                                <p class="length invalid m-0">Minimum <i>8 characters</i></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="font-black text-lg mt-2" for="confpass">Confirm Password:</label>
                                                        <input id="confpass" class="m-2 border-2 border-blue-300 rounded-xl w-80" type="password" name="confirm_password" required />
                                                    </div>
                                                    <div class="mb-2 mt-4">
                                                        <input class="show-passw border-2 rounded-xl border-gray-200 mr-1" type="checkbox">
                                                        <span>Show Password</span>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <button class="submit-emp w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- timesheet form --}}
                    <div class="add-timesheet-modal modal fade" aria-hidden="true" id="add-timesheet-modal">
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
                                        <div class="mb-2 text-sm text-gray-400 italic">No timesheets will be created if the day and date range did not match the schedule of employees.</div>
                                        <form class="add-timesheet flex flex-col space-y-2 justify-center items-center">
                                            <div class="p-2 flex flex-wrap justify-center items-center space-x-4">
                                                <label class="font-black text-lg mt-2" for="start_date">Start Date:</label>
                                                <input id="start_date" class="m-2 border-2 border-blue-300 rounded-xl" type="date" name="start_date" required />
                                                <label class="font-black text-lg mt-2" for="end_date">End Date:</label>
                                                <input id="end_date" class="m-2 border-2 border-blue-300 rounded-xl" type="date" name="end_date" required />
                                            </div>                                        
                                            @foreach (App\Models\Schedule::WEEK_DAYS as $day)
                                            <div class="{{ $day }} flex flex-col justify-center items-center">
                                                <div class="flex items-center space-x-2">
                                                    <input class="day-checkbox border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="{{ $day }}" required>
                                                    <div class="bg-gradient-info shadow font-black rounded-2xl px-4 py-2 m-2 cursor-pointer" data-toggle="collapse" data-target="#{{ $day }}-collapse" aria-expanded="false" aria-controls="{{ $day }}-collapse">
                                                        {{ ucfirst($day) }}
                                                    </div>
                                                </div>
                                                <div class="collapse mx-2 mt-2" id="{{ $day }}-collapse">
                                                    <div class="card card-body">
                                                        <div class="flex flex-wrap justify-center items-center">
                                                            <div class="flex flex-col justify-center items-center">
                                                                <label class="font-black text-lg mt-3" for="time-in">Time In:</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="time_in" disabled />
                                                                <label class="font-black text-lg mt-3" for="time-out">Time Out:</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="time_out" disabled />
                                                            </div>
                                                            <div class="flex flex-col justify-center items-center">
                                                                <label class="font-black text-lg mt-3" for="lunch-start">Lunch Start:</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="lunch_start" disabled />
                                                                <label class="font-black text-lg mt-3" for="lunch-end">Lunch End</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="lunch_end" disabled />
                                                            </div>
                                                            <div class="flex flex-col justify-center items-center">
                                                                <label class="font-black text-lg mt-3" for="ot-start">Overtime Start</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="overtime_start" disabled />
                                                                <label class="font-black text-lg mt-3" for="ot-end">Overtime End</label>
                                                                <input class="m-2 border-2 border-blue-300 rounded-xl bg-gray-100" type="time" name="overtime_end" disabled />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="border-2 border-gray-200 w-full shadow" />
                                            @endforeach
                                            <div class="flex justify-end w-full">
                                                <button class="add-timesheet w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Save</button>
                                            </div>
                                        </form>
                                        <div class="timesheets-logs-cont rounded-2xl w-1/2 h-40 bg-gray-200 p-4 overflow-auto mt-2 hidden">
                                            <span class="text-gray-800 font-black">Timesheets not created for:</span>
                                            <div class="w-full text-black p-3 text-center no-scheds-cont">
                                            </div>
                                        </div>
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
