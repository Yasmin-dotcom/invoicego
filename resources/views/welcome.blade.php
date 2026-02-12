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
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50">

            {{-- Navbar --}}
            @if (Route::has('login'))
                <nav class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto px-8 py-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-slate-800 hover:text-indigo-600 transition duration-200">
                                Invoice SaaS 🚀
                            </a>
                            <div class="flex items-center gap-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-xl focus:outline-indigo-500 transition duration-200">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </nav>
            @endif

            <!-- 🚀 Compact Launch Offer Banner -->
<section class="w-full bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 py-4">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">

    <!-- LEFT CONTENT -->
    <div class="text-center md:text-left">

        <h3 class="text-lg md:text-xl font-semibold text-white">
            🚀 Launch Offer — Limited Time
        </h3>

        <p class="text-sm md:text-base text-white/90 mt-1">
            Start creating GST invoices today with early access benefits.
        </p>

    </div>

    <!-- CTA BUTTON -->
    <div>
        <a href="{{ route('register') }}"
           class="inline-flex items-center justify-center bg-white text-indigo-600 font-semibold px-6 py-2 rounded-full shadow-md hover:shadow-lg hover:scale-105 transition duration-200">
            Grab Offer
        </a>
    </div>

</div>

</section>

            {{-- Hero --}}
            <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-16 md:pt-10 md:pb-20">
                <div class="absolute inset-0 flex justify-center items-center pointer-events-none overflow-hidden" aria-hidden="true">
                    <div class="w-[500px] h-[500px] rounded-full bg-indigo-400 blur-3xl opacity-20"></div>
                </div>
                <div class="relative grid lg:grid-cols-2 gap-16 items-start min-h-[80vh]">
                    {{-- Left: heading + form --}}
                    <div>
                        <h1 class="text-5xl font-bold tracking-tight text-gray-900 mb-6">
                        Start Billing in Minutes🚀
                        </h1>
                        <p class="text-lg text-gray-600 max-w-xl mb-8 leading-relaxed">
                            Simple GST billing solution for small businesses. Create invoices, track payments, and get paid faster.
                        </p>
                        <x-hero-register />

                    </div>
                    {{-- Right: SEO content (no image) --}}
                    <div class="hidden lg:flex flex-col justify-start max-w-xl pl-16">

                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight tracking-tight">
    GST Billing & Invoice Software
    <span class="text-indigo-500">for Small Businesses in India</span>
</h1>

    <h2 class="mt-4 text-lg text-gray-600 leading-relaxed">
        Create invoices, track payments, manage GST and send reminders on
        WhatsApp & Email — all in one simple dashboard.
    </h2>

    <ul class="mt-6 space-y-3 text-gray-600 text-base">
        <li>✅ GST ready invoices</li>
        <li>✅ Razorpay online payments</li>
        <li>✅ WhatsApp & email reminders</li>
        <li>✅ Client & expense tracking</li>
        <li>✅No credit card required</li>
    </ul>

</div>
  </div>
     </section>
      
