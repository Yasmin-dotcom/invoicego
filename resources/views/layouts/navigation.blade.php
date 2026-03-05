<div>
    @if(!request()->routeIs('onboarding*'))
    <aside class="h-screen fixed left-0 top-0 bg-white shadow-md flex flex-col py-4 z-50 transition-all duration-300 ease-in-out overflow-hidden"
           :class="sidebarExpanded ? 'w-48' : 'w-16'"
           x-data="{ openMenu: '{{ request()->is('settings*') ? 'settings' : '' }}' }">
        {{-- Toggle Button --}}
        <button type="button"
                @click="sidebarExpanded = !sidebarExpanded"
                class="mx-3 mb-4 flex items-center justify-center h-10 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-all duration-200"
                aria-label="Toggle sidebar">
            <svg class="h-5 w-5 transition-transform duration-300" :class="sidebarExpanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex flex-col px-3 space-y-1">
            <a href="{{ url('/') }}" title="Dashboard"
               @click="openMenu = ''"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-6v-7H10v7H4a1 1 0 0 1-1-1V9.5z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Home</span>    
            </a>
            <a href="{{ route('invoices.index') }}" title="Invoices"
               @click="openMenu = ''"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->routeIs('invoices.*') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('invoices.*') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M14 2v5h5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 13h6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 17h6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Invoices</span>
            </a>
            <a href="{{ route('clients.index') }}" title="Clients"
               @click="openMenu = ''"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->routeIs('clients.*') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('clients.*') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Clients</span>
            </a>
            <a href="{{ url('/payments') }}" title="Payments"
               @click="openMenu = ''"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->is('payments*') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->is('payments*') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 10h20" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 15h2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Payments</span>
            </a>
            <a href="{{ route('reports.index') }}" title="Reports"
               @click="openMenu = ''"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->is('reports*') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->is('reports*') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 3v18h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 16V9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 16V6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 16v-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Reports</span>
            </a>
            <a href="#" title="Settings"
               @click.prevent="openMenu = openMenu === 'settings' ? '' : 'settings'"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->routeIs('profile.*') || request()->routeIs('settings.business') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                @php
                    $isSettings = request()->routeIs('profile.*') || request()->routeIs('settings.business');
                @endphp
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     class="w-5 h-5 shrink-0 {{ $isSettings ? 'text-blue-600' : 'text-gray-800' }}">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11.983 1.875c.538 0 1.07.045 1.59.132l.285 1.708a7.5 7.5 0 012.097.874l1.52-.82a10.055 10.055 0 011.12 1.12l-.82 1.52c.36.64.655 1.332.874 2.097l1.708.285a9.987 9.987 0 010 3.18l-1.708.285a7.5 7.5 0 01-.874 2.097l.82 1.52a10.055 10.055 0 01-1.12 1.12l-1.52-.82a7.5 7.5 0 01-2.097.874l-.285 1.708a9.987 9.987 0 01-3.18 0l-.285-1.708a7.5 7.5 0 01-2.097-.874l-1.52.82a10.055 10.055 0 01-1.12-1.12l.82-1.52a7.5 7.5 0 01-.874-2.097l-1.708-.285a9.987 9.987 0 010-3.18l1.708-.285a7.5 7.5 0 01.874-2.097l-.82-1.52a10.055 10.055 0 011.12-1.12l1.52.82a7.5 7.5 0 012.097-.874l.285-1.708a10.02 10.02 0 011.59-.132z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Settings</span>
            </a>
            <div x-show="openMenu === 'settings'" x-transition class="ml-9 space-y-1 text-xs text-gray-600" style="display: none;">
                <a href="{{ route('profile.edit') }}"
                   class="block py-1 {{ request()->routeIs('profile.*') ? 'text-indigo-600 font-medium' : 'hover:text-gray-900' }}">
                    Profile
                </a>
                <a href="{{ route('settings.business') }}"
                   class="block py-1 {{ request()->routeIs('settings.business') ? 'text-indigo-600 font-medium' : 'hover:text-gray-900' }}">
                    Business Information
                </a>
                <a href="{{ route('settings.templates') }}"
                   class="block py-1 {{ request()->routeIs('settings.templates') ? 'text-indigo-600 font-medium' : 'hover:text-gray-900' }}">
                    Invoice Templates
                </a>
            </div>
            <a href="{{ route('upgrade') }}" title="Upgrade"
               class="flex items-center gap-3 h-10 px-3 rounded-lg text-gray-600 transition-colors duration-200 {{ request()->routeIs('upgrade') ? 'bg-gray-100 text-indigo-600 font-medium border-l-4 border-indigo-500' : 'border-l-4 border-transparent hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('upgrade') ? 'text-blue-600' : 'text-gray-800' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M7 17L17 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 7h10v10" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-medium truncate" x-show="sidebarExpanded" x-transition style="display: none;">Upgrade</span>
            </a>
        </div>
    </aside>
    @endif

    <nav x-data="{ open: false }" class="bg-white shadow-md">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
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
