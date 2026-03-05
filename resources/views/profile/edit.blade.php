<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Business Information (read-only) --}}
            @php
                /** @var \App\Models\User|null $user */
                $user = auth()->user();
            @endphp

            @if($user)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-3xl">
                        <h2 class="text-lg font-semibold text-gray-900">Business Information</h2>
                        <p class="mt-1 text-sm text-gray-500">
                            These details come from your business onboarding and are used on your invoices.
                        </p>

                        <dl class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Business Name</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->business_name ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">GSTIN</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->gstin ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">GST State Code</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->state_code ?? '-' }}</dd>
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
                                    {{ !empty($addressParts) ? implode("\n", $addressParts) : '-' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">Bank Name</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->bank_name ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">Bank Branch</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->bank_branch ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">Account Name</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->bank_account_name ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">Account Number</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->bank_account_number ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">IFSC</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->bank_ifsc ?? '-' }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500">Invoice Prefix</dt>
                                <dd class="mt-0.5 text-gray-900">{{ $user->invoice_prefix ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