{{-- Trust Icons Section --}}
<!--TEMPORARY COMMENTED OUT CODE
<section class="bg-gradient-to-b from-white to-gray-50 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

    <div class="grid grid-cols-2 md:grid-cols-5 gap-10 text-center">

        <!-- Item 1 --
        <div>
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <!-- Smile Icon --
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.828 14.828a4 4 0 01-5.656 0M9 9h.01M15 9h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-semibold text-gray-900">1,200+</p>
            <p class="text-gray-500 text-sm mt-1">Happy Businesses</p>
        </div>

        <!-- Item 2 --
        <div>
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <!-- Mobile Icon --
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="7" y="4" width="10" height="16" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 18h2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-semibold text-gray-900">Free</p>
            <p class="text-gray-500 text-sm mt-1">Mobile Access</p>
        </div>

        <!-- Item 3 --
        <div>
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <!-- Star Icon --
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l2.037 6.26a1 1 0 00.95.69h6.58c.969 0 1.371 1.24.588 1.81l-5.327 3.87a1 1 0 00-.364 1.118l2.037 6.26c.3.921-.755 1.688-1.538 1.118l-5.327-3.87a1 1 0 00-1.175 0l-5.327 3.87c-.783.57-1.838-.197-1.538-1.118l2.037-6.26a1 1 0 00-.364-1.118L.382 11.687c-.783-.57-.38-1.81.588-1.81h6.58a1 1 0 00.95-.69l2.037-6.26z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-semibold text-gray-900">4.8 / 5</p>
            <p class="text-gray-500 text-sm mt-1">Customer Rating</p>
        </div>

        <!-- Item 4 --
        <div>
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <!-- Desktop Icon --
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="12" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 20h8M12 16v4" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-semibold text-gray-900">Multi-Device</p>
            <p class="text-gray-500 text-sm mt-1">Mobile & Desktop</p>
        </div>

        <!-- Item 5 --
        <div>
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <!-- Users Icon --
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-1a4 4 0 00-4-4h-1M9 20H4v-1a4 4 0 014-4h1M12 12a4 4 0 100-8 4 4 0 000 8z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-semibold text-gray-900">Multi-User</p>
            <p class="text-gray-500 text-sm mt-1">Team Access</p>
        </div>

    </div>

</section>
--> 
 
{{-- Trust & Highlights Section --}}
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-12 text-center">

            {{-- Stat 1 --}}
            <div class="group transition duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                    😊
                </div>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">1,200+</h3>
                <p class="mt-2 text-gray-600 text-sm">Happy Businesses</p>
            </div>

            {{-- Stat 2 --}}
            <div class="group transition duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                    📱
                </div>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Free</h3>
                <p class="mt-2 text-gray-600 text-sm">Mobile Access</p>
            </div>

            {{-- Stat 3 --}}
            <div class="group transition duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                    ⭐
                </div>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">4.8 / 5</h3>
                <p class="mt-2 text-gray-600 text-sm">Customer Rating</p>
            </div>

            {{-- Stat 4 --}}
            <div class="group transition duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                    💻
                </div>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Multi-Device</h3>
                <p class="mt-2 text-gray-600 text-sm">Mobile & Desktop</p>
            </div>

            {{-- Stat 5 --}}
            <div class="group transition duration-300">
                <div class="w-16 h-16 mx-auto flex items-center justify-center rounded-full bg-indigo-50 text-indigo-600 text-2xl group-hover:bg-indigo-600 group-hover:text-white transition">
                    👥
                </div>
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Multi-User</h3>
                <p class="mt-2 text-gray-600 text-sm">Team Access</p>
            </div>

        </div>

    </div>
</section>

     {{-- Section 1: Create & Share Invoices (Image RIGHT, Text LEFT) --}}

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

            <div class="grid grid-cols-1 lg:grid-cols-2 items-start gap-12 lg:gap-16">

   <!-- LEFT SIDE TEXT -->
<div class="mt-6 lg:mt-10 flex flex-col justify-center">

            <!-- Heading -->
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-4">
                Create & Share Invoices Instantly
            </h2>

            <!-- Replace your paragraph block with this -->
            <div x-data="{ open: false }">

                <!-- Always visible short intro -->
                <p class="text-lg text-gray-600 leading-relaxed">
                    Generate professional invoices and send them via WhatsApp or Email in seconds.
                </p>

                <!-- Hidden long content -->
                <div 
                    x-show="open"
                    x-transition
                    class="mt-4 space-y-4 text-lg text-gray-600 leading-relaxed">
                    <p>
                        Download PDF invoices, track delivery status, and manage billing history easily from your dashboard.
                    </p>

                    <p>
                        Customize GST-ready invoice formats, add your logo, tax details, and payment terms with just a few clicks.
                    </p>

                    <p>
                        Stay organized with automatic numbering, payment tracking, and real-time updates — all in one simple system.
                    </p>
                </div>

                <!-- Button -->
                <button 
                    type="button"
                    @click="open = !open"
                    class="mt-4 inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700 transition"
                >
                    <span x-text="open ? '– Read less' : '+ Read more'"></span>
                </button>

            </div>

        </div>
        <!-- RIGHT SIDE IMAGE -->
        <div class="flex justify-center lg:justify-end">
            <img 
                src="{{ asset('images/hero-dashboard2.jpg') }}" 
                alt="GST Invoice Dashboard"
                class="w-full max-w-md lg:max-w-lg object-contain"
            >
        </div>
  </div>
