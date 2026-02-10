<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Upgrade') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="text-lg font-semibold">
                        Upgrade successful
                    </div>
                    <div class="text-sm text-gray-600">
                        Your Pro plan is now active.
                    </div>

                    <div>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            Continue to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

