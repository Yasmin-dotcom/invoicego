<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        .header {
            margin-bottom: 12px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        .meta {
            font-size: 11px;
            color: #4B5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #E5E7EB;
            padding: 6px;
        }
        th {
            background:#F9FAFB;
            font-size: 11px;
            text-align:left;
        }
        .right {
            text-align:right;
        }
    </style>
</head>

<body>

@php
    $hasGst =
        ($invoice->cgst_total ?? 0) > 0 ||
        ($invoice->sgst_total ?? 0) > 0 ||
        ($invoice->igst_total ?? 0) > 0;
@endphp

<div class="header">
    <div class="title">Invoice</div>
    <div class="meta">
        #{{ $invoice->invoice_number }}<br>
        {{ $businessUser->business_name ?? '-' }}<br>
        Client: {{ $invoice->client->name ?? '-' }}
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>Description</th>
        <th class="right">Qty</th>
        <th class="right">Price</th>
        <th class="right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₹{{ number_format($item->price, 2) }}</td>
            <td class="right">₹{{ number_format($item->quantity * $item->price, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if($hasGst)
    <table style="margin-top:10px;width:auto;margin-left:auto;">
        <tr>
            <td class="right" style="border:none;padding:3px 6px;">Subtotal</td>
            <td class="right" style="border:none;padding:3px 6px;">
                ₹{{ number_format($invoice->total, 2) }}
            </td>
        </tr>

        @if(($invoice->cgst_total ?? 0) > 0)
            <tr>
                <td class="right" style="border:none;padding:3px 6px;">CGST</td>
                <td class="right" style="border:none;padding:3px 6px;">
                    ₹{{ number_format($invoice->cgst_total, 2) }}
                </td>
            </tr>
        @endif

        @if(($invoice->sgst_total ?? 0) > 0)
            <tr>
                <td class="right" style="border:none;padding:3px 6px;">SGST</td>
                <td class="right" style="border:none;padding:3px 6px;">
                    ₹{{ number_format($invoice->sgst_total, 2) }}
                </td>
            </tr>
        @endif

        @if(($invoice->igst_total ?? 0) > 0)
            <tr>
                <td class="right" style="border:none;padding:3px 6px;">IGST</td>
                <td class="right" style="border:none;padding:3px 6px;">
                    ₹{{ number_format($invoice->igst_total, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td class="right" style="border:none;padding:3px 6px;font-weight:bold;">Grand Total</td>
            <td class="right" style="border:none;padding:3px 6px;font-weight:bold;">
                ₹{{ number_format($invoice->grand_total ?? $invoice->total, 2) }}
            </td>
        </tr>
    </table>
@else
    <h3 class="right" style="margin-top:10px;font-size:12px;">
        Total: ₹{{ number_format($invoice->total, 2) }}
    </h3>
@endif

</body>
</html>


