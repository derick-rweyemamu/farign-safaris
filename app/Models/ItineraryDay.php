<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryDay extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['description'];

    protected $fillable = [
        'itinerary_id',
        'day',
        'date',
        'destination_id',
        'location',
        'description',
        'accommodation',
        'accommodation_id',
        'accommodation_amount',
        'meals',
        'meal_plan_id',
        'meal_plan_amount',
        'featured_image',
        'adult_price',
        'child_price'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class);
    }

    public function mealPlan()
    {
        return $this->belongsTo(MealPlan::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'itinerary_day_activity')
            ->withPivot('time_of_day', 'amount')
            ->withTimestamps();
    }

    public function transports()
    {
        return $this->belongsToMany(Transport::class, 'itinerary_day_transport')
            ->withPivot('time_of_day', 'amount')
            ->withTimestamps();
    }

    public function animals()
    {
        return $this->belongsToMany(Animal::class, 'animal_itinerary_day')
            ->withTimestamps();
    }
}

