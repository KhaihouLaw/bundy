
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="login-token" content="{{ Auth::user()->login_token }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <link rel="stylesheet" href="{{ asset('css/face-recognition.css') }}">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">

            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}" class="grid justify-items-center">
                                    <img src="/images/homepage_images/lvcc.png" alt="LVCC Logo" class="w-28 fill-current" style="width: 50px">
                                </a>
                            </div>
            
                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <x-nav-link :href="route('advance')" :active="request()->routeIs('advance')">
                                    {{ __('Back') }}
                                </x-nav-link>
                                <x-nav-link :href="route('bundy')" :active="request()->routeIs('bundy')">
                                    {{ __('QR Clock') }}
                                </x-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
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
            </main>
        </div>

        <script src="{{ asset('js/app.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

        <script src="https://rawgit.com/notifyjs/notifyjs/master/dist/notify.js"></script>
        <script src="{{ asset('js/face-api/face-api.min.js') }}" defer></script>
        <script src="{{ asset('js/face-clock.js') }}" defer></script>
    </body>
</html>