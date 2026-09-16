<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NavigationMenu extends Model
{
    use HasTranslations;

    public $translatable = ['label'];

    protected $fillable = [
        'location',
        'label',
        'url',
        'page_id',
        'parent_id',
        'sort_order',
        'is_external',
        'is_active',
        'is_mega_menu',
    ];

    protected $casts = [
        'is_external'  => 'boolean',
        'is_active'    => 'boolean',
        'is_mega_menu' => 'boolean',
        'sort_order'   => 'integer',
    ];

    /** Location labels for display in admin */
    public static array $locations = [
        'header'               => 'Header Navigation',
        'footer_company'       => 'Footer — Company Links',
        'footer_destinations'  => 'Footer — Destinations',
        'footer_legal'         => 'Footer — Legal / Other',
    ];

    /** Relationship to Page (optional) */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /** Parent menu item */
    public function parent()
    {
        return $this->belongsTo(NavigationMenu::class, 'parent_id');
    }

    /** Child menu items */
    public function children()
    {
        return $this->hasMany(NavigationMenu::class, 'parent_id')->orderBy('sort_order');
    }

    /** Scope: active items for a given location, ordered */
    public function scopeForLocation($query, string $location)
    {
        return $query->where('location', $location)
                     ->where('is_active', true)
                     ->orderBy('sort_order');
    }

    /**
     * Resolve the final URL.
     * If a page is linked and has no manual URL set, build it from the page slug.
     */
    public function getResolvedUrlAttribute(): string
    {
        if ($this->page_id && $this->page) {
            if (in_array($this->page->slug, ['home', 'accueil'], true)) {
                return route('home');
            }
            return route('frontend.page.show', $this->page->slug);
        }
        
        $url = $this->url ?: '#';
        
        // If it's not a hash link, not absolute, and not already full URL, wrap it in url() helper
        if ($url !== '#' && !str_starts_with($url, 'http') && !str_starts_with($url, '/') && !str_starts_with($url, '#')) {
            return url($url);
        }
        
        return $url;
    }
}

