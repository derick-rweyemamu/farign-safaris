<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RepairBladeViewsCommand extends Command
{
    protected $signature = 'view:repair';
    protected $description = 'Repair syntax errors caused by automatic translation wrapping in Blade view files';

    public function handle()
    {
        $targetDir = resource_path('views');
        $files = File::allFiles($targetDir);
        $totalFixed = 0;
        $totalFilesFixed = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());
            $originalContent = $content;

            // 1. Fix ->{{ __('...') }} and ?->{{ __('...') }} and =>{{ __('...') }} and ::{{ __('...') }}
            $content = preg_replace_callback('/(\?->|->|=>|::)\s*\{\{\s*__\([\'"](.*?)[\'"]\)\s*\}\}/s', function ($m) use (&$totalFixed) {
                $totalFixed++;
                $operator = $m[1];
                $inner = stripslashes($m[2]);
                return $operator . $inner;
            }, $content);

            // 2. Fix cases where HTML comments got pulled inside {{ __('...') }}
            $content = preg_replace_callback('/\{\{\s*__\([\'"](.*?)[\'"]\)\s*\}\}(\s*<!--)/s', function ($m) use (&$totalFixed) {
                $inner = stripslashes($m[1]);
                // If $inner looks like PHP syntax (e.g. ends with ) or has function calls)
                if (str_contains($inner, ')') || str_contains($inner, '$') || str_contains($inner, '(')) {
                    $totalFixed++;
                    return $inner . "\n        " . $m[2];
                }
                return $m[0];
            }, $content);

            // 3. Fix any line starting with @if, @elseif, @can, @foreach, @forelse, @unless, @while that contains {{ __('...') }}
            $lines = explode("\n", $content);
            $modifiedLines = false;
            foreach ($lines as $i => $line) {
                if (preg_match('/^\s*@(if|elseif|can|foreach|forelse|unless|while)\b/', $line)) {
                    if (str_contains($line, "{{ __('") || str_contains($line, '{{ __("')) {
                        $lines[$i] = preg_replace_callback('/\{\{\s*__\([\'"](.*?)[\'"]\)\s*\}\}/', function ($m) use (&$totalFixed) {
                            $totalFixed++;
                            return stripslashes($m[1]);
                        }, $line);
                        $modifiedLines = true;
                    }
                }
            }

            if ($modifiedLines) {
                $content = implode("\n", $lines);
            }

            if ($content !== $originalContent) {
                File::put($file->getPathname(), $content);
                $totalFilesFixed++;
            }
        }

        $this->info("Repair complete: fixed {$totalFixed} syntax occurrences across {$totalFilesFixed} files.");

        // Check compilation of all files
        $compiler = app('blade.compiler');
        $errorFiles = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            try {
                $compiler->compileString(File::get($file->getPathname()));
            } catch (\Throwable $e) {
                $errorFiles++;
                $this->error("Blade compile error in: " . $file->getRelativePathname() . " -> " . $e->getMessage());
            }
        }

        if ($errorFiles === 0) {
            $this->info("SUCCESS: All Blade view files compiled with 0 syntax errors!");
        } else {
            $this->warn("Found {$errorFiles} files with remaining compilation warnings.");
        }

        return 0;
    }
}
