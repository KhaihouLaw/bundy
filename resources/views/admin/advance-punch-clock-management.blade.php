@extends('admin.base')

@section('styles')
    @include('admin.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/yearpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="apc-management container-fluid">
    <div class="fade-in">
        <div class="card">
            <div class="card-header">
                <span class="text-lg font-black">Face & QR Clock Management</span>
            </div>
            <div class="card-body">
            
                <div class="flex justify-end mx-4">
                    <button class="add-apc px-4 py-1 flex justify-center items-center space-x-2 bg-gradient-to-t from-blue-500 via-blue-300 to-blue-300 rounded-xl text-white font-black shadow-sm">
                        <span class="text-2xl">+</span>
                        <span>Create</span>
                    </button>
                </div>
                <div class="mt-2">@include('admin.components.advance-punch-clocks')</div>
                {{-- add form modal --}}
                <div class="apc-form-modal modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 870px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-black text-xl">Advance Punch Clock Instance</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                <div class="form flex flex-col items-center justify-center px-10 w-full">
                                    <form class="apc flex flex-col space-y-2 justify-center items-center">
                                        <div class="p-2 flex flex-wrap justify-center items-center">
                                            <div class="flex flex-col justify-center items-end">
                                                <div>
                                                    <label class="font-black text-lg mt-2" for="type">Type</label>
                                                    <select id="type" class="m-2 border-2 border-blue-300 rounded-xl w-60 md:w-80" name="type" required>
                                                        <option value="" selected disabled hidden>Select an Option</option>
                                                        <option value="{{ App\Models\Timesheet::TIME_IN }}">Clock In</option>
                                                        <option value="{{ App\Models\Timesheet::TIME_OUT }}">Clock Out</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="font-black text-lg mt-2" for="description">Description:</label>
                                                    <input id="description" class="m-2 border-2 border-blue-300 rounded-xl w-60 md:w-80" type="text" name="description" required />
                                                </div>
                                                <div>
                                                    <label class="font-black text-lg mt-2" for="access-code">Access Code:</label>
                                                    <input id="access-code" class="m-2 border-2 border-blue-300 rounded-xl w-60 md:w-80" type="text" name="access_code" required />
                                                </div>
                                            </div>
                                            <fieldset class="schedule flex flex-col justify-center items-end m-1 mt-4 rounded-2xl">
                                                <legend class="font-sm italic w-auto">Schedule:</legend>
                                                <div class="set-time flex justify-center mt-4">
                                                    <div class="
                                                            flex flex-col items-center
                                                            md:block" 
                                                        style="width: 700px;">
                                                        <div class="monday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Monday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="monday" required>
                                                            </label>
                                                            <div class="flex 
                                                                    flex-col items-end justify-center w-full
                                                                    md:flex-row md:items-center">
                                                                <hr class="border-2 w-4 border-blue-500 mx-2 hidden md:block">
                                                                {{-- <label class="flex justify-center items-center m-2">
                                                                    <span class="text-base">Time Start</span>
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="start-time" disabled>
                                                                </label>
                                                                <label class="flex justify-center items-center m-2">
                                                                    <span class="text-base">Time End</span>
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled>
                                                                </label> --}}

                                                                <div class="flex flex-col justify-center items-center">
                                                                    <div class="bg-blue-400 rounded-2xl px-4 py-2 m-2" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                                                        View Schedules
                                                                    </div>
                                                                    <div class="collapse mx-2 mt-2" id="collapseExample">
                                                                        <div class="card card-body">
                                                                            <div class="flex justify-end">
                                                                                <button class="add-time font-black text-lg bg-blue-500 text-white w-8 rounded-xl" type="button">+</button>
                                                                            </div>
                                                                            <div class="time-inputs flex 
                                                                                    flex-col items-end justify-center w-full
                                                                                    md:flex-row md:items-center">
                                                                                {{-- <label class="flex justify-center items-center m-2">
                                                                                    <span class="text-base">Time Start</span>
                                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="start-time" disabled>
                                                                                </label>
                                                                                <label class="flex justify-center items-center m-2">
                                                                                    <span class="text-base">Time End</span>
                                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled>
                                                                                </label> --}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tuesday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Tuesday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="tuesday" required>
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
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled    >
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="wednesday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Wednesday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="wednesday" required>
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
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled    >
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="thursday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Thursday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="thursday" required>
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
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled    >
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="friday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Friday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="friday" required>
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
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled    >
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="saturday flex items-center
                                                                flex-col justify-center border-2 rounded-xl border-blue-400 m-1 shadow-sm
                                                                md:flex-row md:justify-between md:border-none md:m-0">
                                                            <label class="flex flex-row-reverse justify-center m-2">
                                                                <span class="text-base">Saturday</span>
                                                                <input class="border-2 border-blue-500 rounded-lg w-6 h-6 mx-2" type="checkbox" name="day" value="saturday" required>
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
                                                                    <input class="border-2 border-blue-500 rounded-lg mx-2" type="time" name="end-time" disabled    >
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <button class="submit-apc w-28 h-14 mt-4 bg-blue-400 rounded-xl font-black text-white" type="submit">Save</button>
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
    <script src="{{ asset('js/yearpicker.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection