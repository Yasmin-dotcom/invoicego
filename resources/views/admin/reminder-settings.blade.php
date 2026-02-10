@extends('layouts.dashboard')

@section('title', 'Reminder Settings')

@section('content')
<div
    x-data="{
        remindersEnabled: {{ $settings->reminders_enabled ? 'true' : 'false' }},
        emailEnabled: {{ $settings->email_enabled ? 'true' : 'false' }},
        whatsappEnabled: {{ $settings->whatsapp_enabled ? 'true' : 'false' }},
        smsEnabled: {{ $settings->sms_enabled ? 'true' : 'false' }},
        daysInput: '{{ implode(',', $settings->default_reminder_days ?? [3,0,-2]) }}',
        toast: {{ session('success') ? 'true' : 'false' }},
        toastMsg: '{{ session('success') ?? '' }}',
    }"
    x-init="if (toast) setTimeout(() => toast = false, 2500)"
>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reminder Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Global reminder rules for the entire SaaS.</p>
        </div>
    </div>

    <!-- Toast -->
    <div
        x-show="toast"
        x-transition
        class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-800"
    >
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium" x-text="toastMsg"></span>
            <button type="button" class="text-green-800/70 hover:text-green-900" @click="toast = false">✕</button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Global Controls</h2>
            <p class="text-sm text-gray-600 mt-1">Enable/disable reminders and configure default schedule + channels.</p>
        </div>

        <form method="POST" action="{{ route('admin.reminder-settings.update') }}" class="p-6 space-y-8">
            @csrf

            <!-- Global toggle -->
            <div class="flex items-start justify-between gap-6">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Enable reminders (global)</div>
                    <div class="text-sm text-gray-600 mt-1">If disabled, no reminders will be sent system-wide.</div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-medium text-gray-500" x-text="remindersEnabled ? 'ON' : 'OFF'"></span>
                    <button
                        type="button"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        :class="remindersEnabled ? 'bg-red-500' : 'bg-gray-300'"
                        @click="remindersEnabled = !remindersEnabled"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            :class="remindersEnabled ? 'translate-x-6' : 'translate-x-1'"
                        ></span>
                    </button>
                </div>
                <input type="hidden" name="reminders_enabled" :value="remindersEnabled ? 1 : 0">
            </div>

            <!-- Default reminder days -->
            <div>
                <label for="default_reminder_days" class="block text-sm font-semibold text-gray-900">
                    Default reminder days
                </label>
                <p class="text-sm text-gray-600 mt-1">Example: <span class="font-mono">3,0,-2</span> (before, on, after due date)</p>

                <div class="mt-3">
                    <input
                        id="default_reminder_days"
                        name="default_reminder_days"
                        type="text"
                        x-model="daysInput"
                        placeholder="3,0,-2"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                </div>

                @error('default_reminder_days')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Channels -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Email -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Email</div>
                            <div class="text-xs text-gray-600 mt-1">Enabled now</div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            :class="emailEnabled ? 'bg-red-500' : 'bg-gray-300'"
                            @click="emailEnabled = !emailEnabled"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                  :class="emailEnabled ? 'translate-x-6' : 'translate-x-1'"></span>
                        </button>
                    </div>
                    <input type="hidden" name="email_enabled" :value="emailEnabled ? 1 : 0">
                </div>

                <!-- WhatsApp (future-ready) -->
                <div class="rounded-lg border border-gray-200 p-4 opacity-60">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">WhatsApp</div>
                            <div class="text-xs text-gray-600 mt-1">Coming soon</div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 cursor-not-allowed"
                            disabled
                        >
                            <span class="inline-block h-4 w-4 transform translate-x-1 rounded-full bg-white"></span>
                        </button>
                    </div>
                    <input type="hidden" name="whatsapp_enabled" value="0">
                </div>

                <!-- SMS (future-ready) -->
                <div class="rounded-lg border border-gray-200 p-4 opacity-60">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">SMS</div>
                            <div class="text-xs text-gray-600 mt-1">Coming soon</div>
                        </div>
                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 cursor-not-allowed"
                            disabled
                        >
                            <span class="inline-block h-4 w-4 transform translate-x-1 rounded-full bg-white"></span>
                        </button>
                    </div>
                    <input type="hidden" name="sms_enabled" value="0">
                </div>
            </div>

            @error('reminders_enabled')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('email_enabled')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('whatsapp_enabled')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('sms_enabled')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-red-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    Save settings
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js (only if not already included globally) -->
@if(!isset($alpineIncluded))
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif
@endsection
