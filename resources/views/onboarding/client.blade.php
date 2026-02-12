<x-app-layout>

<div class="py-12 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Step Heading --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    Add your first client
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    You can skip this and add clients later.
                </p>
                <form method="POST" action="{{ route('onboarding.skip') }}" class="mt-2 inline">
                    @csrf
                    <button type="submit"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none">
                        Skip for now →
                    </button>
                </form>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('onboarding.client.store') }}" class="space-y-4">
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
