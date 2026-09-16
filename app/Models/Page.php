<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Page extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasSEO;

    use SoftDeletes;
    use \App\Traits\ParsesBuilderHtml;
    use \Spatie\Translatable\HasTranslations;

    public $translatable = ['title', 'content', 'builder_html', 'builder_data'];
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->acceptsMimeTypes(['image/png', 'image/webp']);
    }

    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'is_published',
        'meta_title',
        'meta_description',
        'builder_html',
        'builder_data'
    ];

}

