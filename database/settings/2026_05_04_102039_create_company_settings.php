<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        // App & Company Info
        $this->migrator->add('company.app_name', null);
        $this->migrator->add('company.company_name', null);
        $this->migrator->add('company.company_logo', null);

        $this->migrator->add('company.address', null);
        $this->migrator->add('company.email', null);
        $this->migrator->add('company.phone', null);
        $this->migrator->add('company.website', null);
        $this->migrator->add('company.tin', null);
        $this->migrator->add('company.vrn', null);

        // Invoice Settings
        $this->migrator->add('company.invoice_prefix', 'INV');
        $this->migrator->add('company.invoice_footer', 'Thank you for doing business with us!');
        $this->migrator->add('company.invoice_note', null);
        $this->migrator->add('company.invoice_terms', null);
        $this->migrator->add('company.invoice_due_days', 30);
        $this->migrator->add('company.invoice_send_reminder', true);

        // Currency
        // Supported currencies
        $this->migrator->add('company.currencies', [
            ['code' => 'USD', 'symbol' => '$'],
            ['code' => 'TZS', 'symbol' => 'TSh'],
        ]);

        // Base currency (used for calculations & reports)
        $this->migrator->add('company.currency_base', 'USD');

        // Default currency (UI fallback when no client selected)
        $this->migrator->add('company.currency_default', 'USD');

        // Exchange rates (against base currency)
        $this->migrator->add('company.currency_rates', [
            'USD' => 1,
            'TZS' => 2600,
        ]);

        // Estimation Settings
        $this->migrator->add('company.estimate_prefix', 'EST');
        $this->migrator->add('company.estimate_footer', 'Thank you for considering us!');
        $this->migrator->add('company.estimate_note', null);
        $this->migrator->add('company.estimate_terms', null);
        $this->migrator->add('company.estimate_due_days', 15);
        $this->migrator->add('company.estimate_convert_to_invoice', true);
        $this->migrator->add('company.estimate_send_reminder', true);

        // Logos
        $this->migrator->add('company.dark_logo', null);
        $this->migrator->add('company.white_logo', null);
        $this->migrator->add('company.favicon', null);

        // Bank Details
        $this->migrator->add('company.bank_name', null);
        $this->migrator->add('company.bank_account_name', null);
        $this->migrator->add('company.bank_account_number', null);
        $this->migrator->add('company.bank_branch', null);
        $this->migrator->add('company.bank_swift', null);

        // Taxes
        $this->migrator->add('company.taxes', [
            ['name' => 'VAT 18%', 'rate' => 18],
            ['name' => 'VAT 20%', 'rate' => 20],
        ]);

        // default discount
        $this->migrator->add('company.discounts', [
            ['name' => 'Discount Rate', 'type' => 'rate', 'value' => 0.00],
            ['name' => 'Discount Value', 'type' => 'value', 'value' => 0.00],
        ]);
    }
};
