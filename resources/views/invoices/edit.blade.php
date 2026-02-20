<x-app-layout>
    <div class="p-6 max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Edit Invoice</h1>

            <a href="{{ route('invoices.show', $invoice) }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                ← Back
            </a>
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

            {{-- IMPORTANT: PUT + update route --}}
            <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Client --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Client *</label>

                    <select name="client_id" required class="w-full border rounded px-3 py-2">
                        <option value="">Select Client</option>

                        @foreach($clients as $client)
                            <option value="{{ $client->id }}"
                                @selected(old('client_id', $invoice->client_id) == $client->id)>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('client_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Due date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Due date (optional)</label>

                    <input
                        type="date"
                        name="due_date"
                        value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
                        class="w-full border rounded px-3 py-2"
                    >

                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Invoice Items --}}
                <div>
                    <label class="block text-sm font-medium mb-2">Items *</label>
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
                            @foreach($invoice->items as $index => $item)
                            <tr class="border-t">
                                <td class="p-2">
                                    <input name="items[{{ $index }}][description]" type="text" required
                                           value="{{ old('items.'.$index.'.description', $item->description) }}"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][quantity]" type="number" step="1" min="1" required
                                           value="{{ old('items.'.$index.'.quantity', $item->quantity) }}"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][price]" type="number" step="0.01" min="0" required
                                           value="{{ old('items.'.$index.'.price', $item->price) }}"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                                <td class="p-2">
                                    <input name="items[{{ $index }}][gst_rate]" type="number" step="0.01" min="0" max="100"
                                           value="{{ old('items.'.$index.'.gst_rate', $item->gst_rate ?? 0) }}"
                                           placeholder="0"
                                           class="w-full border rounded px-2 py-1 text-sm">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <div class="w-full max-w-sm rounded border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <input id="subtotalPreview" type="text" readonly
                                   value="0.00"
                                   class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm text-gray-600">
                            <span>Tax Amount</span>
                            <input id="gstAmountPreview" type="text" readonly
                                   value="0.00"
                                   class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm font-semibold text-gray-900">
                            <span>Grand Total</span>
                            <input id="grandTotalPreview" type="text" readonly
                                   value="0.00"
                                   class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="px-5 py-2 rounded border">
                        Cancel
                    </a>

                    <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700">
                        Update Invoice
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        (function () {
            const subtotalEl = document.getElementById('subtotalPreview');
            const gstAmountEl = document.getElementById('gstAmountPreview');
            const grandTotalEl = document.getElementById('grandTotalPreview');

            function parseNumber(value) {
                const num = parseFloat(String(value).replace(/[^0-9.]/g, ''));
                return Number.isFinite(num) ? num : 0;
            }

            function calculateTotals() {
                const qtyInputs = Array.from(document.querySelectorAll('[name^="items"][name$="[quantity]"]'));
                const priceInputs = Array.from(document.querySelectorAll('[name^="items"][name$="[price]"]'));
                const gstInputs = Array.from(document.querySelectorAll('[name^="items"][name$="[gst_rate]"]'));
                let subtotal = 0;
                let gstAmount = 0;

                qtyInputs.forEach((qtyInput, index) => {
                    const qty = parseNumber(qtyInput.value);
                    const price = parseNumber(priceInputs[index]?.value);
                    const gstRate = parseNumber(gstInputs[index]?.value ?? 0);
                    const itemTotal = qty * price;
                    subtotal += itemTotal;
                    gstAmount += itemTotal * (gstRate / 100);
                });

                const grandTotal = subtotal + gstAmount;

                if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
                if (gstAmountEl) gstAmountEl.value = gstAmount.toFixed(2);
                if (grandTotalEl) grandTotalEl.value = grandTotal.toFixed(2);
            }

            document.addEventListener('input', (event) => {
                if (event.target.matches('[name^="items"][name$="[quantity]"], [name^="items"][name$="[price]"], [name^="items"][name$="[gst_rate]"]')) {
                    calculateTotals();
                }
            });

            calculateTotals();
        })();
    </script>
</x-app-layout>
