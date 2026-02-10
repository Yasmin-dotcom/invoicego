<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="font-sans antialiased">

<div class="pl-16 min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100">

    {{-- ================= TOP NAVIGATION (unchanged) ================= --}}
    @include('layouts.navigation')


    {{-- ================= STICKY HEADER (FIXED + SHADOW ADDED) ================= --}}
    @isset($header)
    <header
        class="fixed top-0 left-16 right-0 z-50
               bg-white/95 backdrop-blur-md
               shadow-md
               border-b border-gray-200">

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
    @endisset


    {{-- ================= PAGE CONTENT ================= --}}
    {{-- pt-20 pushes content below fixed header --}}
    <main class="pt-20">

        {{-- Upgrade success banner --}}
        @if (session('upgraded_success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-900">
                    <div class="text-sm font-semibold">
                        🎉 You are now on Pro plan. Enjoy unlimited invoices!
                    </div>
                </div>
            </div>
        @endif

        {{ $slot }}

    </main>

</div>


{{-- Optional stacks --}}
@stack('modals')
@stack('scripts')

</body>
</html>
