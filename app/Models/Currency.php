<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_active',
        'decimal_places',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

