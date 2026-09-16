<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\NavigationMenu;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('translator', function ($translator, $app) {
            return new \App\Services\DatabaseTranslator(
                $translator->getLoader(),
                $translator->getLocale()
            );
        });
    }

    public function boot(): void
    {
        // Override Livewire's update endpoint URL to avoid WAF/firewall blocks on shared hosting.
        // The default URL contains a hex hash (e.g. /livewire-60cc3eaf/update) which triggers
        // ModSecurity/Imunify360/WAF rules. Using a clean URL fixes the 403 Forbidden error.
        // The DecodeLivewirePayload middleware decodes base64-encoded payloads from the client
        // to bypass WAF body inspection.
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/app/data/update', $handle)
                ->middleware(['web', \App\Http\Middleware\DecodeLivewirePayload::class]);
        });

        // Use a URL without a .js extension so Apache/Nginx don't treat it as a static file and return 404
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/app/livewire-assets', $handle);
        });

        // Register Google Translate Blade Directives
        Blade::directive('autoTranslate', function ($expression) {
            return "<?php echo auto_translate({$expression}); ?>";
        });

        Blade::directive('translateHtml', function ($expression) {
            return "<?php echo translate_html({$expression}); ?>";
        });

        // Register Theme View Namespaces
        View::addNamespace('admin', resource_path('themes/admin/views'));
        View::addNamespace('frontend', resource_path('themes/frontend/views'));

        // Force HTTPS on the live server
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // Load Dynamic Email Settings
        try {
            if (config('app.env') !== 'testing') {
                $emailSettings = app(\App\Settings\EmailSetting::class);

                if (!empty($emailSettings->mail_host) && !empty($emailSettings->mail_from_address)) {
                    config([
                        'mail.default' => $emailSettings->mail_transport ?: config('mail.default'),
                        'mail.mailers.smtp.host' => $emailSettings->mail_host,
                        'mail.mailers.smtp.port' => $emailSettings->mail_port,
                        'mail.mailers.smtp.encryption' => $emailSettings->mail_encryption ?: null,
                        'mail.mailers.smtp.username' => $emailSettings->mail_username,
                        'mail.mailers.smtp.password' => $emailSettings->mail_password,
                        'mail.from.address' => $emailSettings->mail_from_address,
                        'mail.from.name' => $emailSettings->mail_from_name ?: config('app.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Prevent crash during migration or if DB is not available
        }

        // ── Frontend View Composer ─────────────────────────────────────────
        // Share navigation menus and company settings to all views that begin
        // with "frontend." or "frontend::" or use the frontend layout.
        View::composer(['layouts.frontend', 'frontend.*', 'frontend::layouts.frontend', 'frontend::*'], function ($view) {
            try {
                $headerMenu = NavigationMenu::forLocation('header')->whereNull('parent_id')->with('children')->get();
                $footerCompany = NavigationMenu::forLocation('footer_company')->whereNull('parent_id')->with('children')->get();
                $footerDestinations = NavigationMenu::forLocation('footer_destinations')->whereNull('parent_id')->with('children')->get();
                $footerLegal = NavigationMenu::forLocation('footer_legal')->whereNull('parent_id')->with('children')->get();

                $companySetting = app(\App\Settings\GeneralSetting::class);

                $view->with(compact(
                    'headerMenu',
                    'footerCompany',
                    'footerDestinations',
                    'footerLegal',
                    'companySetting'
                ));
            } catch (\Exception $e) {
                // Fallback: provide empty collections so views don't crash
                $view->with([
                    'headerMenu' => collect(),
                    'footerCompany' => collect(),
                    'footerDestinations' => collect(),
                    'footerLegal' => collect(),
                    'companySetting' => null,
                ]);
            }
        });
    }
}

