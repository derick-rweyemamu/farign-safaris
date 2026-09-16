<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ActivityLog;
use App\Settings\EmailSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;

#[Layout('admin::layouts.app')]
#[Title('Email Settings')]
class EmailSettings extends Component
{
    use AuthorizesRequests;

    public string $activeTab = 'smtp';

    public string $mail_transport = '';
    public string $mail_host = '';
    public int $mail_port = 587;
    public string $mail_username = '';
    public string $mail_password = '';
    public string $mail_encryption = '';
    public string $mail_from_address = '';
    public string $mail_from_name = '';

    public string $estimate_subject = '';
    public string $estimate_body = '';
    public string $invoice_subject = '';
    public string $invoice_body = '';
    public string $itinerary_subject = '';
    public string $itinerary_body = '';

    public function mount(EmailSetting $settings): void
    {
        $this->authorize('view company settings');

        $this->mail_transport = $settings->mail_transport;
        $this->mail_host = $settings->mail_host;
        $this->mail_port = $settings->mail_port;
        $this->mail_username = $settings->mail_username;
        $this->mail_password = $settings->mail_password;
        $this->mail_encryption = $settings->mail_encryption;
        $this->mail_from_address = $settings->mail_from_address;
        $this->mail_from_name = $settings->mail_from_name;

        $this->estimate_subject = $settings->estimate_subject;
        $this->estimate_body = $settings->estimate_body;
        $this->invoice_subject = $settings->invoice_subject;
        $this->invoice_body = $settings->invoice_body;
        $this->itinerary_subject = $settings->itinerary_subject;
        $this->itinerary_body = $settings->itinerary_body;
    }

    public function save(EmailSetting $settings): void
    {
        $this->authorize('edit general settings'); // Using existing permission or could create new one

        $this->validate([
            'mail_transport' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'estimate_subject' => 'required|string',
            'estimate_body' => 'required|string',
            'invoice_subject' => 'required|string',
            'invoice_body' => 'required|string',
            'itinerary_subject' => 'required|string',
            'itinerary_body' => 'required|string',
        ]);

        $settings->mail_transport = $this->mail_transport;
        $settings->mail_host = $this->mail_host;
        $settings->mail_port = $this->mail_port;
        $settings->mail_username = $this->mail_username;
        $settings->mail_password = $this->mail_password;
        $settings->mail_encryption = $this->mail_encryption;
        $settings->mail_from_address = $this->mail_from_address;
        $settings->mail_from_name = $this->mail_from_name;

        $settings->estimate_subject = $this->estimate_subject;
        $settings->estimate_body = $this->estimate_body;
        $settings->invoice_subject = $this->invoice_subject;
        $settings->invoice_body = $this->invoice_body;
        $settings->itinerary_subject = $this->itinerary_subject;
        $settings->itinerary_body = $this->itinerary_body;

        $settings->save();

        // Only sync with .env if we are on the SMTP tab
        if ($this->activeTab === 'smtp') {
            ActivityLog::record('settings.email', "Email configuration updated in database");
        } else {
            ActivityLog::record('settings.email', "Email templates updated");
        }

        $this->dispatch('notify', message: __('Email settings updated successfully!'), type: 'success');
    }

    public function testConnection()
    {
        $this->authorize('edit general settings');

        $this->validate([
            'mail_transport' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
        ]);

        try {
            // Temporarily override configuration for the test
            config([
                'mail.default' => $this->mail_transport,
                'mail.mailers.smtp.host' => $this->mail_host,
                'mail.mailers.smtp.port' => $this->mail_port,
                'mail.mailers.smtp.encryption' => $this->mail_encryption,
                'mail.mailers.smtp.username' => $this->mail_username,
                'mail.mailers.smtp.password' => $this->mail_password,
                'mail.from.address' => $this->mail_from_address,
                'mail.from.name' => $this->mail_from_name,
            ]);

            Mail::raw("This is a test email from " . config('app.name') . " to verify your SMTP settings.", function ($message) {
                $message->to($this->mail_from_address)
                    ->subject('SMTP Test Connection');
            });

            $this->dispatch('notify', message: __('Test email sent successfully! Please check') . $this->mail_from_address, type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __('Connection Failed:') . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.email-settings');
    }
}
