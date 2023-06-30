@section('javascript')
    <script src="{{ asset('js/registration.js') }}"></script>
@endsection

<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="grid justify-items-center">
                <img src="./assets/icons/lvcc.png" alt="LVCC Icon" class="w-28 fill-current">
                <span class="text-3xl text-gray-600 font-black" style="font-family: sans-serif, Arial, Helvetica;">LVCC Bundy</span>
            </a>
        </x-slot>

        <form id="register-form" class="register-form" method="POST" action="{{ route('register') }}">
            @csrf

            <!-- First Name -->
            <div>
                <x-label for="first-name" :value="__('First Name')" />
                <div class="first-name-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300 border-blue-300 border-indigo-300 ring ring-indigo-200 ring-opacity-50">
                    <x-input id="first-name" class="first-name border-0 block w-full bg-transparent" type="text" name="first-name" :value="old('first-name')" required autofocus />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Middle Name -->
            <div class="mt-4">
                <x-label for="middle-name" :value="__('Middle Name')" />
                <div class="middle-name-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="middle-name" class="middle-name border-0 block w-full bg-transparent" type="text" name="middle-name" :value="old('middle-name')" required />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Last Name -->
            <div class="mt-4">
                <x-label for="last-name" :value="__('Last Name')" />
                <div class="last-name-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="last-name" class="last-name border-0 block w-full bg-transparent" type="text" name="last-name" :value="old('last-name')" required />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Birth Date -->
            <div class="mt-4">
                <x-label for="birth-date" :value="__('Birth Date')" />
                <div class="birth-date-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="birth-date" class="birth-date border-0 block w-full bg-transparent" type="date" name="birth-date" :value="old('birth-date')" required />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Department -->
            <div class="mt-4">
                <x-label for="department" :value="__('Department')" />
                <div class="department-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <select id="department" class="department border-0 block w-full bg-transparent" name="department" :value="old('department')" required>
                        <option value="0" selected disabled hidden>Select an Option</option>
                        <option value="1">HR</option>
                        <option value="2">College</option>
                        <option value="3">High School</option>
                        <option value="4">Elementary</option>
                        <option value="5">Administration</option>
                        <option value="6">Maintenance</option>
                    </select>
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('Email')" />
                <div class="email-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="email" class="email border-0 block w-full bg-transparent"
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />
                <div class="password-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="password" class="password border-0 block w-full bg-transparent"
                        type="password"
                        name="password"
                        required autocomplete="new-password" />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
                <p class="text-xs text-red-400"></p>
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />
                <div class="password-confirm-container flex justify-center items-center mt-px rounded-md shadow-sm border border-gray-300">
                    <x-input id="password_confirmation" class="password-confirm border-0 block w-full bg-transparent"
                        type="password"
                        name="password_confirmation" required />
                    <i class="warning material-icons-outlined mr-2 text-red-500 hidden">report_problem</i> 
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <x-button class="register-btn ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>

        {{-- <x-slot name="footer">
            <span class="mt-5 text-xs">Copyright 2021. All rights reserved.</span>
        </x-slot> --}}

         <!-- Registration Errors -->
         <x-auth-registration-errors class="grid justify-items-center mb-1.5" :errors="$errors" />
    </x-auth-card>
</x-guest-layout>
