<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #111827; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2>Thank you! Payment Successful</h2>

    <p>Your payment has been received.</p>

    <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
    <p><strong>Amount:</strong> ₹{{ number_format($invoice->total, 2) }}</p>
    <p><strong>Status:</strong> {{ strtoupper($invoice->status ?? 'PAID') }}</p>

    <div style="margin-top: 24px;">
        <a href="{{ route('invoices.show', $invoice) }}"
           style="display: inline-block; background-color: #1f2937; color: #fff; padding: 10px 16px; text-decoration: none; border-radius: 6px; margin-right: 8px;">
            View Invoice
        </a>
        <a href="{{ route('dashboard') }}"
           style="display: inline-block; background-color: #e5e7eb; color: #111827; padding: 10px 16px; text-decoration: none; border-radius: 6px;">
            Dashboard
        </a>
    </div>
</body>
</html>
