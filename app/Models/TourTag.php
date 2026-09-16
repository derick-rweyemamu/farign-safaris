<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class TourTag extends Model
{
    use SoftDeletes, HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'tour_tour_tag');
    }
}

