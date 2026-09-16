<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'salutation',
        'city_id',
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'tin',
        'vrn',
        'address_line1',
        'address_line2',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'preferences',
        'dietary_restrictions',
        'medical_conditions',
        'passport_number',
        'passport_expiry_date',
        'passport_country',
        'loyalty_membership_number',
        'loyalty_points',
        'membership_tier',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
        'preferences' => 'array',
        'dietary_restrictions' => 'array',
        'medical_conditions' => 'array',
        'loyalty_points' => 'decimal:2',
        'gender' => \App\Enums\Gender::class,
        'status' => \App\Enums\ClientStatus::class,
        'membership_tier' => \App\Enums\MembershipTier::class,
        'salutation' => \App\Enums\Salutation::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

