<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostSheetItem extends Model
{
    protected $fillable = [
        'cost_sheet_id',
        'item_type',
        'item_id',
        'name',
        'rate_label',
        'unit_cost',
        'quantity',
        'line_total',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'unit_cost'  => 'decimal:2',
        'line_total' => 'decimal:2',
        'quantity'   => 'integer',
        'sort_order' => 'integer',
    ];

    public function costSheet(): BelongsTo
    {
        return $this->belongsTo(CostSheet::class);
    }
}

