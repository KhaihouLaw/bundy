@extends('user.advance.components.base')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/face-recognition.css') }}">
@endsection
    
@section('content')
    <div class="face-recognition register py-10">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="bg-white border-b border-gray-200">

                    <div class="mt-10 mb-2 flex md:justify-center md:items-center overflow-x-auto py-16">
                        <div class="recognition flex flex-shrink-0 justify-center items-center rounded-2xl shadow-2xl">
                            <video class="rounded-2xl" width="900" height="680" id="user-video" autoplay muted></video>
                        </div>
                    </div>
                    <div class="flex justify-center items-center mb-10">
                        <div class="register flex justify-center items-center px-20 py-8 bg-gray-300 shadow-2xl">
                            <button class="px-12 py-6 bg-gray-500 text-white font-black rounded-2xl" disabled>Register</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
    <script src="{{ asset('js/face-api/face-api.min.js') }}" defer></script>
    <script src="{{ asset('js/face-register.js') }}" defer></script>
@endsection