@extends('layouts.dashboard')

@section('title', 'Client Reminder Settings')

@section('content')
<div
    x-data="{
        adminEnabled: {{ $effective['admin_reminders_enabled'] ? 'true' : 'false' }},

        // Tri-state via "use default" + toggle
        useDefaultReminders: {{ $override?->reminders_enabled === null ? 'true' : 'false' }},
        remindersEnabled: {{ $effective['reminders_enabled'] ? 'true' : 'false' }},

        useDefaultEmail: {{ $override?->email_enabled === null ? 'true' : 'false' }},
        emailEnabled: {{ $effective['email_enabled'] ? 'true' : 'false' }},

        useDefaultWhatsapp: {{ $override?->whatsapp_enabled === null ? 'true' : 'false' }},
        whatsappEnabled: {{ $effective['whatsapp_enabled'] ? 'true' : 'false' }},

        useDefaultSms: {{ $override?->sms_enabled === null ? 'true' : 'false' }},
        smsEnabled: {{ $effective['sms_enabled'] ? 'true' : 'false' }},
    }"
>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Client Reminder Settings</h1>
        <p class="text-sm text-gray-600 mt-1">
            Override admin defaults for your account. Leave fields blank (or choose “use admin default”) to inherit global settings.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if(!$effective['admin_reminders_enabled'])
        <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-800">
            <div class="text-sm font-semibold">Reminders are disabled by admin</div>
            <div class="text-sm mt-1">You can review your preferences, but you can’t enable reminders while the global switch is OFF.</div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Email Payment Reminders</h2>
            <p class="text-sm text-gray-600 mt-1">Manage reminder schedule and channels for your account.</p>
        </div>

        <form method="POST" action="{{ route('client.settings.reminders.update') }}" class="p-6 space-y-8">
            @csrf

            <!-- Enable reminders override -->
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-start justify-between gap-6">
                    <div>
                        <label class="text-sm font-semibold text-gray-900">Enable reminders for this client</label>
                        <p class="text-sm text-gray-600 mt-1">Leave blank to use admin default.</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Effective: <span class="font-semibold">{{ $effective['reminders_enabled'] ? 'Enabled' : 'Disabled' }}</span>
                            @if($override?->reminders_enabled === null)
                                <span class="ml-2 rounded bg-gray-100 px-2 py-0.5 text-gray-700">Admin default</span>
                            @else
                                <span class="ml-2 rounded bg-red-50 px-2 py-0.5 text-red-700">Client override</span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                   x-model="useDefaultReminders">
                            Use admin default
                        </label>

                        <button
                            type="button"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            :class="(!adminEnabled || useDefaultReminders) ? 'bg-gray-300 cursor-not-allowed' : (remindersEnabled ? 'bg-red-500' : 'bg-gray-300')"
                            :disabled="!adminEnabled || useDefaultReminders"
                            @click="if(adminEnabled && !useDefaultReminders) remindersEnabled = !remindersEnabled"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                  :class="(!adminEnabled || useDefaultReminders) ? 'translate-x-1' : (remindersEnabled ? 'translate-x-6' : 'translate-x-1')"></span>
                        </button>
                    </div>
                </div>

                <!-- hidden nullable boolean -->
                <input type="hidden" name="reminders_enabled" :value="useDefaultReminders ? '' : (remindersEnabled ? 1 : 0)">
            </div>

            <!-- Reminder days override -->
            <div>
                <label for="reminder_days" class="block text-sm font-semibold text-gray-900">Reminder days</label>
                <p class="text-sm text-gray-600 mt-1">Leave blank to use admin default. Example: <span class="font-mono">3,0,-2</span></p>
                <p class="text-xs text-gray-500 mt-2">
                    Effective: <span class="font-mono">{{ implode(',', $effective['reminder_days'] ?? []) }}</span>
                    @if(($override?->reminder_days) === null)
                        <span class="ml-2 rounded bg-gray-100 px-2 py-0.5 text-gray-700">Admin default</span>
                    @else
                        <span class="ml-2 rounded bg-red-50 px-2 py-0.5 text-red-700">Client override</span>
                    @endif
                </p>

                <div class="mt-3">
                    <input
                        id="reminder_days"
                        name="reminder_days"
                        type="text"
                        value="{{ old('reminder_days', $override?->reminder_days ? implode(',', $override->reminder_days) : '') }}"
                        placeholder="3,0,-2"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                </div>

                @error('reminder_days')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Channel toggles (override) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Email -->
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Email reminders</div>
                            <p class="text-sm text-gray-600 mt-1">Leave blank to use admin default.</p>
                            <p class="text-xs text-gray-500 mt-2">
                                Effective: <span class="font-semibold">{{ $effective['email_enabled'] ? 'Enabled' : 'Disabled' }}</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" class="rounded border-gray-300 text-red-500 focus:ring-red-500"
                                       x-model="useDefaultEmail">
                                Default
                            </label>
                            <button type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    :class="useDefaultEmail ? 'bg-gray-300 cursor-not-allowed' : (emailEnabled ? 'bg-red-500' : 'bg-gray-300')"
                                    :disabled="useDefaultEmail"
                                    @click="if(!useDefaultEmail) emailEnabled = !emailEnabled">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                      :class="useDefaultEmail ? 'translate-x-1' : (emailEnabled ? 'translate-x-6' : 'translate-x-1')"></span>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="email_enabled" :value="useDefaultEmail ? '' : (emailEnabled ? 1 : 0)">
                </div>

                <!-- WhatsApp (future-ready) -->
                <div class="rounded-lg border border-gray-200 p-4 opacity-60">
                    <div class="text-sm font-semibold text-gray-900">WhatsApp reminders</div>
                    <p class="text-sm text-gray-600 mt-1">Coming soon.</p>
                    <input type="hidden" name="whatsapp_enabled" value="">
                </div>

                <!-- SMS (future-ready) -->
                <div class="rounded-lg border border-gray-200 p-4 opacity-60">
                    <div class="text-sm font-semibold text-gray-900">SMS reminders</div>
                    <p class="text-sm text-gray-600 mt-1">Coming soon.</p>
                    <input type="hidden" name="sms_enabled" value="">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-red-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

@if(!isset($alpineIncluded))
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif
@endsection
