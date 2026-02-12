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
