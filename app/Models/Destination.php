<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\City;
use App\Models\Activity;
use App\Models\Animal;
use App\Models\Tour;

class Destination extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSEO;

    use SoftDeletes;
    use \App\Traits\ParsesBuilderHtml;
    use \Spatie\Translatable\HasTranslations;

    public $translatable = ['name', 'tagline', 'description', 'builder_html', 'builder_data'];

    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'city_id',
        'name',
        'tagline',
        'slug',
        'description',
        'builder_html',
        'builder_data',
        'featured_image',
        'gallery',
        'is_active',
        'is_featured',
        'show_on_page',
        // Concession Fees
        'concession_fee_adult_high_season',
        'concession_fee_adult_low_season',
        'concession_fee_child_high_season',
        'concession_fee_child_low_season',
        // Crater Fees
        'crater_fee_adult_high_season',
        'crater_fee_adult_low_season',
        'crater_fee_child_high_season',
        'crater_fee_child_low_season',
        // Park Fees
        'park_fee_adult_high_season',
        'park_fee_adult_low_season',
        'park_fee_child_high_season',
        'park_fee_child_low_season',
        // Pass Fees
        'pass_fee_adult_high_season',
        'pass_fee_adult_low_season',
        'pass_fee_child_high_season',
        'pass_fee_child_low_season',
    ];

    protected $casts = [
        'city_id'                          => 'integer',
        'is_active'                        => 'boolean',
        'show_on_page'                     => 'boolean',
        'gallery'                          => 'array',
        'concession_fee_adult_high_season' => 'decimal:2',
        'concession_fee_adult_low_season'  => 'decimal:2',
        'concession_fee_child_high_season' => 'decimal:2',
        'concession_fee_child_low_season'  => 'decimal:2',
        'crater_fee_adult_high_season'     => 'decimal:2',
        'crater_fee_adult_low_season'      => 'decimal:2',
        'crater_fee_child_high_season'     => 'decimal:2',
        'crater_fee_child_low_season'      => 'decimal:2',
        'park_fee_adult_high_season'       => 'decimal:2',
        'park_fee_adult_low_season'        => 'decimal:2',
        'park_fee_child_high_season'       => 'decimal:2',
        'park_fee_child_low_season'        => 'decimal:2',
        'pass_fee_adult_high_season'       => 'decimal:2',
        'pass_fee_adult_low_season'        => 'decimal:2',
        'pass_fee_child_high_season'       => 'decimal:2',
        'pass_fee_child_low_season'        => 'decimal:2',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_destination');
    }

    public function animals(): BelongsToMany
    {
        return $this->belongsToMany(Animal::class, 'animal_destination');
    }

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'tour_itineraries', 'location', 'tour_id', 'name', 'id')
                    ->distinct();
    }
    public function attractions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attraction::class)->orderBy('sort_order');
    }

}
