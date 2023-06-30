@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/face-recognition.css') }}">
@endsection

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection
    
@section('content')
    <div class="face-recognition clock py-10 flex md:justify-center">
        <div class="mx-auto sm:px-6 lg:px-8"> 
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="py-4 px-8 border-b border-gray-200">
                    <div class="flex flex-wrap justify-center items-center space-x-4">
                        <div class="p-1 flex md:justify-center md:items-center overflow-x-auto rounded-2xl shadow-2xl">
                            <div class="recognition flex flex-shrink-0 justify-center items-center">
                                <video class="rounded-2xl" width="1220" height="910" id="user-video" autoplay muted></video>
                            </div>
                        </div>
                        <div class="logs flex flex-shrink-0 justify-center items-center">
                            <div class="box p-4 shadow-2xl rounded-2xl overflow-y-auto">
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
    <script src="{{ asset('js/face-api/face-api.min.js') }}" defer></script>
    <script src="{{ asset('js/face-clock.js') }}" defer></script>
@endsection