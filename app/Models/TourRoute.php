<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourRoute extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name', 'description'];

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
        return $this->hasMany(Tour::class);
    }
}
