<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-2">
                    <div><span class="font-semibold">Email:</span> {{ auth()->user()->email }}</div>
                    <div><span class="font-semibold">Role:</span> {{ auth()->user()->role === 'client' ? 'owner' : auth()->user()->role }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

