<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Company.meta_app_id', null);
        $this->migrator->add('Company.meta_app_secret', null);
        $this->migrator->add('Company.meta_page_access_token', null);
        $this->migrator->add('Company.meta_webhook_verify_token', null);
    }
};