</section>

            
 {{-- Section 2: Track Payments & Expenses (Image LEFT, Text RIGHT) --}}

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="grid grid-cols-1 lg:grid-cols-2 items-start gap-12 lg:gap-16">

        <!-- LEFT SIDE IMAGE -->
        <div class="flex justify-center lg:justify-start lg:order-1">
            <img 
                src="{{ asset('images/hero-dashboard2.jpg') }}" 
                alt="Track Payments and Expenses"
                class="w-full max-w-md lg:max-w-lg object-contain">
        </div>

        <!-- RIGHT SIDE TEXT -->
        <div class="lg:order-2 mt-6 lg:mt-10">

            <!-- Heading -->
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-4">
                Track Payments & Expenses Easily
            </h2>

            <!-- Paragraph Block -->
            <div x-data="{ open: false }">

                <!-- Always visible short intro -->
                <p class="text-lg text-gray-600 leading-relaxed">
                    Monitor paid, unpaid, and overdue invoices in real time from one dashboard.
                </p>

                <!-- Hidden long content -->
                <div 
                    x-show="open"
                    x-transition
                    class="mt-4 space-y-4 text-lg text-gray-600 leading-relaxed">
                    <p>
                        Automatically track payment status and get instant visibility into outstanding balances.
                    </p>

                    <p>
                        Record expenses, categorize transactions, and generate detailed financial summaries anytime.
                    </p>

                    <p>
                        Stay in control of your cash flow with smart tracking tools built for small businesses.
                    </p>
                </div>

                <!-- Button -->
                <button 
                    type="button"
                    @click="open = !open"
                    class="mt-4 inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700 transition"
                >
                    <span x-text="open ? '– Read less' : '+ Read more'"></span>
                </button>

            </div>

        </div>

    </div>

</section>

{{-- Section 3: Everything you need (Image RIGHT, Text LEFT) --}}


<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

<div class="grid grid-cols-1 lg:grid-cols-2 items-start gap-12 lg:gap-16">

<!-- LEFT SIDE TEXT -->
<div class="mt-6 lg:mt-10 flex flex-col justify-center">

<!-- Heading -->
<h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-4">
    Create & Share Invoices Instantly
</h2>

<!-- Replace your paragraph block with this -->
<div x-data="{ open: false }">

    <!-- Always visible short intro -->
    <p class="text-lg text-gray-600 leading-relaxed">
        Generate professional invoices and send them via WhatsApp or Email in seconds.
    </p>

    <!-- Hidden long content -->
    <div 
        x-show="open"
        x-transition
        class="mt-4 space-y-4 text-lg text-gray-600 leading-relaxed">
                    <p>
                        Download PDF invoices, track delivery status, and manage billing history easily from your dashboard.
                    </p>
                    <p>
                        Customize GST-ready invoice formats, add your logo, tax details, and payment terms with just a few clicks.
                    </p>
                    <p>
                        Customize GST-ready invoice formats, add your logo, tax details, and payment terms with just a few clicks.
                    </p>

                    <p>
                        Stay organized with automatic numbering, payment tracking, and real-time updates — all in one simple system.
                    </p>
                </div>

                <!-- Button -->
                <button 
                    type="button"
                    @click="open = !open"
                    class="mt-4 inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700 transition">
                    <span x-text="open ? '– Read less' : '+ Read more'"></span>
                </button>

            </div>

        </div>
        <!-- RIGHT SIDE IMAGE -->
        <div class="flex justify-center lg:justify-end">
            <img 
                src="{{ asset('images/hero-dashboard2.jpg') }}" 
                alt="GST Invoice Dashboard"
                class="w-full max-w-md lg:max-w-lg object-contain rounded-2xl shadow-lg"
            >
        </div>
  </div>
</section>

