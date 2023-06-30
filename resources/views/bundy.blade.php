@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vue/vue.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bundy.css') }}">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Bundy') }}
    </h2>
@endsection
    
@section('content')
    <div class="bundy-root py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <div class="flex justify-center">
                        <span class="notifications"></span>
                    </div>
                    <h4 class="date-now font-black text-center mb-4">{{ date('l, F d, Y') }}</h4>
                    <div class="space-y-12">
                        <div>
                            <p class="font-black text-lg">Working Time</p>
                            <div class="working flex flex-col lg:flex-row items-center space-y-5 lg:space-y-0">
                                <div class="flex flex-row">
                                    <button class="{{ (is_null($timesheet) || $timesheet->time_in) ? 'disabled' : '' }} 
                                        clock-in-btn btn btn-primary text-white tms-btn
                                        h-12 w-40 ml-8 
                                        sm:mr-4" type="submit"><i class="fa fa-clock-o text-2xl mr-3"></i>Clock In</button>
                                    <div class="flex flex-col items-center ml-1 mr-8 sm:ml-4">
                                        <button class="disabled clock-out-btn btn btn-primary text-white tms-btn h-12 w-40" type="submit">
                                            <i class="fa fa-clock-o text-2xl mr-3"></i>Clock Out
                                        </button>
                                        <input 
                                            type="checkbox" 
                                            data-on="Regular" 
                                            data-off="Undertime" 
                                            data-onstyle="info" 
                                            data-offstyle="secondary" 
                                            data-toggle="toggle"
                                            data-size="sm"
                                            checked 
                                            disabled
                                            class="toggle-undertime">
                                    </div>
                                </div>
                                <div class="clock h-24 md:h-12 flex flex-col md:flex-row w-full sm:w-2/3">
                                    <div class=" 
                                        rounded-t-md md:rounded-t-none md:rounded-l-md 
                                        border-blue-800 border-t-2 border-b-0 md:border-b-2 border-l-2 border-r-2 md:border-r-0 
                                        flex-1 flex items-center justify-between 
                                        px-4 font-black">
                                        <span>
                                            Clock In
                                            @if (!is_null($timesheet) && $timesheet->isLateToday())
                                            {{-- <small class="badge badge-danger">Late</small> --}}
                                            @endif
                                        </span>
                                        <span class="time-in">--:-- --</span>
                                    </div>
                                    <div class="border-blue-800 rounded-b-md md:rounded-b-none md:rounded-r-md border-t-0 md:border-t-2 border-r-2 border-b-2 border-l-2 flex-1 flex items-center justify-between px-4 font-black">
                                        <span>Clock Out</span>
                                        <span class="time-out">--:-- --</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="font-black text-lg">Lunch Break</p>
                            <div class="working flex flex-col lg:flex-row items-center space-y-5 lg:space-y-0">
                                <div class="flex flex-row">
                                    {{-- @note button disabled class dependent to "bootstrap": "^5.0.1" --}}
                                    <button class="tms-btn disabled lunch-start-btn btn btn-primary 
                                            h-12 w-40 ml-8 
                                            sm:mr-4" 
                                        type="button"><i class="fa fa-clock-o text-2xl mr-3"></i>Start Lunch</button>
                                    <button class="tms-btn disabled lunch-end-btn btn btn-primary 
                                            h-12 w-40 ml-1 mr-8
                                            sm:ml-4" 
                                        type="button"><i class="fa fa-clock-o text-2xl mr-3"></i>End Lunch</button>
                                </div>
                                <div class="clock h-24 md:h-12 flex flex-col md:flex-row w-96 w-full sm:w-2/3">
                                    <div class="rounded-t-md md:rounded-t-none md:rounded-l-md 
                                            border-blue-800 border-t-2 border-b-0 md:border-b-2 border-l-2 border-r-2 md:border-r-0 
                                            flex-1 flex items-center justify-between 
                                            px-4 font-black">
                                        <span>Lunch Break Start</span>
                                        <span class="lunch-start">--:-- --</span>
                                    </div>
                                    <div class="border-blue-800 rounded-b-md md:rounded-b-none md:rounded-r-md border-t-0 md:border-t-2 border-r-2 border-b-2 border-l-2 flex-1 flex items-center justify-between px-4 font-black">
                                        <span>Lunch Break End</span>
                                        <span class="lunch-end">--:-- --</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (date('H') >= 17)
                        <div>
                        @else
                        <div style="display: none">
                        @endif
                            <p class="font-black text-lg">Overtime</p>
                            <div class="working flex flex-col lg:flex-row items-center space-y-5 lg:space-y-0">
                                <div class="flex flex-row">
                                    <button class="tms-btn disabled overtime-start-btn btn btn-secondary text-white 
                                            h-12 w-40 ml-8 
                                            sm:mr-4" 
                                        type="button"><i class="fa fa-clock-o text-2xl mr-3"></i>Overtime Start</button>
                                    <button class="disabled overtime-end-btn btn btn-secondary text-white tms-btn
                                            h-12 w-40 ml-1 mr-8
                                            sm:ml-4" 
                                        type="button"><i class="fa fa-clock-o text-2xl mr-3"></i>Overtime End</button>
                                </div>
                                <div class="clock h-24 md:h-12 flex flex-col md:flex-row w-96 w-full sm:w-2/3">
                                    <div class="border-blue-800 rounded-t-md md:rounded-t-none md:rounded-l-md border-t-2 border-b-0 md:border-b-2 border-l-2 border-r-2 md:border-r-0 flex-1 flex items-center justify-between px-4 font-black">
                                        <span>Overtime Start</span>
                                        <span class="overtime-start">--:-- --</span>
                                    </div>
                                    <div class="border-blue-800 rounded-b-md md:rounded-b-none md:rounded-r-md border-t-0 md:border-t-2 border-r-2 border-b-2 border-l-2 flex-1 flex items-center justify-between px-4 font-black">
                                        <span>Overtime End</span>
                                        <span class="overtime-end">--:-- --</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Undertime Modal --}}
                    <div class="modal undertime-modal fade" id="undertime-modal" aria-labelledby="dayInMonthModal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form class="undertime-note">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-black text-xl">Undertime Notes</h5>
                                        <button type="button" class="close cancel-undertime" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                        <textarea class="notes border-2 border-blue-300 rounded-lg h-40" style="width: 90%" required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary px-4 font-black cancel-undertime" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="submit-note-btn btn btn-primary text-white px-4 font-black">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/bundy.js') }}?v={{ date('YmdHis') }}" class="bundy-script" data-timesheet="{{ $timesheet }}" defer></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
@endsection
