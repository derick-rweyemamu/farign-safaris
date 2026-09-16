<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Company.tour_hero_image', 'settings/default_tour_hero.jpg');
        $this->migrator->add('Company.tour_fallback_image', 'settings/default_tour_fallback.jpg');
    }
};
