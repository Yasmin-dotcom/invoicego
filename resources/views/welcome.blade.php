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
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100">
            @if (Route::has('login'))
                <div class="p-6 text-end">
                    @auth
                        <a href="{{ route('dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="flex justify-center items-center px-6 py-10">
                <div class="bg-white rounded-xl shadow-md p-10 text-center w-full max-w-xl">
                    <h2 class="text-3xl font-bold text-red-500 mb-4">
                        Invoice SaaS 🚀
                    </h2>

                    <p class="text-gray-600 mb-6">
                        Simple billing solution for small businesses
                    </p>

                    <a href="/invoice/create"
                       class="inline-block bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600">
                        Create Invoice
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
