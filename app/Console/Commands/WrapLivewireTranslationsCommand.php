<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WrapLivewireTranslationsCommand extends Command
{
    protected $signature = 'livewire:wrap-translations {--dir= : Specific sub-directory in app/Livewire to scan}';
    protected $description = 'Scan Livewire PHP files and automatically wrap user-facing messages in __('."'...'".')';

    public function handle()
    {
        $dirParam = $this->option('dir');
        $targetDir = $dirParam 
            ? app_path("Livewire/{$dirParam}") 
            : app_path('Livewire');

        if (!File::isDirectory($targetDir)) {
            $this->error("Directory {$targetDir} does not exist.");
            return 1;
        }

        $this->info("Scanning Livewire PHP files in: {$targetDir}...");

        $files = File::allFiles($targetDir);
        $totalFiles = 0;
        $totalWrapped = 0;
        $discoveredKeys = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());
            $originalContent = $content;

            // 1. Ternary message: $this->isEdit ? '...' : '...'
            $content = preg_replace_callback('/message:\s*\$this->isEdit\s*\?\s*([\'"])([^\'"\\\\]{2,150})\1\s*:\s*([\'"])([^\'"\\\\]{2,150})\3/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $val1 = trim($m[2]);
                $val2 = trim($m[4]);
                $this->addKey($val1, $discoveredKeys);
                $this->addKey($val2, $discoveredKeys);
                $totalWrapped += 2;
                return "message: \$this->isEdit ? __('{$val1}') : __('{$val2}')";
            }, $content);

            // 2. Ternary 'message' => $this->isEdit ? '...' : '...'
            $content = preg_replace_callback('/[\'"]message[\'"]\s*=>\s*\$this->isEdit\s*\?\s*([\'"])([^\'"\\\\]{2,150})\1\s*:\s*([\'"])([^\'"\\\\]{2,150})\3/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $val1 = trim($m[2]);
                $val2 = trim($m[4]);
                $this->addKey($val1, $discoveredKeys);
                $this->addKey($val2, $discoveredKeys);
                $totalWrapped += 2;
                return "'message' => \$this->isEdit ? __('{$val1}') : __('{$val2}')";
            }, $content);

            // 3. Ternary session()->flash('...', $this->isEdit ? '...' : '...')
            $content = preg_replace_callback('/session\(\)->flash\(([\'"])(success|status|error|info|message|notify)\1,\s*\$this->isEdit\s*\?\s*([\'"])([^\'"\\\\]{2,150})\3\s*:\s*([\'"])([^\'"\\\\]{2,150})\5\)/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $quote = $m[1];
                $type = $m[2];
                $val1 = trim($m[4]);
                $val2 = trim($m[6]);
                $this->addKey($val1, $discoveredKeys);
                $this->addKey($val2, $discoveredKeys);
                $totalWrapped += 2;
                return "session()->flash({$quote}{$type}{$quote}, \$this->isEdit ? __('{$val1}') : __('{$val2}'))";
            }, $content);

            // 4. Regular message: '...'
            $content = preg_replace_callback('/message:\s*([\'"])([^\'"\\\\]{2,150})\1/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $val = trim($m[2]);
                if ($this->shouldIgnore($val)) {
                    return $m[0];
                }
                $this->addKey($val, $discoveredKeys);
                $totalWrapped++;
                $escaped = addcslashes($val, "'\\");
                return "message: __('{$escaped}')";
            }, $content);

            // 5. Regular 'message' => '...'
            $content = preg_replace_callback('/[\'"]message[\'"]\s*=>\s*([\'"])([^\'"\\\\]{2,150})\1/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $val = trim($m[2]);
                if ($this->shouldIgnore($val)) {
                    return $m[0];
                }
                $this->addKey($val, $discoveredKeys);
                $totalWrapped++;
                $escaped = addcslashes($val, "'\\");
                return "'message' => __('{$escaped}')";
            }, $content);

            // 6. Regular session()->flash('type', '...')
            $content = preg_replace_callback('/session\(\)->flash\(([\'"])(success|status|error|info|message|notify)\1,\s*([\'"])([^\'"\\\\]{2,150})\3\)/i', function ($m) use (&$discoveredKeys, &$totalWrapped) {
                $quote = $m[1];
                $type = $m[2];
                $val = trim($m[4]);
                if ($this->shouldIgnore($val)) {
                    return $m[0];
                }
                $this->addKey($val, $discoveredKeys);
                $totalWrapped++;
                $escaped = addcslashes($val, "'\\");
                return "session()->flash({$quote}{$type}{$quote}, __('{$escaped}'))";
            }, $content);

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
        $this->info("Modified {$totalFiles} Livewire PHP files.");
        $this->info("Wrapped {$totalWrapped} message occurrences.");
        $this->info("Added {$dbAdded} new UI strings to the translations table.");

        return 0;
    }

    private function addKey(string $text, array &$discoveredKeys): void
    {
        if (!$this->shouldIgnore($text)) {
            $discoveredKeys[$text] = true;
        }
    }

    private function shouldIgnore(string $text): bool
    {
        $text = trim($text);
        if (mb_strlen($text) < 2) {
            return true;
        }
        if (!preg_match('/[A-Za-z]{2,}/', $text)) {
            return true;
        }
        $forbidden = ['__(' , 'translate(', 'wire:', '->', '::', '$', '@', '{', '}', '=', 'http://', 'https://', '.com', '.org'];
        foreach ($forbidden as $f) {
            if (str_contains($text, $f)) {
                return true;
            }
        }
        return false;
    }
}
