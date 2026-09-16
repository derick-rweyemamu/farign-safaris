<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanTranslationsCommand extends Command
{
    protected $signature = 'translations:clean {--dry-run : Only display what would be removed without deleting}';
    protected $description = 'Clean up garbage translation keys (e.g., trailing brackets, PHP syntax) from database and lang/*.json files';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info("Scanning translations table and lang/*.json files for garbage keys...");

        $allKeys = Translation::select('lang_key')->distinct()->pluck('lang_key')->toArray();

        // Also collect keys from JSON files
        $langDir = base_path('lang');
        $jsonFiles = File::isDirectory($langDir) ? File::files($langDir) : [];
        $jsonKeys = [];

        foreach ($jsonFiles as $file) {
            if ($file->getExtension() === 'json') {
                $content = File::get($file->getPathname());
                $data = json_decode($content, true);
                if (is_array($data)) {
                    $jsonKeys = array_merge($jsonKeys, array_keys($data));
                }
            }
        }

        $allUniqueKeys = array_unique(array_merge($allKeys, $jsonKeys));
        $garbageKeys = [];

        foreach ($allUniqueKeys as $key) {
            if ($this->isGarbageKey((string) $key)) {
                $garbageKeys[] = $key;
            }
        }

        if (empty($garbageKeys)) {
            $this->info("No garbage translation keys found. Everything is clean!");
            return 0;
        }

        $this->warn("Found " . count($garbageKeys) . " garbage translation keys:");
        foreach ($garbageKeys as $gKey) {
            $this->line(" - [REMOVE] {$gKey}");
        }

        if ($dryRun) {
            $this->info("Dry-run complete. No changes made.");
            return 0;
        }

        // 1. Delete from Database
        $deletedDbCount = Translation::whereIn('lang_key', $garbageKeys)->delete();
        $this->info("Deleted {$deletedDbCount} database translation records.");

        // 2. Clean lang/*.json files
        $cleanedFiles = 0;
        foreach ($jsonFiles as $file) {
            if ($file->getExtension() === 'json') {
                $content = File::get($file->getPathname());
                $data = json_decode($content, true);
                if (is_array($data)) {
                    $originalCount = count($data);
                    foreach ($garbageKeys as $gKey) {
                        unset($data[$gKey]);
                    }
                    if (count($data) !== $originalCount) {
                        File::put($file->getPathname(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $cleanedFiles++;
                        $this->info("Cleaned " . $file->getFilename() . " (removed " . ($originalCount - count($data)) . " keys)");
                    }
                }
            }
        }

        Translation::flushCache();

        $this->info("Cleanup complete! Removed garbage keys from DB and {$cleanedFiles} language files.");
        return 0;
    }

    private function isGarbageKey(string $key): bool
    {
        $key = trim($key);

        if ($key === '') {
            return true;
        }

        // 1. Unbalanced closing parentheses: more ')' than '('
        $openCount = substr_count($key, '(');
        $closeCount = substr_count($key, ')');
        if ($closeCount > $openCount) {
            return true;
        }

        // 2. Unbalanced opening parentheses: more '(' than ')'
        if ($openCount > $closeCount) {
            return true;
        }

        // 3. Ends with ')' but has no opening '(' at all (e.g., 'description)', 'email)')
        if (str_ends_with($key, ')') && $openCount === 0) {
            return true;
        }

        // 4. PHP syntax tokens / method calls in translation keys
        $forbiddenTokens = [
            '()', '->', '::', 'auth()', 'can(', 'hasRole(', 'hasPages(', 'isEmpty(', 'isNotEmpty(', '$this', '_PROTECTED_', '__('
        ];

        foreach ($forbiddenTokens as $token) {
            if (str_contains($key, $token)) {
                return true;
            }
        }

        // 5. Short lowercase single-word keys ending with ')' or '))'
        if (preg_match('/^[a-z_0-9]+\)+$/', $key)) {
            return true;
        }

        return false;
    }
}
