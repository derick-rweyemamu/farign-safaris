<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Company.tripadvisor_url', 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html');
        $this->migrator->add('Company.google_reviews_url', 'https://www.google.com/search?q=Fargin+Safaris+reviews');
        $this->migrator->add('Company.google_place_id', null);
        $this->migrator->add('Company.google_places_api_key', null);
        $this->migrator->add('Company.safaribookings_url', 'https://www.safaribookings.com');
    }
};
