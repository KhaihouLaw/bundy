@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/vue/vue.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timesheet-records.css') }}">
    <script src="https://momentjs.com/downloads/moment.js"></script>
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection
    
@section('content')
    <div id="app" class="timesheet-root py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-white border-b border-gray-200">
                    <timesheet-records login-token={{ Auth::user()->login_token }}></timesheet-records>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
@endsection