{{-- Section 4: Manage Clients (Image RIGHT, Text LEFT) --}}

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="grid grid-cols-1 lg:grid-cols-2 items-start gap-12 lg:gap-16">

        <!-- LEFT SIDE IMAGE -->
        <div class="flex justify-center lg:justify-start lg:order-1">
            <img 
                src="{{ asset('images/hero-dashboard2.jpg') }}" 
                alt="Track Payments and Expenses"
                class="w-full max-w-md lg:max-w-lg object-contain">
        </div>

       <!-- RIGHT SIDE TEXT -->
       <div class="lg:order-2 mt-6 lg:mt-10">

            <!-- Heading -->
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight mb-4">
                Track Payments & Expenses Easily
            </h2>

            <!-- Paragraph Block -->
            <div x-data="{ open: false }">

                <!-- Always visible short intro -->
                <p class="text-lg text-gray-600 leading-relaxed">
                    Monitor paid, unpaid, and overdue invoices in real time from one dashboard.
                </p>

                <!-- Hidden long content -->
                <div 
                    x-show="open"
                    x-transition
                    class="mt-4 space-y-4 text-lg text-gray-600 leading-relaxed">
                    <p>
                        Automatically track payment status and get instant visibility into outstanding balances.
                    </p>

                    <p>
                        Record expenses, categorize transactions, and generate detailed financial summaries anytime.
                    </p>

                    <p>
                        Stay in control of your cash flow with smart tracking tools built for small businesses.
                    </p>
                </div>

                <!-- Button -->
                <button 
                    type="button"
                    @click="open = !open"
                    class="mt-4 inline-flex items-center font-semibold text-indigo-600 hover:text-indigo-700 transition"
                >
                    <span x-text="open ? '– Read less' : '+ Read more'"></span>
                </button>

            </div>

        </div>

    </div>

</section>


