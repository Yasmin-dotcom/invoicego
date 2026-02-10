<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Reminder type: 'before_due', 'due_today', or 'overdue'
     */
    public string $reminderType = 'before_due';

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice,
        string $reminderType = 'before_due'
    ) {
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->reminderType) {
            'before_due' => 'Upcoming Payment Reminder: ' . $this->invoice->invoice_number,
            'due_today' => 'Payment Due Today: ' . $this->invoice->invoice_number,
            'overdue' => 'Overdue Payment Reminder: ' . $this->invoice->invoice_number,
            default => 'Payment Reminder: ' . $this->invoice->invoice_number,
        };

        return new Envelope(
            from: config('mail.from.address', 'noreply@invoice-saas.local'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-payment-reminder',
            with: [
                'invoice' => $this->invoice,
                'client' => $this->invoice->client,
                'paymentLink' => $this->invoice->razorpay_payment_link ?? url('/razorpay/pay/' . $this->invoice->id . '?amount=' . $this->invoice->total),
                'reminderType' => $this->reminderType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate PDF for attachment
        $invoice = $this->invoice->load(['client', 'items']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'invoice-' . $this->invoice->invoice_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
