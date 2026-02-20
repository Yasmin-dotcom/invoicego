<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pay Invoice - {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-indigo-600 px-6 py-5">
            <h1 class="text-xl font-semibold text-white">
                {{ $invoice->user->business_name ?? $invoice->user->name ?? config('app.name') }}
            </h1>
            <p class="text-indigo-100 text-sm mt-1">Invoice Payment</p>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">

            @if(session('info'))
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-amber-800 text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <div class="grid grid-cols-2 gap-2 text-sm">
                <span class="text-gray-500">Invoice #</span>
                <span class="font-medium text-right">{{ $invoice->invoice_number }}</span>
                <span class="text-gray-500">Client</span>
                <span class="font-medium text-right">{{ $invoice->client->name ?? '-' }}</span>
                <span class="text-gray-500">Date</span>
                <span class="font-medium text-right">{{ optional($invoice->invoice_date)->format('d M Y') }}</span>
            </div>

            {{-- GST breakdown --}}
            @php $hasGst = $invoice->cgst_total !== null || $invoice->sgst_total !== null || $invoice->igst_total !== null; @endphp

            <div class="border-t border-gray-100 pt-4 mt-4 space-y-2">
                @if($hasGst)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span>₹{{ number_format($invoice->total, 2) }}</span>
                    </div>
                    @if($invoice->cgst_total > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">CGST</span>
                        <span>₹{{ number_format($invoice->cgst_total, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->sgst_total > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">SGST</span>
                        <span>₹{{ number_format($invoice->sgst_total, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->igst_total > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">IGST</span>
                        <span>₹{{ number_format($invoice->igst_total, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold text-lg pt-2">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($invoice->grand_total ?? $invoice->total, 2) }}</span>
                    </div>
                @else
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Total</span>
                        <span>₹{{ number_format($invoice->total, 2) }}</span>
                    </div>
                @endif
            </div>

            {{-- Pay Now --}}
            <a href="{{ route('invoice.public.pay', ['invoice' => $invoice, 'token' => $invoice->payment_token]) }}"
               class="block w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-center rounded-lg transition">
                Pay Now
            </a>

            <p class="text-xs text-gray-400 text-center">
                Secure payment via Razorpay
            </p>
        </div>
    </div>

</body>
</html>
