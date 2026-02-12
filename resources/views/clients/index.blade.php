<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-3">
            <h1 class="text-2xl font-bold">Clients</h1>

            <a href="{{ route('clients.create') }}"
               class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                Add Client
            </a>
        </div>


        {{-- Search --}}
        <div class="mb-6">
            <input
                id="clientsSearch"
                type="text"
                placeholder="Search clients..."
                class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
            />
        </div>


        {{-- Success message --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif



        {{-- Empty state --}}
        @if($clients->isEmpty())
            <div class="bg-white p-12 rounded shadow text-center">
                <p class="text-gray-500 text-lg">No clients yet</p>

                <a href="{{ route('clients.create') }}"
                   class="inline-block mt-4 bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition-colors">
                    Add Your First Client
                </a>
            </div>
        @else

            {{-- Table --}}
            <div class="bg-white rounded shadow overflow-hidden">
                <table id="clientsTable" class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reminders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">

                    @foreach($clients as $client)
                        <tr class="hover:bg-gray-50">

                            {{-- Name --}}
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $client->name }}
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $client->email ?? '—' }}
                            </td>

                            {{-- Phone --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $client->phone ?? '—' }}
                            </td>

                            {{-- Address --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $client->address ?? '—' }}
                            </td>

                            {{-- Reminders --}}
                            <td class="px-6 py-4">
                                @if($client->reminder_enabled)
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Enabled</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Disabled</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 flex items-center space-x-3">

                                {{-- Edit --}}
                                <a href="{{ route('clients.edit', $client->id) }}"
                                   class="text-blue-600 hover:text-blue-800 text-lg">
                                    ✏️
                                </a>

                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route('clients.destroy', $client->id) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this client?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 text-lg">
                                        🗑️
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>


            {{-- ✅ Pagination (correct place) --}}
            <div class="mt-6 flex justify-center">
                {{ $clients->links() }}
            </div>

        @endif



        {{-- Live Search Script --}}
        <script>
            (function () {
                const searchInput = document.getElementById('clientsSearch');
                const table = document.getElementById('clientsTable');

                if (!searchInput || !table) return;

                const rows = Array.from(table.querySelectorAll('tbody tr'));

                searchInput.addEventListener('keyup', () => {
                    const term = searchInput.value.toLowerCase().trim();

                    rows.forEach((row) => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            })();
        </script>

    </div>
</x-app-layout>
