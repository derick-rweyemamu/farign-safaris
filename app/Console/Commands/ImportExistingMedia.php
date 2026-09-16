<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\MediaLibraryHolder;

#[Signature('media:import-existing')]
#[Description('Import existing uploaded files from storage/app/public into the Media Library without removing originals.')]
class ImportExistingMedia extends Command
{
    public function handle()
    {
        $this->info('Scanning storage/app/public for existing uploaded files...');

        $baseDir = storage_path('app/public');
        if (!File::exists($baseDir)) {
            $this->error("Directory {$baseDir} does not exist.");
            return 1;
        }

        $allFiles = File::allFiles($baseDir);
        $holder = MediaLibraryHolder::singleton();

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($allFiles as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePath());
            $filename = $file->getFilename();

            // Skip hidden files or gitignore
            if (str_starts_with($filename, '.')) {
                $skippedCount++;
                continue;
            }

            // Skip Spatie numeric folders (e.g. storage/app/public/1, storage/app/public/2, etc.)
            // Or livewire-tmp folder
            $firstSegment = explode('/', $relativePath)[0] ?? '';
            if (is_numeric($firstSegment) || $firstSegment === 'livewire-tmp') {
                $skippedCount++;
                continue;
            }

            // Check if already imported (by file name and size)
            $exists = Media::where('file_name', $filename)
                ->where('size', $file->getSize())
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            try {
                $holder->addMedia($file->getPathname())
                    ->preservingOriginal()
                    ->toMediaCollection('default');

                $importedCount++;
                $this->output->write('.');
            } catch (\Exception $e) {
                $this->warn("Failed to import {$filename}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Import complete! Imported: {$importedCount} file(s). Skipped (already imported or internal): {$skippedCount} file(s).");

        return 0;
    }
}
