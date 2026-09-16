<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{

    public string $app_name;
    public string $company_name;
    public ?string $company_logo;

    public string $address;
    public string $email;
    public string $phone;
    public ?string $website;
    public ?string $tin;
    public ?string $vrn;

    public string $invoice_prefix;
    public ?string $invoice_footer;
    public ?string $invoice_note;
    public ?string $invoice_terms;
    public int $invoice_due_days;
    public bool $invoice_send_reminder;

    public array $currencies;
    public string $currency_base;
    public string $currency_default;
    public array $currency_rates;


    public string $estimate_prefix;
    public ?string $estimate_footer;
    public ?string $estimate_note;
    public ?string $estimate_terms;
    public int $estimate_due_days;
    public bool $estimate_convert_to_invoice;
    public bool $estimate_send_reminder;

    // Logos
    public ?string $dark_logo;
    public ?string $white_logo;
    public ?string $favicon;

    // Tour Display Images
    public ?string $tour_hero_image;
    public ?string $tour_fallback_image;
    public ?string $article_hero_image;

    // Proposal / PDF / Auth
    public ?string $proposal_heading_bg_image;
    public ?string $auth_image;

    // Bank Details
    public ?string $bank_name;
    public ?string $bank_account_name;
    public ?string $bank_account_number;
    public ?string $bank_branch;
    public ?string $bank_swift;

    // Social Links
    public ?string $facebook_url;
    public ?string $instagram_url;
    public ?string $youtube_url;
    public ?string $x_url;
    public ?string $tiktok_url;
    public ?string $whatsapp_number;
    public array $taxes;

    // discounts
    public array $discounts;

    // security
    public bool $force_2fa;

    // Meta Lead Ads Integration
    public ?string $meta_app_id;
    public ?string $meta_app_secret;
    public ?string $meta_page_access_token;
    public ?string $meta_webhook_verify_token;

    public static function group(): string
    {
        return 'Company';
    }
}