<?php

use App\Models\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

if (!function_exists('translate')) {
    function translate($key, $lang = null)
    {
        if (empty($key)) {
            return '';
        }

        if ($lang === null) {
            $lang = App::getLocale();
        }

        // Clean string key length for safety
        $keyStr = trim((string) $key);
        if (mb_strlen($keyStr) > 250) {
            $keyStr = mb_substr($keyStr, 0, 250);
        }

        // 1. Ensure the English default is in the database for admin editing
        $translationsEn = Cache::rememberForever('translations-en', function () {
            return Translation::where('lang', 'en')->pluck('lang_value', 'lang_key')->toArray();
        });

        if (!isset($translationsEn[$keyStr])) {
            try {
                Translation::firstOrCreate(
                    ['lang' => 'en', 'lang_key' => $keyStr],
                    ['lang_value' => $keyStr]
                );
                Cache::forget('translations-en');
            } catch (\Exception $e) {
                // Silently ignore any DB race conditions
            }
        }

        // 2. Lookup in current language DB translations
        $translationLocale = Cache::rememberForever("translations-{$lang}", function () use ($lang) {
            return Translation::where('lang', $lang)->pluck('lang_value', 'lang_key')->toArray();
        });

        if (isset($translationLocale[$keyStr]) && $translationLocale[$keyStr] !== null && $translationLocale[$keyStr] !== '') {
            return trim($translationLocale[$keyStr]);
        }

        // 3. Fallback to Laravel's native __() dictionary (from lang/*.json) if available
        $native = __($keyStr, [], $lang);
        if ($native !== $keyStr) {
            return $native;
        }

        // 4. Automatic Google Translate fallback with MySQL Database caching
        if ($lang !== 'en' && $lang !== '' && $lang !== null) {
            try {
                return app(\App\Services\GoogleTranslateService::class)->translate($keyStr, $lang);
            } catch (\Throwable $e) {
                // Silently fallback to English key if translation service fails
            }
        }

        return $keyStr;
    }
}

if (!function_exists('auto_translate')) {
    /**
     * Automatically translate dynamic text or paragraph using Google Translate and cache in MySQL.
     */
    function auto_translate($text, $lang = null)
    {
        if (empty($text)) {
            return '';
        }
        return app(\App\Services\GoogleTranslateService::class)->translate($text, $lang);
    }
}

if (!function_exists('translate_html')) {
    /**
     * Automatically translate rich HTML content (e.g. GrapesJS / CMS pages) without breaking HTML tags.
     */
    function translate_html($html, $lang = null)
    {
        if (empty($html)) {
            return '';
        }
        return app(\App\Services\GoogleTranslateService::class)->translateHtml($html, $lang);
    }
}

