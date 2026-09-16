<?php

namespace App\Mail;

use App\Models\Itinerary;
use App\Settings\EmailSetting;
use App\Settings\GeneralSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ItineraryMail extends Mailable
{
    use Queueable, SerializesModels;

    public Itinerary $itinerary;
    public $subject_text;
    public $body_text;

    public function __construct(Itinerary $itinerary)
    {
        $this->itinerary = $itinerary;
        $settings = app(EmailSetting::class);
        $general = app(GeneralSetting::class);

        $placeholders = [
            '{client_name}' => $itinerary->client_name ?: ($itinerary->client->name ?? __('Client')),
            '{itinerary_name}' => $itinerary->title,
            '{company_name}' => $general->company_name,
        ];

        $this->subject_text = strtr($settings->itinerary_subject ?? __('Your Safari Proposal: {itinerary_name}'), $placeholders);
        $this->body_text = strtr($settings->itinerary_body ?? __("Hi {client_name},\n\nPlease find your requested safari proposal attached to this email.\n\nBest regards,\n{company_name}"), $placeholders);
    }

    /**
     * Get the message envelope.
     */
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
        $this->itinerary->load(['days.destination', 'days.accommodation', 'days.mealPlan', 'days.activities', 'days.transports', 'days.animals']);
        
        $pdf = Pdf::loadView('pdf.itinerary', [
            'itinerary' => $this->itinerary,
            'settings' => $settings
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), "Proposal-{$this->itinerary->reference_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
