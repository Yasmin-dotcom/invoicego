<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h1 class="text-xl font-semibold text-gray-900">Business Information</h1>
            <p class="mt-1 text-sm text-gray-500">
                Update the business details that appear on your invoices.
            </p>

            @if(session('success'))
                <div class="mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.business.update') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Name</label>
                    <input
                        type="text"
                        name="business_name"
                        value="{{ old('business_name', $user->business_name ?? '') }}"
                        class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                        placeholder="Acme Inc."
                    />
                    @error('business_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">GSTIN</label>
                        <input
                            type="text"
                            name="gstin"
                            value="{{ old('gstin', $user->gstin ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="22AAAAA0000A1Z5"
                        />
                        @error('gstin')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">GST State Code</label>
                        <input
                            type="text"
                            name="state_code"
                            value="{{ old('state_code', $user->state_code ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="22"
                        />
                        @error('state_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Address</label>
                    <textarea
                        name="business_address"
                        rows="3"
                        class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                        placeholder="Street, area, landmark"
                    >{{ old('business_address', $user->business_address ?? '') }}</textarea>
                    @error('business_address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City</label>
                        <input
                            type="text"
                            name="business_city"
                            value="{{ old('business_city', $user->business_city ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="Bengaluru"
                        />
                        @error('business_city')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">State</label>
                        <input
                            type="text"
                            name="business_state"
                            value="{{ old('business_state', $user->business_state ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="Karnataka"
                        />
                        @error('business_state')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pincode</label>
                        <input
                            type="text"
                            name="business_pincode"
                            value="{{ old('business_pincode', $user->business_pincode ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="560001"
                        />
                        @error('business_pincode')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <input
                            type="text"
                            name="bank_name"
                            value="{{ old('bank_name', $user->bank_name ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="HDFC Bank"
                        />
                        @error('bank_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bank Branch</label>
                        <input
                            type="text"
                            name="bank_branch"
                            value="{{ old('bank_branch', $user->bank_branch ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="MG Road Branch"
                        />
                        @error('bank_branch')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Account Name</label>
                        <input
                            type="text"
                            name="bank_account_name"
                            value="{{ old('bank_account_name', $user->bank_account_name ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="Acme Technologies Pvt Ltd"
                        />
                        @error('bank_account_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input
                            type="text"
                            name="bank_account_number"
                            value="{{ old('bank_account_number', $user->bank_account_number ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="XXXXXXXXXXXX"
                        />
                        @error('bank_account_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IFSC</label>
                        <input
                            type="text"
                            name="bank_ifsc"
                            value="{{ old('bank_ifsc', $user->bank_ifsc ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="HDFC0000123"
                        />
                        @error('bank_ifsc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Invoice Prefix</label>
                        <input
                            type="text"
                            name="invoice_prefix"
                            value="{{ old('invoice_prefix', $user->invoice_prefix ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition"
                            placeholder="INV-"
                        />
                        @error('invoice_prefix')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Example: "INV-" → invoice numbers like INV-001, INV-002.
                        </p>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700 hover:shadow-md transition-all">
                        Save Business Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

