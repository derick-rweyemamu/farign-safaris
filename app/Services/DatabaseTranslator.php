<?php

namespace App\Services;

use Illuminate\Translation\Translator;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DatabaseTranslator extends Translator
{
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // If key is an array or empty, pass to parent
        if (!is_string($key) || empty($key)) {
            return parent::get($key, $replace, $locale, $fallback);
        }

        $keyStr = trim($key);

        // Avoid querying DB if tables aren't migrated yet
        try {
            if (!Schema::hasTable('translations')) {
                return parent::get($key, $replace, $locale, $fallback);
            }

            // 1. Check current locale DB translations
            $translationLocale = Cache::rememberForever("translations-{$locale}", function () use ($locale) {
                return Translation::where('lang', $locale)->pluck('lang_value', 'lang_key')->toArray();
            });

            if (isset($translationLocale[$keyStr]) && $translationLocale[$keyStr] !== null && $translationLocale[$keyStr] !== '') {
                $line = trim($translationLocale[$keyStr]);
                return $this->makeReplacements($line, $replace);
            }

            // 2. Ensure English key exists for auto-discovery
            if (mb_strlen($keyStr) <= 250 && !str_contains($keyStr, '::') && !str_starts_with($keyStr, 'validation.') && !str_starts_with($keyStr, 'pagination.') && !str_starts_with($keyStr, 'auth.')) {
                $translationsEn = Cache::rememberForever('translations-en', function () {
                    return Translation::where('lang', 'en')->pluck('lang_value', 'lang_key')->toArray();
                });

                if (!isset($translationsEn[$keyStr])) {
                    Translation::firstOrCreate(
                        ['lang' => 'en', 'lang_key' => $keyStr],
                        ['lang_value' => $keyStr]
                    );
                    if ($locale !== 'en') {
                        Translation::firstOrCreate(
                            ['lang' => $locale, 'lang_key' => $keyStr],
                            ['lang_value' => $keyStr]
                        );
                        Cache::forget("translations-{$locale}");
                    }
                    Cache::forget('translations-en');
                }
            }
        } catch (\Throwable $e) {
            // Silently fallback to file translations if any database exception occurs
        }

        // 3. Fallback to Laravel's native file translator
        $native = parent::get($key, $replace, $locale, $fallback);
        if ($native !== $key && $native !== $keyStr) {
            return $native;
        }

        // 4. Automatic Google Translate fallback if missing in DB and JSON files
        if ($locale !== 'en' && mb_strlen($keyStr) <= 250 && !str_contains($keyStr, '::') && !str_starts_with($keyStr, 'validation.') && !str_starts_with($keyStr, 'pagination.') && !str_starts_with($keyStr, 'auth.')) {
            try {
                $translated = app(\App\Services\GoogleTranslateService::class)->translate($keyStr, $locale);
                if ($translated && $translated !== $keyStr) {
                    return $this->makeReplacements($translated, $replace);
                }
            } catch (\Throwable $e) {
                // Silently fallback if translation service fails
            }
        }

        return $native;
    }
}

