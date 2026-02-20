<x-app-layout>
<div class="p-6 max-w-6xl mx-auto">

@php
    $isDraft = $invoice->normalizedStatus() === \App\Models\Invoice::STATUS_DRAFT;
    $isPaid = $invoice->normalizedStatus() === \App\Models\Invoice::STATUS_PAID;
    $canMarkPaid = $invoice->normalizedStatus() === \App\Models\Invoice::STATUS_SENT;
    $isRemindable = in_array($invoice->normalizedStatus(), [\App\Models\Invoice::STATUS_SENT, \App\Models\Invoice::STATUS_OVERDUE], true);

    $itemsTotal = $invoice->items?->sum(fn ($item) => $item->total) ?? 0;
    $displayAmount = $invoice->grand_total ?? $invoice->total;
    $businessName = $invoice->user->business_name ?? $invoice->user->name ?? config('app.name');
    $paymentLink = route('invoice.public.pay', ['invoice' => $invoice, 'token' => $invoice->payment_token]);
    $whatsappMessage = rawurlencode("Hi {$invoice->client->name},\n\nPayment reminder from {$businessName}\n\nInvoice: {$invoice->invoice_number}\nAmount: ₹" . number_format($displayAmount, 2) . "\n\nPay here: {$paymentLink}");
@endphp


{{-- Alerts --}}
@if(session('success'))
<div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800">
    {{ session('success') }}
</div>
@endif



{{-- Header --}}
<div class="flex justify-between items-center mb-8">

    <h1 class="text-2xl font-bold">
        Invoice {{ $invoice->invoice_number }}
    </h1>

    <div class="flex gap-2">

        <a href="{{ route('invoices.download', $invoice) }}"
           class="px-4 py-2 bg-gray-900 text-white rounded">
            Download PDF
        </a>

        @if($canMarkPaid)
        <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
            @csrf
            <button class="px-4 py-2 bg-green-600 text-white rounded">
                Mark Paid
            </button>
        </form>
        @endif

        @if($isRemindable && $invoice->client->phone && $invoice->payment_token)
        @php $phoneDigits = preg_replace('/[^0-9]/', '', $invoice->client->phone); $waNumber = (strlen($phoneDigits) === 10 ? '91' : '') . $phoneDigits; @endphp
        <a href="https://wa.me/{{ $waNumber }}?text={{ $whatsappMessage }}"
           target="_blank"
           rel="noopener noreferrer"
           class="px-4 py-2 bg-[#25D366] text-white rounded hover:bg-[#20bd5a] inline-flex items-center gap-2">
            <span>Send WhatsApp Reminder</span>
        </a>
        @endif

        <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-gray-600">
            ← Back
        </a>
    </div>
</div>



{{-- Info Card --}}
<div class="bg-white shadow rounded-xl p-6 grid grid-cols-3 gap-6">

    <div>
        <div class="text-xs text-gray-500">Client</div>
        <div class="font-semibold">{{ $invoice->client->name }}</div>
    </div>

    <div>
        <div class="text-xs text-gray-500">Amount</div>
        <div class="font-semibold">₹{{ number_format($itemsTotal,2) }}</div>
    </div>

    <div>
        <div class="text-xs text-gray-500">Status</div>
        <div class="font-semibold uppercase">{{ $invoice->status }}</div>
    </div>

</div>

{{-- Invoice Timeline --}}
<div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 mt-8">
    <h3 class="text-sm font-semibold mb-4">Invoice Timeline</h3>
    <div class="flex items-start justify-between">
        @foreach($timeline as $step)
            @php $isDone = !empty($step['time']); @endphp
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="w-4 h-4 rounded-full {{ $isDone ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <span class="mt-2 text-xs font-medium {{ $isDone ? 'text-green-700' : 'text-gray-500' }}">
                    {{ $step['label'] }}
                </span>
                <span class="text-xs text-gray-400">
                    {{ $step['time'] ? $step['time']->format('d M, h:i A') : 'Pending' }}
                </span>
            </div>
            @if(! $loop->last)
                <div class="mt-2 h-0.5 flex-1 {{ !empty($timeline[$loop->index + 1]['time']) ? 'bg-green-300' : 'bg-gray-200' }}"></div>
            @endif
        @endforeach
    </div>