{{--TEMPORARY COMMENTED OUT CODE ENDS HERE --}}

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4">

        <!-- SEO Heading -->
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-6">
            GST Billing & Invoicing Software Features for Small Businesses
        </h2>

        <p class="text-center text-gray-600 mb-14 max-w-2xl mx-auto">
            Create GST-compliant invoices, track payments, manage clients,
            and automate reminders — all from one powerful and easy-to-use dashboard.
        </p>


        <!-- Accordion Wrapper -->
        <div class="space-y-4">

            <!-- Item 1 -->
            <div x-data="{ open: false }" class="border rounded-xl overflow-hidden">
                <button @click="open = !open"
                        class="w-full flex justify-between items-center p-6 text-left">

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-lg">
                            📄
                        </div>
                        <h3 class="text-lg font-semibold">
                            GST Compliant Invoicing
                        </h3>
                    </div>

                    <div class="w-8 h-8 flex items-center justify-center rounded-full border text-indigo-600 transition duration-300"
                         :class="{ 'rotate-45': open }">
                        +
                    </div>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed">
                    Generate professional GST-ready invoices with automatic tax calculations,
                    HSN/SAC support, PDF export, and instant sharing via WhatsApp or Email.
                </div>
            </div>


            <!-- Item 2 -->
            <div x-data="{ open: false }" class="border rounded-xl overflow-hidden">
                <button @click="open = !open"
                        class="w-full flex justify-between items-center p-6 text-left">

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-lg">
                            💳
                        </div>
                        <h3 class="text-lg font-semibold">
                            Online Payments (Razorpay Integration)
                        </h3>
                    </div>

                    <div class="w-8 h-8 flex items-center justify-center rounded-full border text-indigo-600 transition duration-300"
                         :class="{ 'rotate-45': open }">
                        +
                    </div>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed">
                    Accept secure online payments directly from invoices and
                    automatically track payment status in real time to improve cash flow.
                </div>
            </div>


            <!-- Item 3 -->
            <div x-data="{ open: false }" class="border rounded-xl overflow-hidden">
                <button @click="open = !open"
                        class="w-full flex justify-between items-center p-6 text-left">

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-lg">
                            📊
                        </div>
                        <h3 class="text-lg font-semibold">
                            Reports & Business Insights
                        </h3>
                    </div>

                    <div class="w-8 h-8 flex items-center justify-center rounded-full border text-indigo-600 transition duration-300"
                         :class="{ 'rotate-45': open }">
                        +
                    </div>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed">
                    View profit reports, pending payments, GST summaries,
                    expense tracking, and financial performance analytics —
                    all in one easy dashboard.
                </div>
            </div>


            <!-- Item 4 -->
            <div x-data="{ open: false }" class="border rounded-xl overflow-hidden">
                <button @click="open = !open"
                        class="w-full flex justify-between items-center p-6 text-left">

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-lg">
                            📱
                        </div>
                        <h3 class="text-lg font-semibold">
                            Multi-Device Access
                        </h3>
                    </div>

                    <div class="w-8 h-8 flex items-center justify-center rounded-full border text-indigo-600 transition duration-300"
                         :class="{ 'rotate-45': open }">
                        +
                    </div>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed">
                    Access your billing dashboard from desktop, tablet,
                    or mobile — anytime, anywhere.
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-24 bg-gray-100">
    <div class="max-w-6xl mx-auto px-6">

        <!-- Heading -->
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 text-indigo-700">
            GST Billing Software FAQs
        </h2>

        <p class="text-center text-gray-600 max-w-2xl mx-auto mb-16">
            Everything you need to know about our GST billing software,
            pricing, security, and online payments.
        </p>

        <!-- FAQ Wrapper -->
        <div class="space-y-4">

            <!-- FAQ Item -->
            <div x-data="{ open: false }"
                 class="bg-white border border-gray-300 rounded-lg">

                <button @click="open = !open"
                    class="w-full flex justify-between items-center px-6 py-5 text-left">

                    <h3 class="text-lg font-semibold">
                        Is this GST billing software compliant with Indian GST rules?
                    </h3>

                    <!-- Blue Arrow -->
                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-5 h-5 text-indigo-600 transition-transform duration-300"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed border-t border-gray-200">
                    Yes. Our software supports CGST, SGST, IGST,
                    HSN/SAC codes, automatic tax calculation,
                    and professional GST invoice formats.
                </div>
            </div>


            <!-- FAQ Item -->
            <div x-data="{ open: false }"
                 class="bg-white border border-gray-300 rounded-lg">

                <button @click="open = !open"
                    class="w-full flex justify-between items-center px-6 py-5 text-left">

                    <h3 class="text-lg font-semibold">
                        Is my business data and password secure?
                    </h3>

                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-5 h-5 text-indigo-600 transition-transform duration-300"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed border-t border-gray-200">
                    Absolutely. We use encrypted authentication,
                    secure cloud hosting, and protected infrastructure
                    to keep your invoices and passwords completely safe.
                </div>
            </div>


            <!-- FAQ Item -->
            <div x-data="{ open: false }"
                 class="bg-white border border-gray-300 rounded-lg">

                <button @click="open = !open"
                    class="w-full flex justify-between items-center px-6 py-5 text-left">

                    <h3 class="text-lg font-semibold">
                        Does it support secure online payments?
                    </h3>

                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-5 h-5 text-indigo-600 transition-transform duration-300"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed border-t border-gray-200">
                    Yes. Integrated with Razorpay for seamless,
                    fast, and secure online payment collection.
                </div>
            </div>


            <!-- FAQ Item -->
            <div x-data="{ open: false }"
                 class="bg-white border border-gray-300 rounded-lg">

                <button @click="open = !open"
                    class="w-full flex justify-between items-center px-6 py-5 text-left">

                    <h3 class="text-lg font-semibold">
                        Is it affordable for small businesses?
                    </h3>

                    <svg :class="open ? 'rotate-180' : ''"
                        class="w-5 h-5 text-indigo-600 transition-transform duration-300"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     class="px-6 pb-6 text-gray-600 leading-relaxed border-t border-gray-200">
                    Yes. Designed specifically for small businesses
                    and startups with affordable pricing and powerful features.
                </div>
            </div>

        </div>

    </div>
</section>


{{-- Why Choose Us Section --}}

