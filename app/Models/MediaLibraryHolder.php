<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaLibraryHolder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_library_holders';

    protected $guarded = [];

    public $timestamps = true;

    /**
     * Get or create the singleton holder instance for general library uploads.
     */
     public static function singleton(): self
     {
         return static::firstOrCreate(['id' => 1], [
             'name' => 'General Media Library'
         ]);
     }
}
