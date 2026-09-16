<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Tour extends Model
{
    use HasSEO;
    use \Spatie\Translatable\HasTranslations;

    public $translatable = ['name', 'description', 'overview', 'included_features', 'excluded_features'];
    protected $fillable = [
        'tour_category_id',
        'tour_type_id',
        'tour_route_id',
        'name',
        'slug',
        'description',
        'overview',
        'included_features',
        'excluded_features',
        'min_age',
        'max_age',
        'accepts_children',
        'children_free_under_age',
        'accepts_solo_travelers',
        'featured_image',
        'duration_days',
        'duration_nights',
        'difficulty',
        'seasonal_months',
        'max_guests',
        'min_guests',
        'child_policy',
        'adult_price',
        'price_2_pax',
        'price_3_pax',
        'price_4_pax',
        'price_5_pax',
        'price_6_pax',
        'price_7_plus_pax',
        'child_price',
        'gallery',
        'type',
    ];

    protected $casts = [
        'included_features' => 'array',
        'excluded_features' => 'array',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'accepts_children' => 'boolean',
        'children_free_under_age' => 'integer',
        'accepts_solo_travelers' => 'boolean',
        'duration_days' => 'integer',
        'duration_nights' => 'integer',
        'seasonal_months' => 'array',
        'max_guests' => 'integer',
        'min_guests' => 'integer',
        'gallery' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }

    public function tourType()
    {
        return $this->belongsTo(TourType::class, 'tour_type_id');
    }

    public function tourRoute()
    {
        return $this->belongsTo(TourRoute::class);
    }

    public function categories()
    {
        return $this->belongsToMany(TourCategory::class, 'tour_tour_category');
    }

    public function itineraries()
    {
        return $this->hasMany(TourItinerary::class)->orderBy('day_number');
    }

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_tour');
    }

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'accommodation_tour');
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_tour');
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'tour_transport');
    }

    public function mealPlans()
    {
        return $this->belongsToMany(MealPlan::class, 'meal_plan_tour');
    }

    public function tags()
    {
        return $this->belongsToMany(TourTag::class, 'tour_tour_tag');
    }
}

