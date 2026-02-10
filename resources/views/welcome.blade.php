<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-red-50">

            {{-- Navbar --}}
            @if (Route::has('login'))
                <nav class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-8 py-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-slate-800 hover:text-red-500 transition duration-200">
                                Invoice SaaS 🚀
                            </a>
                            <div class="flex items-center gap-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-xl focus:outline-red-500 transition duration-200">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl font-semibold text-white bg-red-500 hover:bg-red-600 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </nav>
            @endif

            {{-- Hero --}}
            <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24">
                <div class="absolute inset-0 flex justify-center items-center pointer-events-none overflow-hidden" aria-hidden="true">
                    <div class="w-[500px] h-[500px] rounded-full bg-red-400 blur-3xl opacity-20"></div>
                </div>
                <div class="relative text-center">
                    <h1 class="text-5xl md:text-6xl font-bold tracking-tight mb-6 text-gray-900">
                        Invoice SaaS 🚀
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                        Simple billing solution for small businesses. Create invoices, track payments, and get paid faster.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-5 justify-center items-center">
                        <a href="/invoice/create"
                           class="inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold text-white bg-red-500 hover:bg-red-600 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                            Create Invoice
                        </a>
                        @guest
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold text-gray-700 bg-white border-2 border-gray-200 shadow-sm hover:border-gray-300 hover:bg-gray-50 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200">
                                    Log in
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </section>

            {{-- Feature cards --}}
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12">
                    Everything you need
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition duration-200 p-8">
                        <div class="rounded-xl bg-red-50 text-red-500 p-3 inline-flex mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Create Invoices</h3>
                        <p class="text-gray-600 leading-relaxed">Generate professional invoices in seconds. Customize and send to clients with one click.</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition duration-200 p-8">
                        <div class="rounded-xl bg-red-50 text-red-500 p-3 inline-flex mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Track Payments</h3>
                        <p class="text-gray-600 leading-relaxed">See payment status at a glance. Get reminders and stay on top of what’s paid and what’s due.</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition duration-200 p-8">
                        <div class="rounded-xl bg-red-50 text-red-500 p-3 inline-flex mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Clients</h3>
                        <p class="text-gray-600 leading-relaxed">Keep client details in one place. Add, edit, and reuse for faster invoicing.</p>
                    </div>
                </div>
            </section>

            {{-- Stats (horizontal) --}}
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                <div class="bg-white/70 backdrop-blur rounded-2xl shadow-md p-10">
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-10 sm:gap-16">
                        <div class="text-center sm:text-left">
                            <p class="text-4xl font-bold text-red-500">10k+</p>
                            <p class="text-gray-600 font-medium mt-1">Invoices created</p>
                        </div>
                        <div class="hidden sm:block w-px h-12 bg-gray-200" aria-hidden="true"></div>
                        <div class="text-center sm:text-left">
                            <p class="text-4xl font-bold text-red-500">500+</p>
                            <p class="text-gray-600 font-medium mt-1">Businesses</p>
                        </div>
                        <div class="hidden sm:block w-px h-12 bg-gray-200" aria-hidden="true"></div>
                        <div class="text-center sm:text-left">
                            <p class="text-4xl font-bold text-red-500">99%</p>
                            <p class="text-gray-600 font-medium mt-1">Uptime</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="border-t border-gray-200 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <p class="text-gray-500 text-sm">© {{ date('Y') }} Invoice SaaS. All rights reserved.</p>
                    <div class="flex items-center gap-6 text-sm">
                        @if (Route::has('login'))
                            @guest
                                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 transition">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-gray-500 hover:text-gray-700 transition">Register</a>
                                @endif
                            @endguest
                        @endif
                    </div>
                </div>
            </footer>

        </div>
    </body>
</html>
