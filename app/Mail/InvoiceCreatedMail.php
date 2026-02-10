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

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'noreply@invoice-saas.local'),
            subject: 'New Invoice: ' . $this->invoice->invoice_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-created',
            with: [
                'invoice' => $this->invoice,
                'client' => $this->invoice->client,
                'paymentLink' => $this->invoice->razorpay_payment_link,
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
