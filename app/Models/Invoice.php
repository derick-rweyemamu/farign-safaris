<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'estimate_id',
        'created_date',
        'due_date',
        'subtotal',
        'tax_total',
        'discount',
        'total',
        'balance_due',
        'notes',
        'terms',
        'status',
        'sent_at',
        'paid_at',
        'tax_rate_index',
        'discount_index',
        'currency_code',
        'currency_symbol',
        'exchange_rate',
        'base_grand_total',
        'is_recurring',
        'recurring_frequency',
        'recurring_next_date',
        'recurring_end_date',
        'parent_id',
    ];

    protected $casts = [
        'created_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'tax_rate_index' => 'integer',
        'discount_index' => 'integer',
        'exchange_rate' => 'decimal:6',
        'base_grand_total' => 'decimal:2',
        'is_recurring' => 'boolean',
        'recurring_next_date' => 'date',
        'recurring_end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimation::class, 'estimate_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(InvoiceExpense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_id');
    }

    public function recurringInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_id');
    }
}

