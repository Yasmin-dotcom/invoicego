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
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-4" id="createInvoiceForm">
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
                    <label class="block text-sm font-medium mb-1">Invoice date (optional)</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                           class="w-full border rounded px-3 py-2">
                    @error('invoice_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Due date (optional)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border rounded px-3 py-2">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Invoice Items (optional - add at least one for create-with-items) --}}
                <div>
                    <label class="block text-sm font-medium mb-2">Items (optional)</label>
                    <table class="w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 text-left">Description</th>
                                <th class="p-2 w-20">Qty</th>
                                <th class="p-2 w-24">Price</th>
                                <th class="p-2 w-20">GST %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $createItems = old('items', [['description' => '', 'quantity' => 1, 'price' => 0, 'gst_rate' => 0]]); @endphp
                            @foreach($createItems as $index => $createItem)
                            <tr class="border-t">
                                <td class="p-2">
                                    <input name="items[{{ $index }}][description]" type="text"
                                           value="{{ old('items.'.$index.'.description', $createItem['description'] ?? '') }}"
                                           placeholder="Item name"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][quantity]" type="number" step="1" min="1"
                                           value="{{ old('items.'.$index.'.quantity', $createItem['quantity'] ?? 1) }}"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][price]" type="number" step="0.01" min="0"
                                           value="{{ old('items.'.$index.'.price', $createItem['price'] ?? 0) }}"
                                           placeholder="0"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][gst_rate]" type="number" step="0.01" min="0" max="100"
                                           value="{{ old('items.'.$index.'.gst_rate', $createItem['gst_rate'] ?? 0) }}"
                                           placeholder="0"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-xs text-gray-500 mt-1">Leave items empty to create a draft invoice. Add at least one item to create a full invoice.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="submit"
                            name="submit_action"
                            value="save"
                            class="bg-gray-100 text-gray-700 px-5 py-2 rounded hover:bg-gray-200">
                        Save
                    </button>
                    <button type="submit"
                            name="submit_action"
                            value="save_send"
                            class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700">
                        Save &amp; Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('createInvoiceForm')?.addEventListener('submit', function(e) {
            const submitAction = e.submitter?.value || 'save';
            if (submitAction === 'save') {
                this.querySelectorAll('[name^="items"]').forEach(el => el.removeAttribute('name'));
                return;
            }

            const descInputs = this.querySelectorAll('[name^="items"][name$="[description]"]');
            const hasFilledItem = Array.from(descInputs).some(el => (el.value || '').trim() !== '');
            if (!hasFilledItem) {
                this.querySelectorAll('[name^="items"]').forEach(el => el.removeAttribute('name'));
            }
        });
    </script>
</x-app-layout>
