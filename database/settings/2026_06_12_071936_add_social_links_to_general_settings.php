<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.facebook_url', '');
        $this->migrator->add('company.instagram_url', '');
        $this->migrator->add('company.youtube_url', '');
        $this->migrator->add('company.x_url', '');
    }
};
