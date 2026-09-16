<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Itinerary extends Model implements HasMedia
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['title', 'description', 'overview'];

    use InteractsWithMedia;

    use SoftDeletes;

    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'client_id',
        'product_id',
        'reference_number',
        'title',
        'slug',
        'description',
        'overview',
        'min_age',
        'max_age',
        'accepts_children',
        'children_free_under_age',
        'accepts_solo_travelers',
        'duration_days',
        'duration_nights',
        'difficulty',
        'seasonal_months',
        'max_guests',
        'min_guests',
        'gallery',
        'client_name',
        'client_email',
        'start_date',
        'end_date',
        'adults',
        'children',
        'price_per_adult',
        'price_per_child',
        'total_price',
        'currency',
        'included',
        'excluded',
        'payment_terms',
        'status',
        'cover_image'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price_per_adult' => 'decimal:2',
        'price_per_child' => 'decimal:2',
        'total_price' => 'decimal:2',
        'included' => 'array',
        'excluded' => 'array',
        'seasonal_months' => 'array',
        'gallery' => 'array',
        'accepts_children' => 'boolean',
        'accepts_solo_travelers' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = date('Y') . '-' . rand(1000, 9999) . '.' . rand(1, 9);
            }
        });
    }

    public function days()
    {
        return $this->hasMany(ItineraryDay::class)->orderBy('date')->orderBy('id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function costSheet()
    {
        return $this->hasOne(CostSheet::class);
    }

    public function categories()
    {
        return $this->belongsToMany(TourCategory::class, 'itinerary_tour_category');
    }

    public function category()
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id'); // If added later, optional
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_itinerary');
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_itinerary');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_itinerary');
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'itinerary_transport');
    }

    public function mealPlans()
    {
        return $this->belongsToMany(MealPlan::class, 'itinerary_meal_plan');
    }
}

