@php
    $brandColor = $businessUser->brand_color ?? '#4f46e5';

    $status = strtolower($invoice->status ?? 'draft');

    $statusColor = match($status) {
        'paid' => '#16a34a',
        'overdue' => '#dc2626',
        default => '#f59e0b'
    };

    $hasGst = ($invoice->cgst_total ?? 0) > 0 
           || ($invoice->sgst_total ?? 0) > 0 
           || ($invoice->igst_total ?? 0) > 0;
@endphp


{{-- HEADER --}}
<table style="border:none; margin-bottom:25px;">
<tr style="border:none;">
<td style="border:none; width:50%; padding:0;">

    {{-- Logo --}}
    @if(!empty($businessUser->logo))
        <img src="{{ public_path('storage/'.$businessUser->logo) }}" 
             style="height:60px;">
    @else
        <div style="font-size:22px; font-weight:bold; color:{{ $brandColor }}">
            {{ $businessUser->business_name ?? 'Business Name' }}
        </div>
    @endif

</td>

<td style="border:none; text-align:right; padding:0;">

    <div style="font-size:26px; font-weight:bold;">INVOICE</div>
    <div>#{{ $invoice->invoice_number }}</div>

    {{-- Status Badge --}}
    <div style="
        display:inline-block;
        margin-top:6px;
        padding:4px 10px;
        font-size:11px;
        color:#fff;
        background:{{ $statusColor }};
        border-radius:12px;">
        {{ strtoupper($status) }}
    </div>

</td>
</tr>
</table>


{{-- BILLING INFO --}}
<table style="border:none; margin-bottom:25px;">
<tr style="border:none;">
<td style="border:none; padding:0;">
    <strong>Billed To:</strong><br>
    {{ $invoice->client->name ?? '-' }}
</td>

<td style="border:none; text-align:right; padding:0;">
    <strong>Date:</strong> 
    {{ optional($invoice->invoice_date)->format('d M Y') }} <br>

    <strong>Due:</strong> 
    {{ optional($invoice->due_date)->format('d M Y') }}
</td>
</tr>
</table>


{{-- ITEMS TABLE --}}
<table>
<thead>
<tr>
<th>Description</th>
<th class="right">Qty</th>
<th class="right">Price</th>
<th class="right">Amount</th>
</tr>
</thead>

<tbody>
@foreach($invoice->items as $item)
<tr>
<td>{{ $item->description }}</td>
<td class="right">{{ $item->quantity }}</td>
<td class="right">₹{{ number_format($item->price,2) }}</td>
<td class="right">
₹{{ number_format($item->quantity * $item->price,2) }}
</td>
</tr>
@endforeach
</tbody>
</table>


{{-- TOTALS --}}
<div class="total-box">

<table>
<tr>
<td>Subtotal</td>
<td class="right">₹{{ number_format($invoice->subtotal ?? 0,2) }}</td>
</tr>

@if($hasGst)
<tr>
<td>CGST</td>
<td class="right">₹{{ number_format($invoice->cgst_total ?? 0,2) }}</td>
</tr>

<tr>
<td>SGST</td>
<td class="right">₹{{ number_format($invoice->sgst_total ?? 0,2) }}</td>
</tr>

<tr>
<td>IGST</td>
<td class="right">₹{{ number_format($invoice->igst_total ?? 0,2) }}</td>
</tr>
@endif

<tr>
<td style="font-weight:bold;">Grand Total</td>
<td class="right" style="
    font-weight:bold;
    font-size:14px;
    color:{{ $brandColor }}">
₹{{ number_format($invoice->total ?? 0,2) }}
</td>
</tr>
</table>

</div>


{{-- PAYMENT SECTION --}}
@if(!empty($invoice->razorpay_payment_link))
<div style="margin-top:40px;">
    <strong>Pay Online:</strong><br>
    <a href="{{ $invoice->razorpay_payment_link }}" 
       style="color:{{ $brandColor }}">
       {{ $invoice->razorpay_payment_link }}
    </a>
</div>
@endif


{{-- QR CODE --}}
@if(!empty($invoice->upi_link))
<div style="margin-top:25px;">
    <strong>Scan to Pay:</strong><br>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($invoice->upi_link) }}">
</div>
@endif


{{-- SIGNATURE --}}
@if(!empty($businessUser->signature))
<div style="margin-top:60px;">
    <img src="{{ public_path('storage/'.$businessUser->signature) }}" 
         style="height:50px;"><br>
    Authorized Signature
</div>
@endif


{{-- FOOTER --}}
<div style="
    margin-top:80px;
    font-size:10px;
    color:#777;
    text-align:center;">
    This invoice was generated digitally by {{ config('app.name') }}.
</div>
