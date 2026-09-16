<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transport extends Model
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name', 'description'];

    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}

