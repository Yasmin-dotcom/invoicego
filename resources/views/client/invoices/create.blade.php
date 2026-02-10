<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Invoice') }}
            </h2>
            <a href="{{ route('client.invoices.index') }}"
               class="text-sm font-semibold text-gray-700 hover:text-gray-900">
                Back to Invoices
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(!$user->isPro())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-yellow-800">
                    <div class="font-semibold">Free Plan: You can create limited invoices. Upgrade to Pro.</div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('client.invoices.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                id="amount"
                                name="amount"
                                value="{{ old('amount') }}"
                                required
                                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            @if(!$user->isPro())
                                <p class="text-xs text-gray-600">
                                    Free plan limit: <span class="font-semibold">3 invoices total</span>.
                                </p>
                            @endif
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                            >
                                Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

