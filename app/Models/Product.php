<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductCategory;

class Product extends Model implements HasMedia
{
    use \Spatie\Translatable\HasTranslations;
    public $translatable = ['name', 'description'];

    use InteractsWithMedia;

    use SoftDeletes;
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'slug',
        'description',
        'unit_price',
        'cost_price',
        'stock_quantity',
        'unit',
        'tax_rates',
        'is_active',
        'image',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'tax_rates' => 'array',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

}
