<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportTranslationsCommand extends Command
{
    protected $signature = 'translations:import';
    protected $description = 'Import static UI strings from lang/*.json files into the translations database table';

    public function handle()
    {
        $this->info('Starting import of lang/*.json files into translations table...');

        $langDir = base_path('lang');
        if (!File::isDirectory($langDir)) {
            $this->error("Directory {$langDir} not found!");
            return 1;
        }

        $files = File::files($langDir);
        $count = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $locale = $file->getFilenameWithoutExtension();
                $this->info("Processing language file: lang/{$locale}.json...");

                $content = File::get($file->getPathname());
                $data = json_decode($content, true);

                if (!is_array($data)) {
                    continue;
                }

                foreach ($data as $key => $val) {
                    $keyStr = trim((string) $key);
                    if (mb_strlen($keyStr) > 250) {
                        $keyStr = mb_substr($keyStr, 0, 250);
                    }

                    // Also make sure 'en' reference exists
                    Translation::firstOrCreate(
                        ['lang' => 'en', 'lang_key' => $keyStr],
                        ['lang_value' => $keyStr]
                    );

                    // Insert/update the locale translation
                    Translation::updateOrCreate(
                        ['lang' => $locale, 'lang_key' => $keyStr],
                        ['lang_value' => trim((string) $val)]
                    );

                    $count++;
                }
            }
        }

        Translation::flushCache();

        $this->info("Successfully imported/updated {$count} translation records and flushed cache.");
        return 0;
    }
}
