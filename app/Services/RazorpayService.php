<?php

namespace App\Services;

use App\Models\Invoice;
use Razorpay\Api\Api;

class RazorpayService
{
    protected ?Api $api = null;

    public function __construct()
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if ($key && $secret) {
            $this->api = new Api($key, $secret);
        }
    }

    /**
     * Create Razorpay order for invoice and store order_id on invoice.
     */
    public function createOrderForInvoice(Invoice $invoice): void
    {
        if (! $this->api) {
            throw new \RuntimeException('Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env');
        }

        $amountPaise = (int) round(($invoice->grand_total ?? $invoice->total) * 100);

        if ($amountPaise < 100) {
            throw new \InvalidArgumentException('Invoice amount must be at least ₹1');
        }

        $order = $this->api->order->create([
            'receipt' => 'inv_' . $invoice->invoice_number . '_' . $invoice->id,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'notes' => [
                'invoice_id' => (string) $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        $invoice->update(['razorpay_order_id' => $order['id']]);
    }

    /**
     * Generate payment link URL for the invoice.
     * Returns the public pay URL with token for secure access.
     */
    public function generatePaymentLink(Invoice $invoice): string
    {
        return route('invoice.public.pay', [
            'invoice' => $invoice,
            'token' => $invoice->payment_token,
        ]);
    }
}