<section class="py-20 bg-gray-50">
    <div 
        x-data="{ active: 0, total: 3 }"
        class="max-w-6xl mx-auto px-4">

        <!-- Section Heading -->
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 text-indigo-700">
    Why Should You Choose Us?
       </h2>

        <p class="text-center text-gray-600 max-w-2xl mx-auto mb-12">
            Built for trust, security, and simplicity — everything your business needs
            to manage GST billing with confidence.
        </p>

        <!-- Carousel Wrapper -->
        <div class="relative overflow-hidden">

            <!-- Slides Container -->
            <div class="flex transition-transform duration-500"
                 :style="'transform: translateX(-' + (active * 100) + '%)'">

                <!-- Slide 1 -->
                <div class="w-full flex-shrink-0 px-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="text-4xl mb-4">🔐</div>
                        <h3 class="text-xl font-semibold mb-3">
                            Your Data is 100% Secure
                        </h3>
                        <p class="text-gray-600">
                            We use encrypted authentication and secure server
                            infrastructure to keep your invoices, passwords,
                            and client data completely safe.
                        </p>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="w-full flex-shrink-0 px-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                        <div class="text-4xl mb-4">💳</div>
                        <h3 class="text-xl font-semibold mb-3">
                            Secure Online Payments
                        </h3>
                        <p class="text-gray-600">
                            Integrated with Razorpay for safe and seamless
                            payment collection directly from invoices.
                        </p>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="w-full flex-shrink-0 px-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                        <div class="text-4xl mb-4">💰</div>
                        <h3 class="text-xl font-semibold mb-3">
                            Affordable & Feature Rich
                        </h3>
                        <p class="text-gray-600">
                            Get powerful GST billing features at a price
                            designed specifically for small businesses.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Left Button -->
            <button 
                @click="active = (active - 1 + total) % total"
                class="absolute left-0 top-1/2 -translate-y-1/2 bg-white shadow rounded-full w-10 h-10 flex items-center justify-center">
                ‹
            </button>

            <!-- Right Button -->
            <button 
                @click="active = (active + 1) % total"
                class="absolute right-0 top-1/2 -translate-y-1/2 bg-white shadow rounded-full w-10 h-10 flex items-center justify-center">
                ›
            </button>

        </div>

        <!-- Dots -->
        <div class="flex justify-center mt-6 space-x-2">
            <template x-for="i in total">
                <div 
                    @click="active = i - 1"
                    class="w-3 h-3 rounded-full cursor-pointer"
                    :class="active === i - 1 ? 'bg-indigo-600' : 'bg-gray-300'">
                </div>
            </template>
        </div>

    </div>
</section>

            {{-- Testimonials --}}
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-4">
                    Loved by small businesses
                </h2>
                <p class="text-sm text-slate-500 text-center max-w-2xl mx-auto mb-10">
                    Teams use InvoiceGo to get invoices out faster and keep cash flow predictable.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition duration-200 p-6 flex flex-col">
                        <div class="flex items-center gap-1 text-indigo-500 mb-3">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed mb-5">
                            "InvoiceGo cut our invoicing time in half and made follow-ups effortless."
                        </p>
                        <p class="text-sm font-semibold text-slate-900">Ankit Sharma</p>
                        <p class="text-xs text-slate-500">Founder, SaaS Studio</p>
                    </article>
                    <article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition duration-200 p-6 flex flex-col">
                        <div class="flex items-center gap-1 text-indigo-500 mb-3">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed mb-5">
                            "Clean, simple and reliable. Our clients pay faster because invoices just look more professional."
                        </p>
                        <p class="text-sm font-semibold text-slate-900">Priya Desai</p>
                        <p class="text-xs text-slate-500">Consultant</p>
                    </article>
                    <article class="bg-white rounded-2xl shadow-md hover:shadow-lg transition duration-200 p-6 flex flex-col">
                        <div class="flex items-center gap-1 text-indigo-500 mb-3">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1.5l2.47 4.99 5.51.8-3.99 3.89.94 5.48L10 13.9l-4.93 2.96.94-5.48-3.99-3.89 5.51-.8L10 1.5z"/></svg>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed mb-5">
                            "Exactly what we needed for recurring clients and payment tracking — no bloat, just what works."
                        </p>
                        <p class="text-sm font-semibold text-slate-900">Rahul Mehta</p>
                        <p class="text-xs text-slate-500">Agency Owner</p>
                    </article>
                </div>
            </section>

    <!-- Rating Section -->
   
    <section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-sm p-10 text-center">

            <h2 class="text-3xl font-bold tracking-tight">
                Rate Your Experience
            </h2>

            <p id="ratingText" class="mt-3 text-gray-600 text-lg">
                4.0 / 5 (1,000 ratings)
            </p>

            <!-- Stars -->
            <div class="flex justify-center mt-6 space-x-3" id="starContainer">
                <!-- Stars will be injected by JS -->
            </div>

            <!-- Thank You Message -->
            <p id="thankYouMessage"
               class="mt-6 text-green-600 font-semibold hidden transition">
                ✅ Thank you for your feedback!
            </p>

        </div>
    </div>
