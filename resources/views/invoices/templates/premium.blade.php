<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Premium Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #2d3748;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .logo img {
            height: 60px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 11px;
            border-radius: 20px;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .right {
            text-align: right;
        }

        .totals {
            width: 40%;
            float: right;
            margin-top: 20px;
        }

        .totals td {
            padding: 6px;
        }

        .grand {
            font-weight: bold;
            font-size: 15px;
        }

        .footer {
            margin-top: 80px;
            font-size: 11px;
            text-align: center;
            color: #777;
        }

    </style>
</head>

<body>

@php
    $brandColor = $businessUser->brand_color ?? '#4f46e5';
    $status = strtolower($invoice->status ?? 'draft');

    $statusColor = match($status) {
        'paid' => '#16a34a',
        'overdue' => '#dc2626',
        default => '#f59e0b'
    };
@endphp

<div class="header">
    <div class="logo">
        @if(!empty($businessUser->logo))
            <img src="{{ public_path('storage/'.$businessUser->logo) }}">
        @endif
    </div>

    <div style="text-align:right;">
        <div class="invoice-title" style="color: {{ $brandColor }}">
            INVOICE
        </div>

        <span class="badge" style="background: {{ $statusColor }}">
            {{ strtoupper($invoice->status ?? 'DRAFT') }}
        </span>

        <div style="margin-top:8px;">
            #{{ $invoice->invoice_number ?? 'N/A' }} <br>
            Date: {{ $invoice->invoice_date ?? '-' }}
        </div>
    </div>
</div>

<hr>

<strong>Billed To:</strong><br>
{{ $invoice->client_name ?? 'Client Name' }}

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
            <td>{{ $item->description ?? '-' }}</td>
            <td class="right">{{ $item->quantity ?? 0 }}</td>
            <td class="right">₹{{ number_format($item->price ?? 0,2) }}</td>
            <td class="right">
                ₹{{ number_format(($item->quantity ?? 0) * ($item->price ?? 0),2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td>Subtotal</td>
        <td class="right">₹{{ number_format($invoice->sub_total ?? 0,2) }}</td>
    </tr>

    @if(($invoice->cgst_total ?? 0) > 0)
    <tr>
        <td>CGST</td>
        <td class="right">₹{{ number_format($invoice->cgst_total ?? 0,2) }}</td>
    </tr>
    @endif

    @if(($invoice->sgst_total ?? 0) > 0)
    <tr>
        <td>SGST</td>
        <td class="right">₹{{ number_format($invoice->sgst_total ?? 0,2) }}</td>
    </tr>
    @endif

    @if(($invoice->igst_total ?? 0) > 0)
    <tr>
        <td>IGST</td>
        <td class="right">₹{{ number_format($invoice->igst_total ?? 0,2) }}</td>
    </tr>
    @endif

    <tr class="grand">
        <td>Grand Total</td>
        <td class="right">₹{{ number_format($invoice->grand_total ?? 0,2) }}</td>
    </tr>
</table>

<div style="clear:both;"></div>

@if(!empty($businessUser->signature))
    <div style="margin-top:60px;">
        <img src="{{ public_path('storage/'.$businessUser->signature) }}" height="60"><br>
        Authorized Signature
    </div>
@endif

<div class="footer">
    This is a digitally generated premium invoice.
</div>

</body>
</html>

