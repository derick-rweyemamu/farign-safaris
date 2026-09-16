<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class EmailSetting extends Settings
{
    public string $mail_transport;
    public string $mail_host;
    public int $mail_port;
    public string $mail_username;
    public string $mail_password;
    public string $mail_encryption;
    public string $mail_from_address;
    public string $mail_from_name;

    public string $estimate_subject;
    public string $estimate_body;
    public string $invoice_subject;
    public string $invoice_body;
    public string $itinerary_subject;
    public string $itinerary_body;

    public static function group(): string
    {
        return 'Email';
    }
}
