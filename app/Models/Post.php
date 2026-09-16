<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSEO;

    use SoftDeletes;
    use \App\Traits\ParsesBuilderHtml;
    use \Spatie\Translatable\HasTranslations;

    public $translatable = ['title', 'excerpt', 'content', 'builder_html', 'builder_data'];
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'published_at',
        'author_id',
        'post_category_id',
        'builder_html',
        'builder_data'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

}
