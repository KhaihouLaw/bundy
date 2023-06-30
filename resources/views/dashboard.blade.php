@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vue/vue.css') }}">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/js-year-calendar@latest/dist/js-year-calendar.min.css" />
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
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

                    <!-- Weather Forecast -->
                    <a class="weatherwidget-io" href="https://forecast7.com/en/14d96120d79/apalit/" data-label_1="APALIT" data-label_2="WEATHER" data-theme="original" >APALIT WEATHER</a>
                    <script>
                    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');
                    </script>

                    <br>
                    <div class="space-y-12">
                        <div class='row'>
                            <!-- 1st Column -->
                            <div class='col-md-4'>
                                <div class="card">
                                    <div class="card-header">
                                        Clock for Today
                                    </div>
                                    <div class="card-body">
                                    @if ($has_timesheet_today)
                                        @if ($timesheet->hasClockedInToday())
                                        <h5 class="card-title">Review your timesheet</h5>
                                        <p class="card-text">Please review your time logs for today on the bundy.</p>
                                        <a href="{{ route('bundy') }}" class="btn btn-primary text-white">Proceed to Bundy</a>
                                        @else
                                        <h5 class="card-title">Friendly Reminder</h5>
                                        <p class="card-text">You have not clocked in today yet, don't forget to clock in.</p>
                                        <a href="{{ route('bundy') }}" class="btn btn-warning text-white">Proceed to Bundy</a>
                                        @endif
                                    @else
                                        <h5 class="card-title">Hello</h5>
                                        <p class="card-text">You don't have a timesheet set for today, please contact HR if you are scheduled to work today.</p>
                                    @endif

                                    </div>
                                </div>
                            </div>
                            <!-- 2nd Column -->
                            <div class='col-md-4'>
                                <div class="card">
                                    <div class="card-header">
                                        Requests
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Timesheet Adjustment Requests (<strong>{{ $adjustments->count() }}</strong>)</h5>
                                        @forelse ($adjustments as $adjustment)
                                        <p class="card-text">{{ $adjustment->getTimesheetDate() }} {{ $adjustment->getStatus() }}</p>
                                        @empty
                                        <p class="card-text">No pending timesheet adjustments.</p>
                                        @endforelse
                                        <h5 class="card-title">Leave Requests (<strong>{{ $leave_requests->count() }}</strong>)</h5>
                                        @forelse ($leave_requests as $leave_request)
                                        <p class="card-text">{{ $leave_request->getStartDate() }} {{ $leave_request->leaveType->getName() }} {{ $leave_request->getStatus() }}</p>
                                        @empty
                                        <p class="card-text">No pending leave requests.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <!-- 3rd Column -->
                            <div class='col-md-4'>
                                <div class="card">
                                    <div class="card-header">
                                        Team Attendance
                                    </div>
                                    <div class="card-body">
                                        @if (!is_null($employee->department))
                                        <h5 class="card-title">{{ $employee->department->getDepartment() }}</h5>
                                        @endif
                                        <p class="card-text">
                                        @foreach ($employee->colleagues as $colleague)
                                            <h6>{{ $colleague->getFullName() }} @if ($colleague->isClockedInToday())<span class="badge bg-success text-white">Present</span>@else<span class="badge bg-secondary text-white">Not yet clocked in</span>@endif</h6>
                                        @endforeach
                                        </p>
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
