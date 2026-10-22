<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'author_name',
        'author_avatar',
        'author_location',
        'rating',
        'title',
        'content',
        'review_date',
        'trip_type',
        'review_url',
        'is_published',
        'is_featured',
        'sort_order',
        'source_id',
        'raw_data',
    ];

    protected $casts = [
        'rating' => 'float',
        'review_date' => 'date',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'raw_data' => 'array',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePlatform($query, ?string $platform)
    {
        if ($platform && $platform !== 'all') {
            return $query->where('platform', $platform);
        }
        return $query;
    }

    /**
     * Get initials for the reviewer avatar fallback.
     */
    public function getAuthorInitialsAttribute(): string
    {
        $words = preg_split('/[\s,&]+/', trim($this->author_name ?? 'Guest'));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: 'G';
    }

    /**
     * Human-readable platform name.
     */
    public function getPlatformLabelAttribute(): string
    {
        return match ($this->platform) {
            'tripadvisor' => 'TripAdvisor',
            'google' => 'Google',
            'safari_bookings' => 'SafariBookings',
            default => 'Direct / Guest',
        };
    }
}
