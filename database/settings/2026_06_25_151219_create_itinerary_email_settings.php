<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Email.itinerary_subject', 'Your Safari Proposal: {itinerary_name}');
        $this->migrator->add('Email.itinerary_body', "Hi {client_name},\n\nPlease find your requested safari proposal attached to this email.\n\nBest regards,\n{company_name}");
    }
};
