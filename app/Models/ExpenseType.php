<?php
/* [NEW] [ExpenseType.php](file:///d:/LOCALSERVER/laragon/www/ mwangwi-safaris/app/Models/ExpenseType.php) */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function invoiceExpenses(): HasMany
    {
        return $this->hasMany(InvoiceExpense::class);
    }

    public function generalExpenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}

