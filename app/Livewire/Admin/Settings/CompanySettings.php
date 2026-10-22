<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ActivityLog;
use App\Models\Currency;
use App\Settings\GeneralSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Company Settings')]
class CompanySettings extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public string $activeTab = 'general';

    // General & Logos
    public string $app_name = '';
    public string $company_name = '';
    public string $address = '';
    public string $email = '';
    public string $phone = '';
    public ?string $website = '';
    public ?string $tin = '';
    public ?string $vrn = '';
    public $new_company_logo = null;
    public $new_dark_logo = null;
    public $new_white_logo = null;
    public $new_favicon = null;
    public $new_tour_hero_image = null;
    public $new_tour_fallback_image = null;
    public $new_article_hero_image = null;
    public $new_proposal_heading_bg_image = null;
    public $new_auth_image = null;

    public ?string $company_logo = null;
    public ?string $dark_logo = null;
    public ?string $white_logo = null;
    public ?string $favicon = null;
    public ?string $tour_hero_image = null;
    public ?string $tour_fallback_image = null;
    public ?string $article_hero_image = null;
    public ?string $proposal_heading_bg_image = null;
    public ?string $auth_image = null;

    // Invoice Settings
    public string $invoice_prefix = '';
    public ?string $invoice_footer = '';
    public ?string $invoice_note = '';
    public ?string $invoice_terms = '';
    public int $invoice_due_days = 30;
    public bool $invoice_send_reminder = false;

    // Estimate Settings
    public string $estimate_prefix = '';
    public ?string $estimate_footer = '';
    public ?string $estimate_note = '';
    public ?string $estimate_terms = '';
    public int $estimate_due_days = 15;
    public bool $estimate_convert_to_invoice = false;
    public bool $estimate_send_reminder = false;

    // Bank Details
    public ?string $bank_name = '';
    public ?string $bank_account_name = '';
    public ?string $bank_account_number = '';
    public ?string $bank_branch = '';
    public ?string $bank_swift = '';

    // Financial (Currencies, Taxes, Discounts)
    public string $currency_base = '';
    public string $currency_default = '';

    public array $taxes = [];
    public array $discounts = [];

    // Social Links
    public ?string $facebook_url = '';
    public ?string $instagram_url = '';
    public ?string $youtube_url = '';
    public ?string $x_url = '';
    public ?string $tiktok_url = '';
    public ?string $whatsapp_number = '';

    // Review Platforms
    public ?string $tripadvisor_url = '';
    public ?string $google_reviews_url = '';
    public ?string $google_place_id = '';
    public ?string $google_places_api_key = '';
    public ?string $safaribookings_url = '';

    // Security Settings
    public bool $force_2fa = false;

    // Meta Lead Ads Integration
    public ?string $meta_app_id = '';
    public ?string $meta_app_secret = '';
    public ?string $meta_page_access_token = '';
    public ?string $meta_webhook_verify_token = '';

    public function mount(GeneralSetting $settings): void
    {
        $this->authorize('view company settings');
        // App & Company Info
        $this->app_name = $settings->app_name;
        $this->company_name = $settings->company_name;
        $this->company_logo = $settings->company_logo;
        $this->address = $settings->address;
        $this->email = $settings->email;
        $this->phone = $settings->phone;
        $this->website = $settings->website;
        $this->tin = $settings->tin;
        $this->vrn = $settings->vrn;


        // Logos
        $this->dark_logo = $settings->dark_logo;
        $this->white_logo = $settings->white_logo;
        $this->favicon = $settings->favicon;
        $this->tour_hero_image = $settings->tour_hero_image;
        $this->tour_fallback_image = $settings->tour_fallback_image;
        $this->article_hero_image = $settings->article_hero_image;
        $this->proposal_heading_bg_image = $settings->proposal_heading_bg_image;
        $this->auth_image = $settings->auth_image;

        // Invoice Settings
        $this->invoice_prefix = $settings->invoice_prefix;
        $this->invoice_footer = $settings->invoice_footer;
        $this->invoice_note = $settings->invoice_note;
        $this->invoice_terms = $settings->invoice_terms;
        $this->invoice_due_days = $settings->invoice_due_days;
        $this->invoice_send_reminder = $settings->invoice_send_reminder;

        // Estimate Settings
        $this->estimate_prefix = $settings->estimate_prefix;
        $this->estimate_footer = $settings->estimate_footer;
        $this->estimate_note = $settings->estimate_note;
        $this->estimate_terms = $settings->estimate_terms;
        $this->estimate_due_days = $settings->estimate_due_days;
        $this->estimate_convert_to_invoice = $settings->estimate_convert_to_invoice;
        $this->estimate_send_reminder = $settings->estimate_send_reminder;

        // Bank Details
        $this->bank_name = $settings->bank_name;
        $this->bank_account_name = $settings->bank_account_name;
        $this->bank_account_number = $settings->bank_account_number;
        $this->bank_branch = $settings->bank_branch;
        $this->bank_swift = $settings->bank_swift;

        // Financial
        $this->currency_base = $settings->currency_base;
        $this->currency_default = $settings->currency_default;

        $this->taxes = $settings->taxes;
        $this->discounts = $settings->discounts;

        // Social Links
        $this->facebook_url = $settings->facebook_url;
        $this->instagram_url = $settings->instagram_url;
        $this->youtube_url = $settings->youtube_url;
        $this->x_url = $settings->x_url;
        $this->tiktok_url = $settings->tiktok_url;
        $this->whatsapp_number = $settings->whatsapp_number;

        // Review Platforms
        $this->tripadvisor_url = $settings->tripadvisor_url;
        $this->google_reviews_url = $settings->google_reviews_url;
        $this->google_place_id = $settings->google_place_id;
        $this->google_places_api_key = $settings->google_places_api_key;
        $this->safaribookings_url = $settings->safaribookings_url;

        // Security Settings
        $this->force_2fa = $settings->force_2fa;

        // Meta Lead Ads
        $this->meta_app_id = $settings->meta_app_id;
        $this->meta_app_secret = $settings->meta_app_secret;
        $this->meta_page_access_token = $settings->meta_page_access_token;
        $this->meta_webhook_verify_token = $settings->meta_webhook_verify_token;
    }

    public function saveGeneral(GeneralSetting $settings): void
    {
        $this->authorize('edit general settings');

        if ($this->website && !preg_match("~^(?:f|ht)tps?://~i", $this->website)) {
            $this->website = "https://" . $this->website;
        }

        $this->validate([
            'app_name' => 'required|string|max:100',
            'company_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'tin' => 'nullable|string|max:50',
            'vrn' => 'nullable|string|max:50',
            'new_company_logo' => 'nullable|image|mimes:png,webp|max:600',
            'new_dark_logo' => 'nullable|image|mimes:png,webp|max:600',
            'new_white_logo' => 'nullable|image|mimes:png,webp|max:600',
            'new_favicon' => 'nullable|image|mimes:png,webp|max:600',
            'new_tour_hero_image' => 'nullable|image|mimes:png,webp|max:600',
            'new_tour_fallback_image' => 'nullable|image|mimes:png,webp|max:600',
            'new_article_hero_image' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:2048',
            'new_proposal_heading_bg_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'new_auth_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        $settings->app_name = $this->app_name;
        $settings->company_name = $this->company_name;
        $settings->address = $this->address;
        $settings->email = $this->email;
        $settings->phone = $this->phone;
        $settings->website = $this->website;
        $settings->tin = $this->tin;
        $settings->vrn = $this->vrn;

        if ($this->new_company_logo) {
            $settings->company_logo = $this->new_company_logo->store('settings', 'public');
            $this->company_logo = $settings->company_logo;
        }
        if ($this->new_dark_logo) {
            $settings->dark_logo = $this->new_dark_logo->store('settings', 'public');
            $this->dark_logo = $settings->dark_logo;
        }
        if ($this->new_white_logo) {
            $settings->white_logo = $this->new_white_logo->store('settings', 'public');
            $this->white_logo = $settings->white_logo;
        }
        if ($this->new_favicon) {
            $settings->favicon = $this->new_favicon->store('settings', 'public');
            $this->favicon = $settings->favicon;
        }
        if ($this->new_tour_hero_image) {
            $settings->tour_hero_image = $this->new_tour_hero_image->store('settings', 'public');
            $this->tour_hero_image = $settings->tour_hero_image;
        }
        if ($this->new_tour_fallback_image) {
            $settings->tour_fallback_image = $this->new_tour_fallback_image->store('settings', 'public');
            $this->tour_fallback_image = $settings->tour_fallback_image;
        }
        if ($this->new_article_hero_image) {
            $settings->article_hero_image = $this->new_article_hero_image->store('settings', 'public');
            $this->article_hero_image = $settings->article_hero_image;
        }
        if ($this->new_proposal_heading_bg_image) {
            $settings->proposal_heading_bg_image = $this->new_proposal_heading_bg_image->store('settings', 'public');
            $this->proposal_heading_bg_image = $settings->proposal_heading_bg_image;
        }
        if ($this->new_auth_image) {
            $settings->auth_image = $this->new_auth_image->store('settings', 'public');
            $this->auth_image = $settings->auth_image;
        }

        $settings->save();
        $this->reset(['new_company_logo', 'new_dark_logo', 'new_white_logo', 'new_favicon', 'new_tour_hero_image', 'new_tour_fallback_image', 'new_article_hero_image', 'new_proposal_heading_bg_image', 'new_auth_image']);

        ActivityLog::record('settings.general', "General company settings updated");
        $this->dispatch('notify', message: __('General settings updated successfully!'), type: 'success');
    }

    public function saveInvoice(GeneralSetting $settings): void
    {
        $this->authorize('edit invoices settings');
        $this->validate([
            'invoice_prefix' => 'required|string|max:20',
            'invoice_due_days' => 'required|integer|min:0',
            'invoice_footer' => 'nullable|string',
            'invoice_note' => 'nullable|string',
            'invoice_terms' => 'nullable|string',
            'invoice_send_reminder' => 'boolean',
        ]);

        $settings->invoice_prefix = $this->invoice_prefix;
        $settings->invoice_due_days = $this->invoice_due_days;
        $settings->invoice_footer = $this->invoice_footer;
        $settings->invoice_note = $this->invoice_note;
        $settings->invoice_terms = $this->invoice_terms;
        $settings->invoice_send_reminder = $this->invoice_send_reminder;

        $settings->save();

        ActivityLog::record('settings.invoice', "Invoice settings updated");
        $this->dispatch('notify', message: __('Invoice settings updated successfully!'), type: 'success');
    }

    public function saveEstimate(GeneralSetting $settings): void
    {
        $this->authorize('edit estimates settings');
        $this->validate([
            'estimate_prefix' => 'required|string|max:20',
            'estimate_due_days' => 'required|integer|min:0',
            'estimate_footer' => 'nullable|string',
            'estimate_note' => 'nullable|string',
            'estimate_terms' => 'nullable|string',
            'estimate_convert_to_invoice' => 'boolean',
            'estimate_send_reminder' => 'boolean',
        ]);

        $settings->estimate_prefix = $this->estimate_prefix;
        $settings->estimate_due_days = $this->estimate_due_days;
        $settings->estimate_footer = $this->estimate_footer;
        $settings->estimate_note = $this->estimate_note;
        $settings->estimate_terms = $this->estimate_terms;
        $settings->estimate_convert_to_invoice = $this->estimate_convert_to_invoice;
        $settings->estimate_send_reminder = $this->estimate_send_reminder;

        $settings->save();

        ActivityLog::record('settings.estimate', "Estimate settings updated");
        $this->dispatch('notify', message: __('Estimate settings updated successfully!'), type: 'success');
    }

    public function saveBank(GeneralSetting $settings): void
    {
        $this->authorize('edit bank details');
        $this->validate([
            'bank_name' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_branch' => 'nullable|string|max:100',
            'bank_swift' => 'nullable|string|max:50',
        ]);

        $settings->bank_name = $this->bank_name;
        $settings->bank_account_name = $this->bank_account_name;
        $settings->bank_account_number = $this->bank_account_number;
        $settings->bank_branch = $this->bank_branch;
        $settings->bank_swift = $this->bank_swift;

        $settings->save();

        ActivityLog::record('settings.bank', "Bank details updated");
        $this->dispatch('notify', message: __('Bank details updated successfully!'), type: 'success');
    }

    // Taxes
    public function addTax(): void
    {
        $this->taxes[] = ['name' => '', 'rate' => 0];
    }

    public function removeTax(int $index): void
    {
        unset($this->taxes[$index]);
        $this->taxes = array_values($this->taxes);
    }

    // Discounts
    public function addDiscount(): void
    {
        $this->discounts[] = ['name' => '', 'type' => 'rate', 'value' => 0];
    }

    public function removeDiscount(int $index): void
    {
        unset($this->discounts[$index]);
        $this->discounts = array_values($this->discounts);
    }

    // Currencies are now managed in the dedicated Currency Management module

    public function saveFinancial(GeneralSetting $settings): void
    {
        $this->validate([
            'currency_base' => 'required|string|max:10',
            'currency_default' => 'required|string|max:10',
            'taxes.*.name' => 'required|string|max:100',
            'taxes.*.rate' => 'required|numeric|min:0',
            'discounts.*.name' => 'required|string|max:100',
            'discounts.*.type' => 'required|in:rate,value',
            'discounts.*.value' => 'required|numeric|min:0',
        ]);

        $settings->currency_base = $this->currency_base;
        $settings->currency_default = $this->currency_default;

        $settings->taxes = $this->taxes;
        $settings->discounts = $this->discounts;

        $settings->save();

        ActivityLog::record('settings.financial', "Financial settings updated");
        $this->dispatch('notify', message: __('Financial settings updated successfully!'), type: 'success');
    }

    public function saveSecurity(GeneralSetting $settings): void
    {
        $this->validate([
            'force_2fa' => 'boolean',
        ]);

        $settings->force_2fa = $this->force_2fa;
        $settings->save();

        ActivityLog::record('settings.security', "Security settings updated");
        $this->dispatch('notify', message: __('Security settings updated successfully!'), type: 'success');
    }

    public function saveSocial(GeneralSetting $settings): void
    {
        $this->authorize('edit general settings');

        $urlFields = ['facebook_url', 'instagram_url', 'youtube_url', 'x_url', 'tiktok_url'];
        foreach ($urlFields as $field) {
            if ($this->$field && !preg_match("~^(?:f|ht)tps?://~i", $this->$field)) {
                $this->$field = "https://" . $this->$field;
            }
        }

        $this->validate([
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'x_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:50',
        ]);

        $settings->facebook_url = $this->facebook_url;
        $settings->instagram_url = $this->instagram_url;
        $settings->youtube_url = $this->youtube_url;
        $settings->x_url = $this->x_url;
        $settings->tiktok_url = $this->tiktok_url;
        $settings->whatsapp_number = $this->whatsapp_number;

        $settings->save();

        ActivityLog::record('settings.social', "Social links updated");
        $this->dispatch('notify', message: __('Social links updated successfully!'), type: 'success');
    }

    public function saveMetaLeads(GeneralSetting $settings): void
    {
        $this->authorize('edit general settings');

        $this->validate([
            'meta_app_id' => 'nullable|string|max:100',
            'meta_app_secret' => 'nullable|string|max:255',
            'meta_page_access_token' => 'nullable|string',
            'meta_webhook_verify_token' => 'nullable|string|max:255',
        ]);

        $settings->meta_app_id = $this->meta_app_id;
        $settings->meta_app_secret = $this->meta_app_secret;
        $settings->meta_page_access_token = $this->meta_page_access_token;
        $settings->meta_webhook_verify_token = $this->meta_webhook_verify_token;

        $settings->save();

        ActivityLog::record('settings.meta_leads', "Meta Lead Ads settings updated");
        $this->dispatch('notify', message: __('Meta Lead Ads settings updated successfully!'), type: 'success');
    }

    public function saveReviewLinks(GeneralSetting $settings): void
    {
        $this->authorize('edit general settings');

        $this->validate([
            'tripadvisor_url' => 'nullable|url|max:255',
            'google_reviews_url' => 'nullable|string|max:500',
            'google_place_id' => 'nullable|string|max:100',
            'google_places_api_key' => 'nullable|string|max:255',
            'safaribookings_url' => 'nullable|url|max:255',
        ]);

        $settings->tripadvisor_url = $this->tripadvisor_url;
        $settings->google_reviews_url = $this->google_reviews_url;
        $settings->google_place_id = $this->google_place_id;
        $settings->google_places_api_key = $this->google_places_api_key;
        $settings->safaribookings_url = $this->safaribookings_url;

        $settings->save();

        ActivityLog::record('settings.reviews', "Review platform links updated");
        $this->dispatch('notify', message: __('Review platform links updated successfully!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view company settings');
        return view('livewire.admin.settings.company-settings');
    }
}
