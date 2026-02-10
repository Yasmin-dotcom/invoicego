<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Branding') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-2">
                    <div class="font-semibold">Branding Settings (Pro)</div>
                    <div class="text-sm text-gray-600">
                        This section is Pro-only in V1 (example route protection).
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

