<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use SoftDeletes;
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'flag',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}

