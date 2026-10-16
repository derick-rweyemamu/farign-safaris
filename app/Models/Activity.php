<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name', 'description'];

    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return \Illuminate\Support\Facades\Storage::url($this->image);
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'activity_destination');
    }
}

