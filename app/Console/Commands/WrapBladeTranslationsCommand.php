<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WrapBladeTranslationsCommand extends Command
{
    protected $signature = 'view:wrap-translations {--dir= : Specific sub-directory in resources/views to scan}';
    protected $description = 'Scan Blade view files and automatically wrap hardcoded English text in {{ __(\'...\') }}';

    public function handle()
    {
        $dirParam = $this->option('dir');
        if ($dirParam) {
            $targetDir = File::isDirectory(resource_path("views/{$dirParam}")) 
                ? resource_path("views/{$dirParam}") 
                : resource_path($dirParam);
        } else {
            $targetDir = resource_path('views');
        }

        if (!File::isDirectory($targetDir)) {
            $this->error("Directory {$targetDir} does not exist.");
            return 1;
        }

        $this->info("Scanning Blade files in: {$targetDir}...");

        $files = File::allFiles($targetDir);
        $totalFiles = 0;
        $totalWrapped = 0;
        $discoveredKeys = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Exclude vendor views if any
            if (str_contains($file->getPathname(), 'vendor')) {
                continue;
            }

            $content = File::get($file->getPathname());
            $originalContent = $content;

            // 1. Protect script, style, php blocks, and existing Blade expressions
            $protectedBlocks = [];
            $placeholderIndex = 0;

            // Protect <script>...</script>
            $content = preg_replace_callback('/<script\b[^>]*>.*?<\/script>/is', function ($m) use (&$protectedBlocks, &$placeholderIndex) {
                $token = "___PROTECTED_SCRIPT_" . ($placeholderIndex++) . "___";
                $protectedBlocks[$token] = $m[0];
                return $token;
            }, $content);

            // Protect <style>...</style>
            $content = preg_replace_callback('/<style\b[^>]*>.*?<\/style>/is', function ($m) use (&$protectedBlocks, &$placeholderIndex) {
                $token = "___PROTECTED_STYLE_" . ($placeholderIndex++) . "___";
                $protectedBlocks[$token] = $m[0];
                return $token;
            }, $content);

            // Protect PHP blocks
            $content = preg_replace_callback('/<\\?php.*?\\?' . '>/is', function ($m) use (&$protectedBlocks, &$placeholderIndex) {
                $token = "___PROTECTED_PHP_" . ($placeholderIndex++) . "___";
                $protectedBlocks[$token] = $m[0];
                return $token;
            }, $content);

            // Protect existing {{ ... }} and {!! ... !!}
            $content = preg_replace_callback('/(\{\{.*?\}\}|\{\!\!.*?\!\!\})/s', function ($m) use (&$protectedBlocks, &$placeholderIndex) {
                $token = "___PROTECTED_BLADE_" . ($placeholderIndex++) . "___";
                $protectedBlocks[$token] = $m[0];
                return $token;
            }, $content);

            // Protect Blade directives (@if(...), @foreach(...), @endif, etc.) with balanced parentheses
            $content = preg_replace_callback('/(@[a-zA-Z]+(\s*\((?:[^()]+|(?2))*\))?)/s', function ($m) use (&$protectedBlocks, &$placeholderIndex) {
                $token = "___PROTECTED_DIRECTIVE_" . ($placeholderIndex++) . "___";
                $protectedBlocks[$token] = $m[0];
                return $token;
            }, $content);

            // 2. Wrap attributes: placeholder, title, label
            $content = preg_replace_callback('/(placeholder|title|label)=["\']([^"\']{2,150})["\']/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $attr = $m[1];
                $val = trim($m[2]);

                if ($this->shouldIgnore($val)) {
                    return $m[0];
                }

                $discoveredKeys[$val] = true;
                $totalWrapped++;
                $escapedVal = addcslashes($val, "'\\");
                return "{$attr}=\"{{ __('{$escapedVal}') }}\"";
            }, $content);

            // 3. Wrap text between tags (> or /> and <) - negative lookbehind prevents ->, ?->, =>
            $content = preg_replace_callback('/(?<![\-\?\=])(>|\/>)\s*([A-Za-z][A-Za-z0-9\s\,\.\-\?\!\:\/\(\)\'\"&;]{1,150}[A-Za-z0-9\.\?\!\:\'\"]?)\s*(<)/s', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $prefix = $m[1];
                $text = trim($m[2]);
                $suffix = $m[3];

                if ($this->shouldIgnore($text)) {
                    return $m[0];
                }

                $discoveredKeys[$text] = true;
                $totalWrapped++;
                $escapedText = addcslashes($text, "'\\");
                return "{$prefix}{{ __('{$escapedText}') }}{$suffix}";
            }, $content);

            // 4. Restore protected blocks
            foreach (array_reverse($protectedBlocks, true) as $token => $block) {
                $content = str_replace($token, $block, $content);
            }

            if ($content !== $originalContent) {
                File::put($file->getPathname(), $content);
                $totalFiles++;
            }
        }

        // Insert discovered keys into DB for admin translation
        $dbAdded = 0;
        foreach (array_keys($discoveredKeys) as $keyStr) {
            $keyStr = trim((string) $keyStr);
            if (mb_strlen($keyStr) <= 250 && !empty($keyStr)) {
                $created = Translation::firstOrCreate(
                    ['lang' => 'en', 'lang_key' => $keyStr],
                    ['lang_value' => $keyStr]
                );
                if ($created->wasRecentlyCreated) {
                    $dbAdded++;
                }
            }
        }

        Translation::flushCache();

        $this->info("Scan complete!");
        $this->info("Modified {$totalFiles} Blade files.");
        $this->info("Wrapped {$totalWrapped} text occurrences.");
        $this->info("Added {$dbAdded} new UI strings to the translations table.");

        return 0;
    }

    private function shouldIgnore(string $text): bool
    {
        $text = trim($text);

        // Ignore short text or syntax tokens
        if (mb_strlen($text) < 2) {
            return true;
        }

        // Ignore if it doesn't contain at least one word character
        if (!preg_match('/[A-Za-z]{2,}/', $text)) {
            return true;
        }

        // Ignore if parentheses are unbalanced
        $openCount = substr_count($text, '(');
        $closeCount = substr_count($text, ')');
        if ($closeCount !== $openCount) {
            return true;
        }

        // Ignore code syntax, tokens, and placeholders
        $forbidden = [
            '__(' , 'translate(', 'wire:', '->', '::', '$', '@', '{', '}', '=', '_PROTECTED_', 'http://', 'https://', '.com', '.org', 'svg', 'path', 'xmlns', 'viewBox', '()', 'auth()', 'can(', 'hasRole(', 'isEmpty('
        ];

        foreach ($forbidden as $f) {
            if (str_contains($text, $f)) {
                return true;
            }
        }

        return false;
    }
}
