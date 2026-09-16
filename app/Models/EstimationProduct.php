<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimationProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'estimation_id',
        'product_id',
        'tour_id',
        'adults',
        'children',
        'item_name',
        'quantity',
        'unit_price',
        'discount_value',
        'discount_type',
        'tax_applied',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_applied' => 'boolean',
    ];

    public function estimation(): BelongsTo
    {
        return $this->belongsTo(Estimation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}

