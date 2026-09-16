<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TourCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tours()
    {
        return $this->hasMany(Tour::class, 'tour_category_id');
    }

    public function toursMany()
    {
        return $this->belongsToMany(Tour::class, 'tour_tour_category');
    }
}

