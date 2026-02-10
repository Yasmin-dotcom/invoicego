<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Business Info') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="text-lg font-semibold text-gray-900">
                    Tell us about your business
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    This helps personalize your invoices and dashboard.
                </div>

                <form method="POST" action="{{ route('onboarding.business') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Business Name</label>
                        <input
                            type="text"
                            name="business_name"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Acme Inc."
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Logo (optional)</label>
                        <input
                            type="file"
                            name="logo"
                            accept="image/*"
                            class="mt-1 w-full text-sm text-gray-700"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select
                            name="currency"
                            required
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="INR" selected>INR (₹)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
                    >
                        Continue
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
