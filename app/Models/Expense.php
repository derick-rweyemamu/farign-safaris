<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_type_id',
        'user_id',
        'amount',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
        'base_amount',
        'expense_date',
        'reference_no',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'base_amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

