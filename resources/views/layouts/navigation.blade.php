<div>
    <aside class="w-16 h-screen fixed left-0 top-0 bg-gray-900 text-gray-400 flex flex-col items-center py-6 space-y-6 border-r border-gray-800 z-50">
        <a
            href="{{ route('dashboard') }}"
            title="Dashboard"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9.5z" />
            </svg>
        </a>
        <a
            href="{{ route('invoices.index') }}"
            title="Invoices"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->routeIs('invoices.*') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm7 1.5V9h4.5L14 4.5zM8 12h8v1.5H8V12zm0 4h8v1.5H8V16z" />
            </svg>
        </a>
        <a
            href="{{ route('clients.index') }}"
            title="Clients"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->routeIs('clients.*') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0zm-12 9a6 6 0 0 1 12 0v1H4v-1zm14.5-7.5a3.5 3.5 0 1 0-3.3-5 5.5 5.5 0 0 1 0 10 3.5 3.5 0 0 0 3.3-5zM20 20v1h-2v-1a4.98 4.98 0 0 0-1.1-3.1 5.45 5.45 0 0 1 3.1 3.1z" />
            </svg>
        </a>
        <a
            href="{{ url('/payments') }}"
            title="Payments"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->is('payments*') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M4 6h16a2 2 0 0 1 2 2v1H2V8a2 2 0 0 1 2-2zm18 6v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4h20zm-3 2h-4v2h4v-2z" />
            </svg>
        </a>
        <a
            href="{{ url('/reports') }}"
            title="Reports"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->is('reports*') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5zm3 14H6V9h2v8zm5 0h-2V5h2v12zm5 0h-2v-6h2v6z" />
            </svg>
        </a>
        <a
            href="{{ route('profile.edit') }}"
            title="Settings"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm9 3.5a7.8 7.8 0 0 0-.1-1l2-1.6-2-3.4-2.4 1a8.9 8.9 0 0 0-1.7-1l-.3-2.6H9.5l-.3 2.6a8.9 8.9 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.6a7.8 7.8 0 0 0 0 2l-2 1.6 2 3.4 2.4-1a8.9 8.9 0 0 0 1.7 1l.3 2.6h5l.3-2.6a8.9 8.9 0 0 0 1.7-1l2.4 1 2-3.4-2-1.6a7.8 7.8 0 0 0 .1-1z" />
            </svg>
        </a>
        <a
            href="{{ route('upgrade') }}"
            title="Upgrade"
            class="w-10 h-10 flex items-center justify-center rounded-xl transition hover:bg-gray-800 {{ request()->routeIs('upgrade') ? 'bg-indigo-600 text-white' : '' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2l2.1 4.3L19 7l-3.5 3.4.8 4.6L12 12.9 7.7 15l.8-4.6L5 7l4.9-.7L12 2zM4 20h16v2H4z" />
            </svg>
        </a>
    </aside>

    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                    {{-- Plan Badge --}}
                    <x-plan-badge />

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center gap-2">
                                    <div>{{ auth()->user()->name }}</div>
                                    @if(auth()->user()?->isPlanPro())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-600 text-white">
                                            PRO
                                        </span>
                                    @endif
                                </div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (
                                auth()->check() &&
                                in_array(auth()->user()->role, ['owner', 'client'], true) &&
                                ! auth()->user()?->isPlanPro() &&
                                auth()->user()->plan === 'free'
                            )
                                <x-dropdown-link :href="route('upgrade')">
                                    Upgrade to Pro
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endauth

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
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
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 space-y-1">
                    <div class="font-medium text-base text-gray-800 flex items-center gap-2">
                        <span>{{ auth()->user()->name }}</span>
                        @if(auth()->user()?->isPlanPro())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-600 text-white">
                                PRO
                            </span>
                        @endif
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ auth()->user()->email }}
                    </div>

                    <div class="pt-2">
                        <x-plan-badge />
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (
                        auth()->check() &&
                        in_array(auth()->user()->role, ['owner', 'client'], true) &&
                        ! auth()->user()?->isPlanPro() &&
                        auth()->user()->plan === 'free'
                    )
                        <x-responsive-nav-link :href="route('upgrade')">
                            Upgrade to Pro
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>
</div>
