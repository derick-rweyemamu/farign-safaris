<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourItinerary extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['title', 'description'];

    protected $fillable = [
        'tour_id',
        'day_number',
        'title',
        'description',
        'activities',
        'accommodation',
        'accommodation_id',
        'accommodation_amount',
        'meals',
        'meal_plan_id',
        'meal_plan_amount',
        'adult_price',
        'child_price',
        'image_path',
        'featured_image',
        'image_caption',
        'gallery_images',
        'location',
        'distance_km',
        'driving_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'activities' => 'array',
        'gallery_images' => 'array',
        'distance_km' => 'integer',
        'driving_hours' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function accommodationModel()
    {
        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }

    public function mealPlan()
    {
        return $this->belongsTo(MealPlan::class, 'meal_plan_id');
    }

    public function animals()
    {
        return $this->belongsToMany(Animal::class, 'animal_tour_itinerary');
    }

    public function pivotActivities()
    {
        return $this->belongsToMany(Activity::class, 'activity_tour_itinerary')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'tour_itinerary_transport')
            ->withPivot('amount')
            ->withTimestamps();
    }
}

