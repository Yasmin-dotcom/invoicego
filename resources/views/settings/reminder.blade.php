<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Reminder Settings</h1>
            <p class="mt-1 text-sm text-gray-500">
                Configure global overdue reminder behavior.
            </p>

            @if(session('success'))
                <div class="mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.reminders.save') }}" class="mt-6 space-y-5">
                @csrf

                <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Enable reminders</div>
                        <div class="text-xs text-gray-500">Send reminders only for overdue unpaid invoices.</div>
                    </div>
                    <input type="hidden" name="enable_reminders" value="0">
                    <input type="checkbox"
                           name="enable_reminders"
                           value="1"
                           @checked($settings->reminders_enabled)
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </label>

                <div>
                    <label for="start_after_days" class="block text-sm font-medium text-gray-700">Start after overdue days</label>
                    <input id="start_after_days"
                           name="start_after_days"
                           type="number"
                           min="0"
                           value="{{ old('start_after_days', $settings->start_after_days ?? 0) }}"
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="repeat_every_days" class="block text-sm font-medium text-gray-700">Repeat every X days</label>
                    <input id="repeat_every_days"
                           name="repeat_every_days"
                           type="number"
                           min="1"
                           value="{{ old('repeat_every_days', $settings->repeat_every_days ?? 3) }}"
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="max_reminders" class="block text-sm font-medium text-gray-700">Max reminders</label>
                    <input id="max_reminders"
                           name="max_reminders"
                           type="number"
                           min="1"
                           value="{{ old('max_reminders', $settings->max_reminders ?? 5) }}"
                           class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Email enabled</div>
                        <div class="text-xs text-gray-500">Send reminders over email.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="email_enabled" value="0">
                        <input type="checkbox"
                               name="email_enabled"
                               value="1"
                               @checked(old('email_enabled', $settings->email_enabled))
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </div>
                </label>

                <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">WhatsApp enabled</div>
                        <div class="text-xs text-gray-500">Enable WhatsApp reminder channel.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="whatsapp_enabled" value="0">
                        <input type="checkbox"
                               name="whatsapp_enabled"
                               value="1"
                               @checked(old('whatsapp_enabled', $settings->whatsapp_enabled))
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </div>
                </label>

                <label class="flex items-center justify-between p-4 rounded-lg border border-gray-200">
                    <div>
                        <div class="text-sm font-medium text-gray-900">SMS enabled</div>
                        <div class="text-xs text-gray-500">Enable SMS reminder channel.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="sms_enabled" value="0">
                        <input type="checkbox"
                               name="sms_enabled"
                               value="1"
                               @checked(old('sms_enabled', $settings->sms_enabled))
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </div>
                </label>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
