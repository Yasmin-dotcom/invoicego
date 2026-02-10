<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('First Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="text-lg font-semibold text-gray-900">
                    Add your first client
                </div>
                <div class="text-sm text-gray-600 mt-1">
                    You can skip this and add clients later.
                </div>

                <form method="POST" action="{{ route('onboarding.client.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Name</label>
                        <input
                            type="text"
                            name="client_name"
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Jane Doe"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Email</label>
                        <input
                            type="email"
                            name="client_email"
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="client@example.com"
                        />
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
                    >
                        Finish Setup
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
