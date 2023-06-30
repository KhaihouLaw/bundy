<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="grid justify-items-center">
                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                <img src="./assets/icons/lvcc.png" alt="LVCC Icon" class="w-28 fill-current">
                <span class="text-3xl text-gray-600 font-black" style="font-family: sans-serif, Arial, Helvetica;">LVCC Bundy</span>
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="grid justify-items-center mb-1.5" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label class="font-bold" for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label class="font-bold" for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-500 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="flex justify-center bg-blue-700 h-12 w-28 font-black text-sm">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>

        <span class="grid justify-items-center">
            <hr class="w-full h-0.5 bg-gray-300 mt-5">
            <span class="pl-3 pr-3 items-center -mt-3.5 mb-2 bg-white">or</span>
        </span>
        
        <div class="grid justify-items-center">
            <a href="{{ url('/auth/redirect') }}" class="flex items-center border-2 border-gray-200 rounded py-3 px-6">
                <img src="./assets/icons/google.svg" alt="Google Icon" class="w-8 mr-2">
                {{ __('Sign In with Google') }}
            </a>
        </div>

        <x-slot name="footer">
            <span class="mt-5 text-xs">Copyright 2021. All rights reserved.</span>
        </x-slot>

    </x-auth-card>
</x-guest-layout>
