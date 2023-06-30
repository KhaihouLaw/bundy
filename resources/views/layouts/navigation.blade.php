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
                @if (\Auth::user()->hasRole('admin'))
                    <x-nav-link :href="route('admin_dashboard')" :active="request()->routeIs('admin_dashboard')">
                        {{ __('Administrator') }}
                    </x-nav-link>
                @endif
                    <x-nav-link :href="route('bundy')" :active="request()->routeIs('bundy')">
                        {{ __('Bundy') }}
                    </x-nav-link>
                    <x-nav-link :href="route('timesheets')" :active="request()->routeIs('timesheets')">
                        {{ __('Timesheets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('all_leave_requests')" :active="request()->routeIs('all_leave_requests')">
                        {{ __('Leave Requests') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div class="user-info">
                                <div>
                                    Welcome <strong>{{ Auth::user()->name }}!</strong>
                                </div>
                            </div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="GET" action="{{ route('advance') }}"> 
                            @csrf
                            <x-dropdown-link :href="route('advance')"
                                    class="space-x-8"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" style="text-decoration: none;">
                                <i class="fa fa-cogs text-lg" aria-hidden="true"></i>
                                <span>{{ __('Advance') }}</span>
                            </x-dropdown-link>
                        </form>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    class="space-x-8"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" style="text-decoration: none;">
                                <i class="fa fa-sign-out text-lg" aria-hidden="true"></i>
                                <span>{{ __('Log Out') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (\Auth::user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('admin_dashboard')" :active="request()->routeIs('admin_dashboard')">
                    {{ __('Administrator') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('bundy')" :active="request()->routeIs('bundy')">
                {{ __('Bundy') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('timesheets')" :active="request()->routeIs('timesheets')">
                {{ __('Timesheets') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('all_leave_requests')" :active="request()->routeIs('all_leave_requests')">
                {{ __('Leave Requests') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
