<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name', 'description'];

    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'activity_destination');
    }
}

