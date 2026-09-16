<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupTour extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['title', 'description'];

    protected $fillable = [
        'group_category_id',
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'price',
        'total_seats',
        'booked_seats',
        'available_seats',
        'featured_image',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'total_seats' => 'integer',
        'booked_seats' => 'integer',
        'available_seats' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($tour) {
            $total = $tour->total_seats ?? 0;
            $booked = $tour->booked_seats ?? 0;
            $tour->available_seats = max(0, $total - $booked);
        });
    }

    public function category()
    {
        return $this->belongsTo(GroupCategory::class, 'group_category_id');
    }
}

