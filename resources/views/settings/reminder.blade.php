<x-app-layout>
    <div class="max-w-3xl mx-auto p-6 space-y-6">
        {{-- Business Profile (read-only) --}}
        @php
            /** @var \App\Models\User|null $user */
            $user = auth()->user();
        @endphp

        @if($user)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900">Business Profile</h2>
                <p class="mt-1 text-sm text-gray-500">
                    These details come from your onboarding business setup.
                </p>

                <dl class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Business Name</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->business_name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">GSTIN</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->gstin ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">GST State Code</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->state_code ?? '—' }}</dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-gray-500">Business Address</dt>
                        <dd class="mt-0.5 text-gray-900 whitespace-pre-line">
                            @php
                                $addressParts = array_filter([
                                    $user->business_address ?? null,
                                    trim(($user->business_city ?? '') . (isset($user->business_state) && $user->business_state ? ', ' . $user->business_state : '')),
                                    $user->business_pincode ?? null,
                                ]);
                            @endphp
                            {{ !empty($addressParts) ? implode(\"\\n\", $addressParts) : '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Bank Name</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->bank_name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Bank Branch</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->bank_branch ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Account Name</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->bank_account_name ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Account Number</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->bank_account_number ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">IFSC</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->bank_ifsc ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-gray-500">Invoice Prefix</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $user->invoice_prefix ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        @endif

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

                @php
                    $appSettings = \App\Models\Setting::getSettings();
                    $defaultTemplate = old('default_template', $appSettings->default_template ?? 'classic');
                @endphp

                <div class="pt-4 border-t border-gray-100 mt-4">
                    <h2 class="text-sm font-semibold text-gray-900 mb-2">
                        Default Invoice Template
                    </h2>
                    <p class="text-xs text-gray-500 mb-2">
                        New invoices will use this template by default. You can still change it per invoice.
                    </p>
                    <select name="default_template"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="classic" @selected($defaultTemplate === 'classic')>Classic</option>
                        <option value="minimal" @selected($defaultTemplate === 'minimal')>Minimal</option>
                        <option value="modern" @selected($defaultTemplate === 'modern')>Modern (Pro)</option>
                        <option value="gst" @selected($defaultTemplate === 'gst')>GST Focused (Pro)</option>
                        <option value="premium" @selected($defaultTemplate === 'premium')>Premium (Pro)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Free plan → Classic &amp; Minimal only. Pro plan unlocks all templates.
                    </p>
                </div>

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
