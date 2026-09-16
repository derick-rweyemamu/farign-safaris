<?php

namespace App\Traits;

use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Validator;

trait InteractsWithMediaUploads
{
    /**
     * Add images selected from the media library modal to an array property (like gallery).
     */
    #[On('add-gallery-from-library')]
    public function addGalleryFromLibrary($model, $paths = []): void
    {
        if (!property_exists($this, $model)) {
            return;
        }

        $current = is_array($this->{$model}) ? $this->{$model} : [];
        $merged = array_merge($current, $paths);
        $this->{$model} = array_values(array_unique($merged));
    }

    /**
     * Get the validation rule for a single image field that can be either an uploaded file or a library string path.
     */
    protected function imageRule(string $field, int $maxKb = 2048): string
    {
        $val = $this->{$field} ?? null;
        if (is_string($val)) {
            return 'nullable|string';
        }
        return "nullable|image|mimes:png,webp,jpg,jpeg|max:{$maxKb}";
    }

    /**
     * Get the validation rule for gallery items that can be either temporary uploaded files or library string paths.
     */
    protected function galleryRule(int $maxKb = 2048): array
    {
        return [
            function ($attribute, $value, $fail) use ($maxKb) {
                if (is_string($value)) {
                    return; // Already a library path or URL
                }
                if (is_object($value)) {
                    $validator = Validator::make(
                        ['file' => $value],
                        ['file' => "image|mimes:png,webp,jpg,jpeg|max:{$maxKb}"]
                    );
                    if ($validator->fails()) {
                        $fail($validator->errors()->first('file'));
                    }
                }
            }
        ];
    }

    /**
     * Store or normalize a single image field safely.
     * Works for both new TemporaryUploadedFile objects and string storage paths from Media Library.
     */
    protected function saveImageField($value, string $folder = 'uploads'): ?string
    {
        if (empty($value)) {
            return null;
        }

        // If it's a freshly uploaded file from Livewire
        if (is_object($value) && method_exists($value, 'store')) {
            return $value->store($folder, 'public');
        }

        // If it's a string path (either selected from media library or already existing)
        if (is_string($value) && trim($value) !== '') {
            $path = trim($value);

            // Strip domain if full URL was passed
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                $parsed = parse_url($path, PHP_URL_PATH);
                $path = $parsed ?: $path;
            }

            // Strip leading slash or '/storage/' prefix to get pure storage/app/public relative path
            $path = ltrim($path, '/');
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }

            return $path;
        }

        return null;
    }

    /**
     * Store or normalize multiple images (gallery array) safely.
     */
    protected function saveGalleryField($values, string $folder = 'uploads/gallery'): array
    {
        if (empty($values) || !is_array($values)) {
            return [];
        }

        $paths = [];
        foreach ($values as $val) {
            $saved = $this->saveImageField($val, $folder);
            if ($saved) {
                $paths[] = $saved;
            }
        }

        return array_values(array_unique($paths));
    }
}
