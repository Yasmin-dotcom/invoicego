<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }
        .header {
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .paid {
            background: #d1fae5;
            color: #065f46;
        }
        .unpaid {
            background: #fef3c7;
            color: #92400e;
        }
        .overdue {
            background: #fee2e2;
            color: #991b1b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f3f4f6;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Invoice</div>
        <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>

        <p>
            <strong>Status:</strong>
            @php($status = strtoupper($invoice->lifecycle_status ?? $invoice->status ?? 'DRAFT'))
            @if($status === 'PAID')
                <span class="badge paid">PAID</span>
            @elseif($status === 'OVERDUE')
                <span class="badge overdue">OVERDUE</span>
            @elseif($status === 'SENT')
                <span class="badge unpaid">SENT</span>
            @else
                <span class="badge unpaid">DRAFT</span>
            @endif
        </p>
    </div>

    <p><strong>Client:</strong> {{ $invoice->client->name ?? '-' }}</p>

    <p>
        <strong>Invoice Date:</strong>
        {{ optional($invoice->invoice_date)->format('d M Y') }}
    </p>

    <p>
        <strong>Due Date:</strong>
        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '-' }}
    </p>

    @if($invoice->status === 'paid' && $invoice->paid_at)
        <p>
            <strong>Paid On:</strong>
            {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y') }}
        </p>
    @endif

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
                    <td class="right">
                        ₹{{ number_format($item->quantity * $item->price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="right" style="margin-top: 15px;">
        Total: ₹{{ number_format($invoice->total, 2) }}
    </h3>

</body>
</html>
