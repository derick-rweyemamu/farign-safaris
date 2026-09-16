<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\City;
use App\Models\AccommodationRate;

class Accommodation extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    use HasSEO;
    use \Spatie\Translatable\HasTranslations;

    public $translatable = ['name', 'description'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'description',
        'featured_image',
        'gallery',
        'is_active'
    ];

    protected $casts = [
        'city_id' => 'integer',
        'is_active' => 'boolean',
        'gallery' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(AccommodationRate::class);
    }
}

