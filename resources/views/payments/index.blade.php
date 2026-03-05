<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Payments
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                @if($payments->isEmpty())
                    <div class="text-sm text-gray-600">No payments yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                            <tr class="text-left text-gray-600 border-b border-gray-200">
                                <th class="py-3 pr-4 font-medium">Invoice No</th>
                                <th class="py-3 pr-4 font-medium">Client</th>
                                <th class="py-3 pr-4 font-medium">Subtotal</th>
                                <th class="py-3 pr-4 font-medium">GST</th>
                                <th class="py-3 pr-4 font-medium">Total</th>
                                <th class="py-3 pr-4 font-medium">Payment Date</th>
                                <th class="py-3 pr-4 font-medium">Status</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($payments as $inv)
                                <tr class="text-gray-800">
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 pr-4">{{ $inv->client->name ?? '-' }}</td>
                                    <td class="py-3 pr-4">₹{{ number_format((float) ($inv->subtotal_calc ?? 0), 2) }}</td>
                                    <td class="py-3 pr-4">₹{{ number_format((float) ($inv->gst_calc ?? 0), 2) }}</td>
                                    <td class="py-3 pr-4">
                                        ₹{{ number_format((float) (($inv->subtotal_calc ?? 0) + ($inv->gst_calc ?? 0)), 2) }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        {{ $inv->paid_at ? \Carbon\Carbon::parse($inv->paid_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                            {{ strtoupper((string) ($inv->status ?? 'paid')) }}
                                        </span>
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
</x-app-layout>

