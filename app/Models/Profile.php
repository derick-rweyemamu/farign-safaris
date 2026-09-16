<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;

    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'bio',
        'position',
        'department',
        'location',
        'website',
        'linkedin',
        'twitter',
        'status',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

