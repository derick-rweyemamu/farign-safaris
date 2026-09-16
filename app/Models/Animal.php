<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Animal extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name'];

    protected $fillable = ['name', 'image_path', 'icon'];

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'animal_destination');
    }
}

