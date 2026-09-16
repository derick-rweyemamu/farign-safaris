<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Accommodation;

class AccommodationRate extends Model
{
    protected $fillable = [
        'accommodation_id',
        'room_type',
        'season',
        'class',
        'sto',
        'rack'
    ];

    protected $casts = [
        'accommodation_id' => 'integer',
        'sto' => 'decimal:2',
        'rack' => 'decimal:2'
    ];

    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }
}

