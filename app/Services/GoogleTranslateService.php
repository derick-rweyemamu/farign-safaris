<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GoogleTranslateService
{
    /**
     * Translate a string or paragraph using MySQL Database Caching + Google Translate API endpoint.
     *
     * @param string|null $text
     * @param string|null $targetLocale
     * @param string $sourceLocale
     * @param bool $cacheInDb
     * @return string
     */
    public function translate(?string $text, ?string $targetLocale = null, string $sourceLocale = 'en', bool $cacheInDb = true): string
    {
        if (empty($text)) {
            return '';
        }

        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $targetLocale = $targetLocale ?: App::getLocale();

        // If target is source language, return original text
        if ($targetLocale === $sourceLocale) {
            return $text;
        }

        // Avoid querying DB if tables aren't migrated yet
        try {
            if (!Schema::hasTable('translations')) {
                return $text;
            }

            // Clean string key for cache/DB (if <= 250 chars use direct lookup, if longer use md5 key or truncate for DB key)
            $dbKey = mb_strlen($text) > 250 ? mb_substr($text, 0, 250) : $text;

            // 1. Check current locale DB translations cache
            $translationLocale = Cache::rememberForever("translations-{$targetLocale}", function () use ($targetLocale) {
                return Translation::where('lang', $targetLocale)->pluck('lang_value', 'lang_key')->toArray();
            });

            if (isset($translationLocale[$dbKey]) && $translationLocale[$dbKey] !== null && $translationLocale[$dbKey] !== '' && $translationLocale[$dbKey] !== $dbKey) {
                return trim($translationLocale[$dbKey]);
            }

            // 2. Not cached or equals English key -> Call Google Translate endpoint
            $translated = $this->callGoogleTranslateApi($text, $sourceLocale, $targetLocale);

            if ($translated && $translated !== $text) {
                if ($cacheInDb) {
                    // Ensure English default exists
                    Translation::firstOrCreate(
                        ['lang' => 'en', 'lang_key' => $dbKey],
                        ['lang_value' => $text]
                    );

                    // Save translated version to DB cache
                    Translation::updateOrCreate(
                        ['lang' => $targetLocale, 'lang_key' => $dbKey],
                        ['lang_value' => $translated]
                    );

                    Cache::forget("translations-{$targetLocale}");
                    Cache::forget('translations-en');
                }

                return $translated;
            }
        } catch (\Throwable $e) {
            Log::warning('GoogleTranslateService error: ' . $e->getMessage());
        }

        return $text;
    }

    /**
     * Translate HTML content while preserving HTML tags, attributes, and structure.
     * Only text nodes inside tags are translated.
     *
     * @param string|null $html
     * @param string|null $targetLocale
     * @param string $sourceLocale
     * @return string
     */
    public function translateHtml(?string $html, ?string $targetLocale = null, string $sourceLocale = 'en'): string
    {
        if (empty($html)) {
            return '';
        }

        $targetLocale = $targetLocale ?: App::getLocale();
        if ($targetLocale === $sourceLocale) {
            return $html;
        }

        // Check if entire HTML block is already cached in translations
        $dbKey = 'html_' . md5($html);
        try {
            if (Schema::hasTable('translations')) {
                $cachedHtml = Translation::where('lang', $targetLocale)->where('lang_key', $dbKey)->value('lang_value');
                if ($cachedHtml && $cachedHtml !== '') {
                    return $cachedHtml;
                }
            }
        } catch (\Throwable $e) {
            // Ignore DB errors
        }

        // Strip script and style tags before parsing
        $translatedHtml = $this->translateHtmlNodes($html, $sourceLocale, $targetLocale);

        // Cache the translated HTML in MySQL
        try {
            if (Schema::hasTable('translations') && $translatedHtml && $translatedHtml !== $html) {
                Translation::updateOrCreate(
                    ['lang' => 'en', 'lang_key' => $dbKey],
                    ['lang_value' => $html]
                );
                Translation::updateOrCreate(
                    ['lang' => $targetLocale, 'lang_key' => $dbKey],
                    ['lang_value' => $translatedHtml]
                );
            }
        } catch (\Throwable $e) {
            // Ignore DB errors
        }

        return $translatedHtml ?: $html;
    }

    /**
     * Call the free Google Translate single API endpoint.
     */
    protected function callGoogleTranslateApi(string $text, string $source, string $target): ?string
    {
        try {
            $url = "https://translate.googleapis.com/translate_a/single";
            $response = Http::timeout(6)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->get($url, [
                'client' => 'gtx',
                'sl' => $source,
                'tl' => $target,
                'dt' => 't',
                'q' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data[0]) && is_array($data[0])) {
                    $translated = '';
                    foreach ($data[0] as $segment) {
                        if (isset($segment[0])) {
                            $translated .= $segment[0];
                        }
                    }
                    return trim($translated) ?: null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Google Translate API call failed for [{$target}]: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Safely translate text nodes inside HTML strings.
     */
    protected function translateHtmlNodes(string $html, string $source, string $target): string
    {
        // Suppress libxml warnings for malformed HTML fragments
        $internalErrors = libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        // Wrap in a container to ensure valid UTF-8 parsing
        $wrappedHtml = '<?xml encoding="UTF-8"><div>' . $html . '</div>';
        $doc->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($doc);
        // Find all text nodes that have alphanumeric content and are not inside script/style
        $textNodes = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style) and normalize-space() != ""]');

        foreach ($textNodes as $node) {
            $text = trim($node->nodeValue);
            if (mb_strlen($text) > 1 && !is_numeric($text)) {
                $translated = $this->translate($text, $target, $source, true);
                if ($translated) {
                    $node->nodeValue = $translated;
                }
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        // Extract inner HTML of the container div
        $div = $doc->getElementsByTagName('div')->item(0);
        if (!$div) {
            return $html;
        }

        $output = '';
        foreach ($div->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }

        return $output ?: $html;
    }
}
