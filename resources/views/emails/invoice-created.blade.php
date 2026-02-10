<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Invoice</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <!-- HEADER -->
    <div style="background-color:#1e3a8a; padding:18px; text-align:center; border-radius:8px 8px 0 0;">
        <h1 style="margin:0; font-size:22px; color:white; font-weight:bold;">
            Invoice SaaS
        </h1>
    </div>


    <!-- BODY -->
    <div style="background-color:#f9fafb; padding:30px; border:1px solid #e5e7eb; border-top:none; border-radius:0 0 8px 8px;">

        <h2 style="color:#111827; margin-top:0;">New Invoice Created</h2>
        
        <p>Dear {{ $client->name ?? 'Valued Client' }},</p>
        
        <p>A new invoice has been created for you. Please find the details below:</p>


        <!-- INVOICE CARD -->
        <div style="background-color:white; padding:20px; border-radius:6px; margin:20px 0; border:1px solid #e5e7eb;">
            <table style="width:100%; border-collapse:collapse;">

                <tr>
                    <td style="padding:8px 0; font-weight:bold; color:#6b7280;">Invoice Number:</td>
                    <td style="padding:8px 0; color:#111827;">{{ $invoice->invoice_number }}</td>
                </tr>

                <tr>
                    <td style="padding:8px 0; font-weight:bold; color:#6b7280;">Invoice Date:</td>
                    <td style="padding:8px 0; color:#111827;">
                        {{ optional($invoice->invoice_date)->format('d M Y') }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 0; font-weight:bold; color:#6b7280;">Due Date:</td>
                    <td style="padding:8px 0; color:#111827;">
                        {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '-' }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 0; font-weight:bold; color:#6b7280;">Total Amount:</td>
                    <td style="padding:8px 0; color:#111827; font-size:18px; font-weight:bold;">
                        ₹{{ number_format($invoice->total, 2) }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 0; font-weight:bold; color:#6b7280;">Status:</td>
                    <td style="padding:8px 0;">
                        <span style="background-color:#fef3c7; color:#92400e; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:bold;">
                            {{ strtoupper($invoice->lifecycle_status ?? $invoice->status ?? 'DRAFT') }}
                        </span>
                    </td>
                </tr>

            </table>
        </div>


        <p style="margin-top:30px;">
            A PDF copy of the invoice is attached to this email for your records.
        </p>


        <!-- PAY BUTTON (🔥 FIXED – SAME AS REMINDER WITH TOKEN) -->
        @php($status = strtoupper($invoice->lifecycle_status ?? $invoice->status ?? 'DRAFT'))

        @if($status !== 'PAID')
            <div style="text-align:center; margin:30px 0;">
                <a
                    href="{{ route('invoice.public.pay', ['invoice' => $invoice->id, 'token' => $invoice->payment_token]) }}"
                    style="display:inline-block;background:#16a34a;color:#ffffff;padding:14px 35px;text-decoration:none;border-radius:6px;font-weight:bold;font-size:16px;">
                    Pay Now
                </a>
            </div>
        @endif


        <p>Please make payment by the due date to avoid any late fees.</p>

        <p style="margin-top:30px;">
            Best regards,<br>
            <strong>Invoice SaaS Team</strong>
        </p>

    </div>


    <!-- FOOTER -->
    <div style="text-align:center; margin-top:20px; color:#6b7280; font-size:12px;">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>

</body>
</html>
