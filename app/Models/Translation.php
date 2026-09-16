<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'lang',
        'lang_key',
        'lang_value',
    ];

    public static function flushCache($lang = null): void
    {
        if ($lang) {
            \Illuminate\Support\Facades\Cache::forget("translations-{$lang}");
        } else {
            foreach (['en', 'es', 'fr', 'de', 'it', 'zh'] as $locale) {
                \Illuminate\Support\Facades\Cache::forget("translations-{$locale}");
            }
        }
    }

    public static function syncMissingKeys(): void
    {
        $enKeys = self::where('lang', 'en')->pluck('lang_value', 'lang_key');
        $locales = ['es', 'de', 'fr', 'it', 'zh'];

        foreach ($locales as $locale) {
            $existing = self::where('lang', $locale)->pluck('lang_key')->map(fn($k) => strtolower((string)$k))->toArray();
            $existingMap = array_flip($existing);
            $toInsert = [];
            $now = now();

            foreach ($enKeys as $key => $val) {
                $keyLower = strtolower((string)$key);
                if (!isset($existingMap[$keyLower])) {
                    $toInsert[] = [
                        'lang' => $locale,
                        'lang_key' => $key,
                        'lang_value' => $val,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                if (count($toInsert) >= 500) {
                    self::insert($toInsert);
                    $toInsert = [];
                }
            }

            if (!empty($toInsert)) {
                self::insert($toInsert);
            }
        }

        self::flushCache();
    }
}
