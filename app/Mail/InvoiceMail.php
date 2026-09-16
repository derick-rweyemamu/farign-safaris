<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Settings\EmailSetting;
use App\Settings\GeneralSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public $subject_text;
    public $body_text;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $settings = app(EmailSetting::class);
        $general = app(GeneralSetting::class);

        $placeholders = [
            '{client_name}' => $invoice->client->name ?? __('Client'),
            '{invoice_number}' => $invoice->invoice_number,
            '{company_name}' => $general->company_name,
        ];

        $this->subject_text = strtr($settings->invoice_subject, $placeholders);
        $this->body_text = strtr($settings->invoice_body, $placeholders);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject_text,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.generic',
            with: [
                'body' => $this->body_text,
                'subject' => $this->subject_text,
            ],
        );
    }

    public function attachments(): array
    {
        $settings = app(GeneralSetting::class);
        $this->invoice->load(['client', 'items']);
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->invoice,
            'settings' => $settings
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Invoice-{$this->invoice->invoice_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
