<x-app-layout>

<div class="py-2 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Step Indicator --}}
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm text-indigo-500 mb-2">

                </div>
            
            </div>

            {{-- Card --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-3xl shadow-2xl p-10">

                <div class="text-2xl font-semibold text-gray-900">
                    Tell us about your business
                </div>

                <div class="text-sm text-gray-500 mt-2">
                    This helps personalize your invoices, branding and dashboard experience.
                </div>

                <form method="POST"
                      action="{{ route('onboarding.business') }}"
                      enctype="multipart/form-data"
                      class="mt-10 space-y-6">
                    @csrf

                    {{-- Business Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Business Name
                        </label>
                        <input
                            type="text"
                            name="business_name"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="Acme Inc."
                        />
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Logo (optional)
                        </label>
                        <input
                            type="file"
                            name="logo"
                            accept="image/*"
                            class="mt-2 w-full text-sm text-gray-700"
                        />
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Currency
                        </label>
                        <select
                            name="currency"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                        >
                            <option value="INR" selected>INR (₹)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                    </div>

                    {{-- GSTIN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            GSTIN (optional)
                        </label>
                        <input
                            type="text"
                            name="gstin"
                            id="onboarding_gstin"
                            value="{{ old('gstin', auth()->user()->gstin ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="22AAAAA0000A1Z5"
                        />
                    </div>

                    {{-- State Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            State Code (optional)
                        </label>
                        <input
                            type="text"
                            name="state_code"
                            id="onboarding_state_code"
                            inputmode="numeric"
                            maxlength="2"
                            value="{{ old('state_code', auth()->user()->state_code ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="22"
                        />
                        <p class="mt-1 text-xs text-gray-500">State code auto-filled from GSTIN.</p>
                        <script>
                        (function () {
                            var gstinEl = document.getElementById('onboarding_gstin');
                            var stateCodeEl = document.getElementById('onboarding_state_code');
                            if (!gstinEl || !stateCodeEl) return;
                            var stateCodeUserEdited = false;

                            stateCodeEl.addEventListener('input', function () {
                                stateCodeUserEdited = true;
                                this.value = this.value.replace(/\D/g, '');
                            });
                            stateCodeEl.addEventListener('paste', function () { stateCodeUserEdited = true; });

                            gstinEl.addEventListener('input', function () {
                                var gstin = (this.value || '').trim();
                                if (gstin.length >= 2) {
                                    var firstTwo = gstin.slice(0, 2);
                                    if (/^\d{2}$/.test(firstTwo) && !stateCodeUserEdited) {
                                        stateCodeEl.value = firstTwo;
                                    }
                                } else if (gstin.length < 2 && !stateCodeUserEdited) {
                                    stateCodeEl.value = '';
                                }
                            });
                            gstinEl.addEventListener('paste', function () {
                                setTimeout(function () {
                                    var gstin = (gstinEl.value || '').trim();
                                    if (gstin.length >= 2) {
                                        var firstTwo = gstin.slice(0, 2);
                                        if (/^\d{2}$/.test(firstTwo) && !stateCodeUserEdited) {
                                            stateCodeEl.value = firstTwo;
                                        }
                                    } else if (gstin.length < 2 && !stateCodeUserEdited) {
                                        stateCodeEl.value = '';
                                    }
                                }, 0);
                            });
                        })();
                        </script>
                    </div>

                    {{-- Business Address (optional) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Business Address (optional)
                        </label>
                        <textarea
                            name="business_address"
                            rows="3"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="Street, area, landmark"
                        >{{ old('business_address', auth()->user()->business_address ?? '') }}</textarea>
                    </div>

                    {{-- Business City / State / Pincode (optional) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                City (optional)
                            </label>
                            <input
                                type="text"
                                name="business_city"
                                value="{{ old('business_city', auth()->user()->business_city ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="Bengaluru"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                State (optional)
                            </label>
                            <input
                                type="text"
                                name="business_state"
                                value="{{ old('business_state', auth()->user()->business_state ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="Karnataka"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Pincode (optional)
                            </label>
                            <input
                                type="text"
                                name="business_pincode"
                                value="{{ old('business_pincode', auth()->user()->business_pincode ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="560001"
                            />
                        </div>
                    </div>

                    {{-- Bank Details (optional) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Bank Name (optional)
                            </label>
                            <input
                                type="text"
                                name="bank_name"
                                value="{{ old('bank_name', auth()->user()->bank_name ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="HDFC Bank"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Bank Branch (optional)
                            </label>
                            <input
                                type="text"
                                name="bank_branch"
                                value="{{ old('bank_branch', auth()->user()->bank_branch ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="MG Road Branch"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Account Name (optional)
                            </label>
                            <input
                                type="text"
                                name="bank_account_name"
                                value="{{ old('bank_account_name', auth()->user()->bank_account_name ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="Acme Technologies Pvt Ltd"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Account Number (optional)
                            </label>
                            <input
                                type="text"
                                name="bank_account_number"
                                value="{{ old('bank_account_number', auth()->user()->bank_account_number ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="XXXXXXXXXXXX"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                IFSC (optional)
                            </label>
                            <input
                                type="text"
                                name="bank_ifsc"
                                value="{{ old('bank_ifsc', auth()->user()->bank_ifsc ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="HDFC0000123"
                            />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Invoice Prefix (optional)
                            </label>
                            <input
                                type="text"
                                name="invoice_prefix"
                                value="{{ old('invoice_prefix', auth()->user()->invoice_prefix ?? '') }}"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                                placeholder="INV-"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Example: "INV-" → invoice numbers like INV-001, INV-002.
                            </p>
                        </div>
                    </div>

                    {{-- Continue --}}
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 hover:shadow-xl transition-all"
                        >
                            Continue →
                        </button>
                    </div>

                </form>
            </div>

        </div>
        
    </div>

</x-app-layout>
