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

                <div class="flex justify-end">
                    <div class="w-full max-w-sm rounded border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <input id="subtotalPreview" type="text" readonly
                                   value="0.00"
                                   class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
                        </div>
                        <div class="mt-3 flex items-center justify-between text-sm text-gray-600">
                            <label for="gstPercent" class="mr-3">GST %</label>
                            <input id="gstPercent" name="gst_percent" type="number" step="0.01" min="0"
                                   value="0"
                                   class="w-24 text-right border border-gray-300 rounded px-2 py-1" />
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
            const gstInput = document.getElementById('gstPercent');
            const subtotalEl = document.getElementById('subtotalPreview');
            const gstAmountEl = document.getElementById('gstAmountPreview');
            const grandTotalEl = document.getElementById('grandTotalPreview');

            function parseNumber(value) {
                const num = parseFloat(String(value).replace(/[^0-9.]/g, ''));
                return Number.isFinite(num) ? num : 0;
            }

            function calculateSubtotal() {
                const qtyInputs = Array.from(document.querySelectorAll('[name^="items"][name$="[quantity]"]'));
                const priceInputs = Array.from(document.querySelectorAll('[name^="items"][name$="[price]"]'));
                let subtotal = 0;

                qtyInputs.forEach((qtyInput, index) => {
                    const qty = parseNumber(qtyInput.value);
                    const price = parseNumber(priceInputs[index]?.value);
                    subtotal += qty * price;
                });

                return subtotal;
            }

            function updatePreview() {
                const subtotal = calculateSubtotal();
                const gstPercent = parseNumber(gstInput?.value);
                const gstAmount = subtotal * (gstPercent / 100);
                const grandTotal = subtotal + gstAmount;

                if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
                if (gstAmountEl) gstAmountEl.value = gstAmount.toFixed(2);
                if (grandTotalEl) grandTotalEl.value = grandTotal.toFixed(2);
            }

            document.addEventListener('input', (event) => {
                if (event.target.matches('[name^="items"][name$="[quantity]"], [name^="items"][name$="[price]"], #gstPercent')) {
                    updatePreview();
                }
            });

            updatePreview();
        })();
    </script>
</x-app-layout>
