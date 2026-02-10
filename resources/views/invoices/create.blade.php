<x-app-layout>
    <div class="p-6 max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Create Invoice</h1>
        </div>

        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded border border-green-400 bg-green-100 text-green-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 px-4 py-3 rounded border border-red-400 bg-red-100 text-red-800 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded shadow">
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Client *</label>
                    <select name="client_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Select Client</option>
                        @if($clients->isEmpty())
                            <option value="" disabled>No clients found</option>
                        @else
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('client_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if($clients->isEmpty())
                        <p class="text-sm text-gray-600 mt-2">
                            No clients found.
                            <a href="{{ route('clients.create') }}" class="text-red-600 hover:text-red-700 font-semibold">
                                Add a client
                            </a>
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Due date (optional)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700">
                        Create Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