</div>

{{-- ================= ITEMS SECTION ================= --}}
<div class="bg-white shadow rounded-xl p-6 mt-8">

    <h2 class="text-lg font-semibold mb-4">Invoice Items</h2>


    {{-- 🔥 ADD ITEM FORM (ONLY DRAFT) --}}
    @if($isDraft)
    <form action="{{ route('invoices.items.store', $invoice) }}" method="POST"
          class="grid grid-cols-6 gap-3 mb-6">
        @csrf

        <input name="name" placeholder="Item name" required
               class="border rounded px-3 py-2">

        <input name="quantity" type="number" step="1" min="1" value="1" required
               class="border rounded px-3 py-2">

        <input name="price" type="number" step="0.01" placeholder="Price" required
               class="border rounded px-3 py-2">

        <input name="gst_rate" type="number" step="0.01" min="0" max="100" value="0"
               placeholder="GST %"
               class="border rounded px-3 py-2">

        <button class="bg-blue-600 text-white rounded px-4">
            Add
        </button>
    </form>
    @endif



    {{-- TABLE --}}
    <table class="min-w-full border">
        <thead>
        <tr class="bg-gray-50">
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>GST %</th>
            <th>Total</th>
            @if($isDraft)
            <th>Action</th>
            @endif
        </tr>
        </thead>

        <tbody>
        @forelse($invoice->items as $item)
        <tr class="border-t">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>₹{{ $item->price }}</td>
            <td>{{ $item->gst_rate ?? 0 }}%</td>
            <td>₹{{ $item->total }}</td>

            @if($isDraft)
            <td>
                <form action="{{ route('invoices.items.destroy', $item) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Delete</button>
                </form>
            </td>
            @endif
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-gray-400">No items added</td>
        </tr>
        @endforelse
        </tbody>
    </table>



    <div class="flex justify-end mt-6">
        <div class="w-full max-w-sm rounded border border-gray-200 bg-gray-50 p-4">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <input id="subtotalPreview" type="text" readonly
                       value="{{ number_format($itemsTotal,2) }}"
                       class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
            </div>
            <div class="mt-3 flex items-center justify-between text-sm text-gray-600">
                <label for="gstPercent" class="mr-3">GST %</label>
                <input id="gstPercent" type="number" step="0.01" min="0"
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
                       value="{{ number_format($itemsTotal,2) }}"
                       class="w-24 text-right bg-transparent border-0 p-0 text-gray-900" />
            </div>
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

            function calculateTableSubtotal() {
                const rows = Array.from(document.querySelectorAll('table tbody tr'));
                let subtotal = 0;

                rows.forEach((row) => {
                    const cells = row.querySelectorAll('td');
                    if (!cells || cells.length < 5) return;
                    const cellText = cells[4].textContent || '';
                    subtotal += parseNumber(cellText);
                });

                return subtotal;
            }

            function calculateDraftInputSubtotal() {
                const qtyInput = document.querySelector('input[name="quantity"]');
                const priceInput = document.querySelector('input[name="price"]');
                if (!qtyInput || !priceInput) return 0;
                return parseNumber(qtyInput.value) * parseNumber(priceInput.value);
            }

            function updatePreview() {
                const subtotal = calculateTableSubtotal() + calculateDraftInputSubtotal();
                const gstPercent = parseNumber(gstInput?.value);
                const gstAmount = subtotal * (gstPercent / 100);
                const grandTotal = subtotal + gstAmount;

                if (subtotalEl) subtotalEl.value = subtotal.toFixed(2);
                if (gstAmountEl) gstAmountEl.value = gstAmount.toFixed(2);
                if (grandTotalEl) grandTotalEl.value = grandTotal.toFixed(2);
            }

            document.addEventListener('input', (event) => {
                if (event.target.matches('input[name="quantity"], input[name="price"], #gstPercent')) {
                    updatePreview();
                }
            });

            updatePreview();
        })();
    </script>

</div>

</div>
</x-app-layout>