</section>


            {{-- Trust badges strip --}}
            <!-- TEMPORARY COMMENTED OUT CODE -->
             <!--
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="bg-indigo-600 rounded-2xl shadow-sm px-6 py-5 flex flex-col sm:flex-row gap-4 sm:gap-8 justify-between items-start sm:items-center text-sm text-white">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-semibold shrink-0">✓</div>
                        <span>Secure payments</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-semibold shrink-0">⚡</div>
                        <span>Fast invoices</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-semibold shrink-0">☁</div>
                        <span>Cloud backup</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center text-xs font-semibold shrink-0">📱</div>
                        <span>Mobile friendly</span>
                    </div>
                </div>
            </section>
-->
            <!--TEMPORARY COMMENTED OUT CODE ENDS HERE -->

            {{-- Footer --}}
            <footer class="bg-slate-900 text-slate-300 py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{-- Row 1: Brand --}}
                    <div class="mb-10">
                        <h1 class="text-3xl font-bold text-white">InvoiceGo</h1>
                        <p class="text-sm text-slate-400 mt-1">Simple invoicing for small businesses</p>
                    </div>

                    {{-- Row 2: 4 columns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] gap-16">
                        <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Features</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="#" class="hover:text-white transition">Create Invoice</a></li>
                                <li><a href="#" class="hover:text-white transition">Clients</a></li>
                                <li><a href="#" class="hover:text-white transition">Payments</a></li>
                                <li><a href="#" class="hover:text-white transition">Reports</a></li>
                                <li><a href="#" class="hover:text-white transition">Integrations</a></li>
                            </ul>
                        </div>
                        <div>
                    <h4 class="text-lg font-semibold text-white mb-3">For Businesses</h4>
                            <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white">Retail</a></li>
                            <li><a href="#" class="hover:text-white">Grocery</a></li>
                            <li><a href="#" class="hover:text-white">Pharmacy</a></li>
                            <li><a href="#" class="hover:text-white">Restaurant</a></li>
                            <li><a href="#" class="hover:text-white">Clothing/Apparel</a></li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="text-lg font-semibold text-white mb-3">Contact</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    <a href="#" class="hover:text-white transition">+91 XXXXX XXXXX</a>
                                </li>
                                <li><a href="mailto:support@invoicego.in" class="hover:text-white transition">support@invoicego.in</a></li>
                                <li>Hours: Mon–Sat, 9AM–6PM</li>
                            </ul>
                        </div>
                        <div>
  <h3 class="text-lg font-semibold text-white mb-3">Legal</h3>
  <ul class="space-y-2 text-sm">
    <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
    <li><a href="#" class="hover:text-white">Terms & Conditions</a></li>
  </ul>
</div>
                    </div>

                    {{-- Row 3: Copyright --}}
                    <div class="border-t border-slate-800 mt-10 pt-6">
                    <p class="text-center text-sm text-slate-400 mt-10">
                        © {{ date('Y') }} InvoiceGo. All rights reserved.
                    </p>
                </div>
            </footer>

        </div>

    </body>
</html>
