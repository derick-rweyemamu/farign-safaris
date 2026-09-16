<?php
/* [NEW] [InvoiceItem.php](file:///d:/LOCALSERVER/laragon/www/ mwangwi-safaris/app/Models/InvoiceItem.php) */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'tour_id',
        'item_name',
        'description',
        'quantity',
        'unit_price',
        'tax',
        'discount_value',
        'discount_type',
        'tax_applied',
        'adults',
        'children',
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_applied' => 'boolean',
        'adults' => 'integer',
        'children' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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

