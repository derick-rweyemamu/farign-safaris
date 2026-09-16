<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLoss extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'total_revenue',
        'total_general_expenses',
        'total_invoice_expenses',
        'total_payroll',
        'net_profit',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'total_general_expenses' => 'decimal:2',
        'total_invoice_expenses' => 'decimal:2',
        'total_payroll' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];
}

