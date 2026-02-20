<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<!-- Card -->
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">

    <!-- Header -->
    <tr>
        <td style="background:#16a34a;color:#ffffff;padding:22px;text-align:center;font-size:22px;font-weight:bold;">
            Invoice SaaS
        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="padding:30px;">

            <h2 style="margin-top:0;color:#111827;">Payment Receipt</h2>

            <p style="color:#374151;">
                Dear {{ $invoice->client->name ?? 'Customer' }},
            </p>

            <p style="color:#374151;">
                Thank you for your payment! Your invoice has been marked as paid successfully.
            </p>

            <!-- Info Box -->
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="margin-top:20px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;padding:18px;">

                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Invoice Number</td>
                    <td align="right" style="font-weight:bold;">{{ $invoice->invoice_number }}</td>
                </tr>

                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Paid On</td>
                    <td align="right">{{ now()->format('d M Y') }}</td>
                </tr>

                @php
                $hasGst =
                ($invoice->cgst_total ?? 0) > 0 ||
                ($invoice->sgst_total ?? 0) > 0 ||
                ($invoice->igst_total ?? 0) > 0;
                @endphp
                @if($hasGst)
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Subtotal</td>
                    <td align="right">₹{{ number_format($invoice->total, 2) }}</td>
                </tr>
                @if($invoice->cgst_total > 0)
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">CGST</td>
                    <td align="right">₹{{ number_format($invoice->cgst_total, 2) }}</td>
                </tr>
                @endif
                @if($invoice->sgst_total > 0)
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">SGST</td>
                    <td align="right">₹{{ number_format($invoice->sgst_total, 2) }}</td>
                </tr>
                @endif
                @if($invoice->igst_total > 0)
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">IGST</td>
                    <td align="right">₹{{ number_format($invoice->igst_total, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Amount Paid</td>
                    <td align="right" style="font-size:18px;font-weight:bold;">
                        ₹{{ number_format($invoice->grand_total ?? $invoice->total, 2) }}
                    </td>
                </tr>
                @else
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Amount Paid</td>
                    <td align="right" style="font-size:18px;font-weight:bold;">
                        ₹{{ number_format($invoice->total, 2) }}
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Status</td>
                    <td align="right">
                        <span style="background:#dcfce7;color:#166534;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:bold;">
                            PAID
                        </span>
                    </td>
                </tr>

            </table>

            <p style="margin-top:22px;color:#374151;">
                📎 A PDF copy of your paid invoice is attached with this email for your records.
            </p>

            <p style="margin-top:30px;">
                Best regards,<br>
                <strong>Invoice SaaS Team</strong>
            </p>

        </td>
    </tr>

</table>

<!-- Footer -->
<p style="margin-top:18px;font-size:12px;color:#9ca3af;text-align:center;">
    This is an automated email. Please do not reply.
</p>

</td>
</tr>
</table>

</body>
</html>
