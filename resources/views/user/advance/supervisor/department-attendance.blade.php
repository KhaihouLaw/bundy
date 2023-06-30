@extends('layouts.app')

@section('styles')
    @include('user.shared.data-table.style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.css">
    <link rel="stylesheet" href="{{ asset('css/advance/supervisor/common.css') }}">
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="attendance-summary py-10 ">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <div id="attendance-summary-calendar" class="calendar"></div>
                    <div class="modal fade" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 1020px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title font-black text-xl">{{ Auth::user()->employee->department->getDepartment() . ' Deparment Attendance Summary' }}</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body flex justify-center" style="padding: 40px 0 40px 0;">
                                    <div class="summary flex flex-col items-center justify-center px-10 w-full">
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
    @include('user.shared.data-table.script')
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.min.js"></script>
    <script src="{{ asset('js/advance/supervisor.js') }}"></script>
@endsection