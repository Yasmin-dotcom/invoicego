<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Invoices') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col items-end">
                @if(!$user->isPro() && $invoices->count() >= 3)
                    <button type="button"
                            disabled
                            title="Free plan limit reached"
                            class="inline-flex items-center rounded-md !bg-gray-400 px-4 py-2 text-sm font-semibold !text-white shadow-sm cursor-not-allowed opacity-80">
                        + Create Invoice
                    </button>
                    <div class="mt-2 text-sm text-gray-600">
                        Free plan limit reached. Upgrade to Pro to create more invoices.
                    </div>
                @else
                    <a href="{{ route('client.invoices.create') }}"
                       class="inline-flex items-center rounded-md !bg-indigo-600 px-4 py-2 text-sm font-semibold !text-white shadow-sm hover:!bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        + Create Invoice
                    </a>
                @endif
            </div>

            @if(!$user->isPro())
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-yellow-800">
                    <div class="font-semibold">Free Plan: You can create limited invoices. Upgrade to Pro.</div>
                </div>
            @endif

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                    <div class="font-semibold">{{ session('success') }}</div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <div class="text-sm text-gray-600">
                        Logged in as: <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                    </div>

                    @if($invoices->isEmpty())
                        <div class="text-gray-600">No invoices yet.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Invoice #</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Created</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($invoices as $inv)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $inv->invoice_number }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">₹{{ number_format($inv->total, 2) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @php
                                                    $status = strtoupper($inv->lifecycle_status ?? $inv->status ?? 'DRAFT');
                                                    $badge = match ($status) {
                                                        'DRAFT' => 'bg-gray-100 text-gray-800',
                                                        'SENT' => 'bg-blue-100 text-blue-800',
                                                        'PAID' => 'bg-green-100 text-green-800',
                                                        'OVERDUE' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                                    {{ $status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($inv->created_at)->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                @if(strtolower((string) $inv->status) === 'pending' && !empty($inv->razorpay_order_id))
                                                    <button type="button"
                                                            class="js-razorpay-pay inline-flex items-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700"
                                                            data-order-id="{{ $inv->razorpay_order_id }}"
                                                            data-amount="{{ (int) round(((float) $inv->total) * 100) }}"
                                                            data-invoice-number="{{ $inv->invoice_number }}"
                                                            data-client-name="{{ $user->name }}">
                                                        Pay Now
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
@push('scripts')
    <script>
        (function () {
            const razorpayKey = @json(config('services.razorpay.key_id'));

            function loadRazorpay() {
                return new Promise((resolve, reject) => {
                    if (window.Razorpay) return resolve();

                    const existing = document.querySelector('script[data-razorpay-checkout="1"]');
                    if (existing) {
                        existing.addEventListener('load', () => resolve());
                        existing.addEventListener('error', () => reject(new Error('Failed to load Razorpay Checkout')));
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
                    script.async = true;
                    script.dataset.razorpayCheckout = '1';
                    script.onload = () => resolve();
                    script.onerror = () => reject(new Error('Failed to load Razorpay Checkout'));
                    document.head.appendChild(script);
                });
            }

            async function openCheckout(button) {
                if (!razorpayKey) {
                    console.error('Missing Razorpay key id (services.razorpay.key_id)');
                    return;
                }

                const orderId = button.dataset.orderId;
                const amount = Number(button.dataset.amount || 0);
                const invoiceNumber = button.dataset.invoiceNumber || '';
                const clientName = button.dataset.clientName || '';

                await loadRazorpay();

                const options = {
                    key: razorpayKey,
                    amount: amount,
                    currency: 'INR',
                    name: @json(config('app.name', 'Invoice SaaS')),
                    description: invoiceNumber ? `Invoice ${invoiceNumber}` : 'Invoice Payment',
                    order_id: orderId,
                    prefill: { name: clientName },
                    notes: { invoice_number: invoiceNumber },
                    handler: function () {
                        // No webhook / paid marking in this step.
                    },
                };

                const rzp = new window.Razorpay(options);
                rzp.open();
            }

            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.js-razorpay-pay');
                if (!btn) return;
                e.preventDefault();
                openCheckout(btn).catch((err) => console.error(err));
            });
        })();
    </script>
@endpush

