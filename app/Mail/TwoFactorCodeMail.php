<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your Two-Factor Authentication Code'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '
            <div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <h2 style="color: #4f46e5; text-align: center;">' . __('Authentication Code') . '</h2>
                <p style="color: #374151; font-size: 16px;">' . __('Hello,') . '</p>
                <p style="color: #374151; font-size: 16px;">' . __('Here is your two-factor authentication code. It is valid for 10 minutes.') . '</p>
                <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
                    <span style="font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #111827;">' . $this->code . '</span>
                </div>
                <p style="color: #6b7280; font-size: 14px;">' . __('If you did not request this code, please ignore this email or secure your account.') . '</p>
            </div>
            ',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
