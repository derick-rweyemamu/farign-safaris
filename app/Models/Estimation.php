<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estimation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'lead_id',
        'itinerary_id',
        'tour_id',
        'estimate_number',
        'date',
        'expiry_date',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'notes',
        'terms',
        'created_by',
        'tax_rate_index',
        'discount_index',
        'due_date',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
        'base_grand_total',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'expiry_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'tax_rate_index' => 'integer',
            'discount_index' => 'integer',
            'due_date' => 'date',
            'exchange_rate' => 'decimal:6',
            'base_grand_total' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimationProduct::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'estimate_id');
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}

