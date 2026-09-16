<?php

namespace App\Mail;

use App\Models\Estimation;
use App\Settings\EmailSetting;
use App\Settings\GeneralSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstimateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Estimation $estimate;
    public $subject_text;
    public $body_text;

    public function __construct(Estimation $estimate)
    {
        $this->estimate = $estimate;
        $settings = app(EmailSetting::class);
        $general = app(GeneralSetting::class);

        $placeholders = [
            '{client_name}' => $estimate->client->name ?? __('Client'),
            '{estimate_number}' => $estimate->estimate_number,
            '{company_name}' => $general->company_name,
        ];

        $this->subject_text = strtr($settings->estimate_subject, $placeholders);
        $this->body_text = strtr($settings->estimate_body, $placeholders);
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
        $this->estimate->load(['client', 'items']);
        
        $pdf = Pdf::loadView('pdf.estimate', [
            'estimate' => $this->estimate,
            'settings' => $settings
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Estimate-{$this->estimate->estimate_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
