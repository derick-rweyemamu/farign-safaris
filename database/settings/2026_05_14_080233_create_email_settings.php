<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('Email.mail_transport', 'smtp');
        $this->migrator->add('Email.mail_host', 'smtp.mailtrap.io');
        $this->migrator->add('Email.mail_port', 2525);
        $this->migrator->add('Email.mail_username', '');
        $this->migrator->add('Email.mail_password', '');
        $this->migrator->add('Email.mail_encryption', 'tls');
        $this->migrator->add('Email.mail_from_address', 'hello@example.com');
        $this->migrator->add('Email.mail_from_name', 'Example App');
    }
};
