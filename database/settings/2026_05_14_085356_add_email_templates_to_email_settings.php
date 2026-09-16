<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Email.estimate_subject', 'New Estimation: {estimate_number}');
        $this->migrator->add('Email.estimate_body', 'Hello {client_name},<br><br>Please find attached the estimation {estimate_number} for your review.<br><br>Regards,<br>{company_name}');
        
        $this->migrator->add('Email.invoice_subject', 'Invoice Received: {invoice_number}');
        $this->migrator->add('Email.invoice_body', 'Hello {client_name},<br><br>We have sent you an invoice {invoice_number}. Please find the details in the attached PDF.<br><br>Regards,<br>{company_name}');
    }
};
