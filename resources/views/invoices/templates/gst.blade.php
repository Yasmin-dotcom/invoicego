<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GST Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .gst-badge {
            background: #1e40af;
            color: #fff;
            padding: 4px 10px;
            font-size: 10px;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #f3f4f6;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .right {
            text-align: right;
        }

        .totals td {
            border: none;
            padding: 4px;
        }

        .grand {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>

@php
    $hasGst = ($invoice->cgst_total ?? 0) > 0
           || ($invoice->sgst_total ?? 0) > 0
           || ($invoice->igst_total ?? 0) > 0;
@endphp

<div class="header">
    <div class="title">
        TAX INVOICE
        <span class="gst-badge">GST</span>
    </div>

    <strong>{{ $businessUser->business_name ?? '' }}</strong><br>
    GSTIN: {{ $businessUser->gst_number ?? 'N/A' }}<br>
    Invoice #: {{ $invoice->invoice_number }}<br>
    Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
</div>

<div>
    <strong>Billed To:</strong><br>
    {{ $invoice->client->name ?? '' }}<br>
</div>

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
            <td>{{ $item->description ?? $item->item_name ?? '-' }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₹{{ number_format($item->price,2) }}</td>
            <td class="right">₹{{ number_format(($item->quantity ?? 0) * ($item->price ?? 0), 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals" style="margin-top:20px;">
    <tr>
        <td width="70%"></td>
        <td class="right">Subtotal:</td>
        <td class="right">₹{{ number_format($invoice->sub_total ?? 0, 2) }}</td>
    </tr>

    @if(($invoice->cgst_total ?? 0) > 0)
    <tr>
        <td></td>
        <td class="right">CGST:</td>
        <td class="right">₹{{ number_format($invoice->cgst_total,2) }}</td>
    </tr>
    @endif

    @if(($invoice->sgst_total ?? 0) > 0)
    <tr>
        <td></td>
        <td class="right">SGST:</td>
        <td class="right">₹{{ number_format($invoice->sgst_total,2) }}</td>
    </tr>
    @endif

    @if(($invoice->igst_total ?? 0) > 0)
    <tr>
        <td></td>
        <td class="right">IGST:</td>
        <td class="right">₹{{ number_format($invoice->igst_total,2) }}</td>
    </tr>
    @endif

    <tr class="grand">
        <td></td>
        <td class="right">Grand Total:</td>
        <td class="right">₹{{ number_format($invoice->total,2) }}</td>
    </tr>
</table>

<div style="margin-top:40px; font-size:10px; text-align:center; color:#777;">
    This is a GST compliant tax invoice generated digitally.
</div>

</body>
</html>

