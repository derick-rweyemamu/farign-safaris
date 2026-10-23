<?php

namespace App\Traits;

trait ParsesBuilderHtml
{
    protected bool $renderingHtml = false;

    /**
     * Override the content attribute to return parsed builder HTML.
     */
    public function getContentAttribute($value)
    {
        if ($this->renderingHtml) {
            return $value;
        }
        return $this->getRenderedHtml();
    }

    /**
     * Parse the GrapesJS HTML and render dynamic tour listing blocks server-side.
     */
    public function getRenderedHtml()
    {
        if ($this->renderingHtml) {
            return '';
        }
        $this->renderingHtml = true;
        try {
            $locale = app()->getLocale();
            $html = null;
            $hasLocaleTranslation = false;

            if (method_exists($this, 'getTranslation')) {
                // Try to get a locale-specific translation first
                $localeHtml = $locale !== 'en' ? $this->getTranslation('builder_html', $locale, false) : null;
                $trimmedLocale = trim($localeHtml ?: '');
                $isEmptyLocale = empty($localeHtml)
                    || $trimmedLocale === '<body></body>'
                    || $trimmedLocale === '<style>* { box-sizing: border-box; } body {margin: 0;}</style><body></body>'
                    || $trimmedLocale === '<style>* { box-sizing: border-box; } body {margin: 0;}</style>';

                if (!$isEmptyLocale) {
                    $html = $localeHtml;
                    $hasLocaleTranslation = true;
                }

                // Fall back to English builder_html
                if (empty($html)) {
                    $html = $this->getTranslation('builder_html', 'en', false);
                }

                $trimmed = trim($html ?: '');
                if (empty($html) || $trimmed === '<body></body>' || $trimmed === '<style>* { box-sizing: border-box; } body {margin: 0;}</style><body></body>' || $trimmed === '<style>* { box-sizing: border-box; } body {margin: 0;}</style>') {
                    $translatableAttrs = method_exists($this, 'getTranslatableAttributes') ? $this->getTranslatableAttributes() : ($this->translatable ?? []);
                    $field = in_array('content', $translatableAttrs) ? 'content' : (in_array('description', $translatableAttrs) ? 'description' : null);
                    
                    if ($field) {
                        $html = $this->getTranslation($field, $locale, false) ?: $this->getTranslation($field, 'en', false);
                    }
                }
            }
            if (empty($html)) {
                // Use translation-aware accessors (not raw attributes) to avoid leaking JSON like {"en":""}
                if (method_exists($this, 'getTranslation')) {
                    $translatableAttrs2 = method_exists($this, 'getTranslatableAttributes') ? $this->getTranslatableAttributes() : ($this->translatable ?? []);
                    $fallbackContent = $this->getTranslation('builder_html', 'en', false);
                    if (empty($fallbackContent) && in_array('content', $translatableAttrs2)) {
                        $fallbackContent = $this->getTranslation('content', 'en', false);
                    }
                    if (empty($fallbackContent) && in_array('description', $translatableAttrs2)) {
                        $fallbackContent = $this->getTranslation('description', 'en', false);
                    }
                    $fallbackContent = $fallbackContent ?: '';
                } else {
                    $fallbackContent = $this->attributes['builder_html']
                        ?? $this->attributes['content']
                        ?? $this->attributes['description']
                        ?? '';
                }
                
                // Guard against raw JSON strings that Spatie Translatable may store
                if (!empty($fallbackContent) && str_starts_with(trim($fallbackContent), '{') && json_decode($fallbackContent) !== null) {
                    $fallbackContent = '';
                }
                $html = $fallbackContent;
            }
            if (empty($html)) {
                return '';
            }

            // Auto-translate via Google Translate if no locale-specific translation exists
            if ($locale !== 'en' && !$hasLocaleTranslation && !empty($html)) {
                try {
                    $translated = app(\App\Services\GoogleTranslateService::class)->translateHtml($html, $locale);
                    if ($translated && $translated !== $html) {
                        // Cache the translation back into the model so next request is instant (no API call)
                        if (method_exists($this, 'setTranslation') && $this->exists) {
                            $this->setTranslation('builder_html', $locale, $translated);
                            $this->saveQuietly();
                        }
                        $html = $translated;
                    }
                } catch (\Throwable $e) {
                    // Silently fallback to English if Google Translate fails
                    \Illuminate\Support\Facades\Log::warning('ParsesBuilderHtml auto-translate failed: ' . $e->getMessage());
                }
            }


        // Convert Alpine shorthand @events (e.g. @click, @mouseenter) to XML-safe x-on: events so libxml DOMDocument does not strip them
        $html = preg_replace('/(\s)@([a-zA-Z0-9_\.\:\-]+)=/', '$1x-on:$2=', $html ?? '');

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        // Wrap in a body tag with UTF-8 encoding to prevent formatting issues
        $dom->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Helper function to replace a node with rendered HTML
        $replaceNode = function($node, $renderedHtml) use ($dom) {
            if (!$node || !$node->parentNode) {
                return;
            }
            // Also protect Alpine @ directives inside rendered HTML before DOMDocument parsing
            $renderedHtml = preg_replace('/(\s)@([a-zA-Z0-9_\.\:\-]+)=/', '$1x-on:$2=', $renderedHtml ?? '');

            $tempDom = new \DOMDocument();
            libxml_use_internal_errors(true);
            @$tempDom->loadHTML('<?xml encoding="utf-8" ?><body>' . $renderedHtml . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            
            $body = $tempDom->getElementsByTagName('body')->item(0);
            if (!$body) {
                return;
            }

            $importedNode = $dom->importNode($body, true);
            while ($importedNode->hasChildNodes()) {
                $node->parentNode->insertBefore($importedNode->firstChild, $node);
            }
            $node->parentNode->removeChild($node);
        };

        // 1. Parse Group Tours First
        foreach ($xpath->query('//div[contains(@class, "group-tours-listing")]') as $node) {
            $categorySlug = $node->getAttribute('data-group-category') ?: 'all';
            $limit = $node->getAttribute('data-limit') ?: 6;
            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.group-tours-block", ["categorySlug" => $categorySlug, "limit" => $limit])',
                ['categorySlug' => $categorySlug, 'limit' => $limit]
            );
            $replaceNode($node, $rendered);
        }

        // 2. Parse Normal Tours
        foreach ($xpath->query('//div[contains(@class, "tours-listing") and not(contains(@class, "group-tours-listing"))]') as $node) {
            $categorySlug = $node->getAttribute('data-category') ?: 'all';
            $countryId = $node->getAttribute('data-country') ?: 'all';
            $tourTypeSlug = $node->getAttribute('data-tour-type') ?: 'all';
            $limit = $node->getAttribute('data-limit') ?: 6;
            $heading = $node->getAttribute('data-heading') ?: null;
            $headingAccent = $node->getAttribute('data-heading-accent') ?: null;
            $subheading = $node->getAttribute('data-subheading') ?: null;
            $layout = $node->getAttribute('data-layout') ?: 'auto';
            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.tours-block", [
                    "categorySlug" => $categorySlug,
                    "tourTypeSlug" => $tourTypeSlug,
                    "countryId" => $countryId,
                    "limit" => $limit,
                    "heading" => $heading,
                    "headingAccent" => $headingAccent,
                    "subheading" => $subheading,
                    "layout" => $layout
                ])',
                [
                    'categorySlug' => $categorySlug,
                    'tourTypeSlug' => $tourTypeSlug,
                    'countryId' => $countryId,
                    'limit' => $limit,
                    'heading' => $heading,
                    'headingAccent' => $headingAccent,
                    'subheading' => $subheading,
                    'layout' => $layout
                ]
            );
            $replaceNode($node, $rendered);
        }

        // 3. Parse Full Tour Page Block
        foreach ($xpath->query('//div[contains(@class, "safaris-tour-page-block")]') as $node) {
            $countryId = $node->getAttribute('data-country') ?: 'all';
            $categorySlug = $node->getAttribute('data-category') ?: 'all';
            $tagSlug = $node->getAttribute('data-tag') ?: 'all';
            $typeSlug = $node->getAttribute('data-type') ?: 'all';
            $limit = (int) ($node->getAttribute('data-limit') ?: 12);
            $customTitle = $node->getAttribute('data-title') ?: null;
            $customSubtitle = $node->getAttribute('data-subtitle') ?: null;
            
            $query = \App\Models\Tour::with(['category', 'destinations'])->orderBy('name');
            $destinationNames = collect();
            
            if ($countryId !== 'all') {
                $destinationNames = \App\Models\Destination::whereHas('city', function($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                })->pluck('name');

                $query->where(function($q) use ($countryId, $destinationNames) {
                    $q->whereHas('destinations.city', function($q2) use ($countryId) {
                        $q2->where('country_id', $countryId);
                    })->orWhereHas('itineraries', function($q2) use ($destinationNames) {
                        $q2->whereIn('location', $destinationNames);
                    });
                });
            }
            if ($categorySlug !== 'all') {
                $query->whereHas('category', function($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            }
            if ($tagSlug !== 'all') {
                $query->whereHas('tags', function($q) use ($tagSlug) {
                    $q->where('slug', $tagSlug);
                });
            }
            if ($typeSlug !== 'all') {
                $query->whereHas('tourType', function($q) use ($typeSlug) {
                    $q->where('slug', $typeSlug);
                });
            }

            $tourQueryCallback = function($q) use ($countryId, $destinationNames, $categorySlug, $tagSlug, $typeSlug) {
                if ($countryId !== 'all') {
                    $q->where(function($q2) use ($countryId, $destinationNames) {
                        $q2->whereHas('destinations.city', function($q3) use ($countryId) {
                            $q3->where('country_id', $countryId);
                        })->orWhereHas('itineraries', function($q3) use ($destinationNames) {
                            $q3->whereIn('location', $destinationNames);
                        });
                    });
                }
                if ($categorySlug !== 'all') {
                    $q->whereHas('category', function($q2) use ($categorySlug) {
                        $q2->where('slug', $categorySlug);
                    });
                }
                if ($tagSlug !== 'all') {
                    $q->whereHas('tags', function($q2) use ($tagSlug) {
                        $q2->where('slug', $tagSlug);
                    });
                }
                if ($typeSlug !== 'all') {
                    $q->whereHas('tourType', function($q2) use ($typeSlug) {
                        $q2->where('slug', $typeSlug);
                    });
                }
            };

            $tours = $query->get();
            $categories = \App\Models\TourCategory::withCount(['tours' => $tourQueryCallback])
                ->where('is_active', true)->orderBy('name')->get();
                
            $tags = \App\Models\TourTag::whereHas('tours', $tourQueryCallback)
                ->where('is_active', true)->orderBy('name')->get();
            $totalTours = $tours->count();
            
            $rendered = view('frontend.partials.tours-full-page', compact('tours', 'categories', 'tags', 'totalTours', 'limit', 'customTitle', 'customSubtitle'))->render();
            $replaceNode($node, $rendered);
        }

        // 3.5 Parse Full Hiking Tour Page Block
        foreach ($xpath->query('//div[contains(@class, "safaris-hiking-tour-page-block")]') as $node) {
            $countryId = $node->getAttribute('data-country') ?: 'all';
            $routeSlug = $node->getAttribute('data-route') ?: 'all';
            $typeSlug = $node->getAttribute('data-type') ?: 'all';
            $limit = (int) ($node->getAttribute('data-limit') ?: 12);
            $customTitle = $node->getAttribute('data-title') ?: null;
            $customSubtitle = $node->getAttribute('data-subtitle') ?: null;
            
            $query = \App\Models\Tour::with(['tourRoute', 'destinations'])->orderBy('name');
            $destinationNames = collect();
            
            if ($countryId !== 'all') {
                $destinationNames = \App\Models\Destination::whereHas('city', function($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                })->pluck('name');

                $query->where(function($q) use ($countryId, $destinationNames) {
                    $q->whereHas('destinations.city', function($q2) use ($countryId) {
                        $q2->where('country_id', $countryId);
                    })->orWhereHas('itineraries', function($q2) use ($destinationNames) {
                        $q2->whereIn('location', $destinationNames);
                    });
                });
            }
            if ($routeSlug !== 'all') {
                $routeSlugs = explode(',', $routeSlug);
                $query->whereHas('tourRoute', function($q) use ($routeSlugs) {
                    $q->whereIn('slug', $routeSlugs);
                });
            }
            if ($typeSlug !== 'all') {
                $query->whereHas('tourType', function($q) use ($typeSlug) {
                    $q->where('slug', $typeSlug);
                });
            }

            $tourQueryCallback = function($q) use ($countryId, $destinationNames, $routeSlug, $typeSlug) {
                if ($countryId !== 'all') {
                    $q->where(function($q2) use ($countryId, $destinationNames) {
                        $q2->whereHas('destinations.city', function($q3) use ($countryId) {
                            $q3->where('country_id', $countryId);
                        })->orWhereHas('itineraries', function($q3) use ($destinationNames) {
                            $q3->whereIn('location', $destinationNames);
                        });
                    });
                }
                if ($routeSlug !== 'all') {
                    $routeSlugs = explode(',', $routeSlug);
                    $q->whereHas('tourRoute', function($q2) use ($routeSlugs) {
                        $q2->whereIn('slug', $routeSlugs);
                    });
                }
                if ($typeSlug !== 'all') {
                    $q->whereHas('tourType', function($q2) use ($typeSlug) {
                        $q2->where('slug', $typeSlug);
                    });
                }
            };

            $tours = $query->get();
            $routes = \App\Models\TourRoute::withCount(['tours' => $tourQueryCallback])
                ->where('is_active', true)
                ->when($routeSlug !== 'all', function($q) use ($routeSlug) {
                    $q->whereIn('slug', explode(',', $routeSlug));
                })
                ->orderBy('name')->get();
                
            $totalTours = $tours->count();
            
            $rendered = view('frontend.partials.hiking-tours-full-page', compact('tours', 'routes', 'totalTours', 'limit', 'customTitle', 'customSubtitle', 'routeSlug'))->render();
            $replaceNode($node, $rendered);
        }

        // 4. Parse Destinations Block
        foreach ($xpath->query('//div[contains(@class, "destinations-listing")]') as $node) {
            $limit = $node->getAttribute('data-limit') ?: 6;
            $countryId = $node->getAttribute('data-country-id') ?: null;
            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.destinations-block", ["limit" => $limit, "countryId" => $countryId])',
                ['limit' => $limit, 'countryId' => $countryId]
            );
            $replaceNode($node, $rendered);
        }

        // 4.5 Parse Activities Slider Block
        foreach ($xpath->query('//div[contains(@class, "safaris-activities-slider-block")] | //section[contains(@class, "safaris-activities-slider-block")]') as $node) {
            $activityIds = $node->getAttribute('data-activities') ?: 'all';
            $heading = $node->getAttribute('data-heading') ?: null;
            $headingAccent = $node->getAttribute('data-heading-accent') ?: null;
            $subheading = $node->getAttribute('data-subheading') ?: null;
            $theme = $node->getAttribute('data-theme') ?: 'light';
            $autoplay = $node->getAttribute('data-autoplay') !== 'false';
            $limit = (int) ($node->getAttribute('data-limit') ?: 12);

            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.activities-slider-block", [
                    "activityIds" => $activityIds,
                    "heading" => $heading,
                    "headingAccent" => $headingAccent,
                    "subheading" => $subheading,
                    "theme" => $theme,
                    "autoplay" => $autoplay,
                    "limit" => $limit
                ])',
                [
                    'activityIds' => $activityIds,
                    'heading' => $heading,
                    'headingAccent' => $headingAccent,
                    'subheading' => $subheading,
                    'theme' => $theme,
                    'autoplay' => $autoplay,
                    'limit' => $limit
                ]
            );
            // If this node is wrapped inside an outer builder section (e.g. safaris-activities-slider-section), replace the outer section
            $targetNode = $node;
            $curr = $node->parentNode;
            while ($curr && $curr instanceof \DOMElement && $curr->nodeName !== 'body' && $curr->nodeName !== '#document') {
                if ($curr->nodeName === 'section' && str_contains($curr->getAttribute('class') ?? '', 'safaris-activities-slider-section')) {
                    $targetNode = $curr;
                    break;
                }
                $curr = $curr->parentNode;
            }

            $replaceNode($targetNode, $rendered);
        }

        // 4.6 Parse Trekking Slider Block
        $trekkingProcessed = false;
        foreach ($xpath->query('//section[contains(@class, "safaris-trekking-slider-section")] | //section[@id="trekking"] | //div[contains(@class, "safaris-trekking-slider-block")] | //section[contains(@class, "safaris-trekking-slider-block")]') as $node) {
            if ($trekkingProcessed || !$node || !$node->parentNode) {
                continue;
            }

            $tourType = $node->getAttribute('data-tour-type') ?: 'trekking';
            $bgImage = $node->getAttribute('data-bg-image') ?: null;
            $limit = (int) ($node->getAttribute('data-limit') ?: 10);
            $autoplay = $node->getAttribute('data-autoplay') !== 'false';

            // Find outer section if wrapped
            $targetNode = $node;
            $curr = $node->parentNode;
            while ($curr && $curr instanceof \DOMElement && $curr->nodeName !== 'body' && $curr->nodeName !== '#document') {
                if ($curr->nodeName === 'section' && (str_contains($curr->getAttribute('class') ?? '', 'safaris-trekking-slider-section') || $curr->getAttribute('id') === 'trekking')) {
                    $targetNode = $curr;
                    break;
                }
                $curr = $curr->parentNode;
            }
            if (!$targetNode || !$targetNode->parentNode) {
                continue;
            }
            $scope = $targetNode;

            if (empty($bgImage) && preg_match('/background-image:\s*url\([\'"]?([^\'")]+)[\'"]?\)/i', $scope->getAttribute('style') . ' ' . $dom->saveHTML($scope), $bgMatches)) {
                $bgImage = $bgMatches[1];
            }

            // 1. Subheading Tagline & Tailwind Classes
            $subheadingTag = $node->getAttribute('data-subheading-tag');
            $subheadingClass = $node->getAttribute('data-subheading-class');
            $badgeNodes = $xpath->query('.//*[contains(@class, "trekking-badge")] | .//*[@id="its3b1"]', $scope);
            if ($badgeNodes->length > 0) {
                $rawText = trim($badgeNodes->item(0)->textContent);
                if (!empty($rawText)) $subheadingTag = $rawText;
                $rawClass = trim($badgeNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-badge\b/', '', $rawClass));
                if (!empty($cleanClass)) $subheadingClass = $cleanClass;
            }
            $subheadingTag = $subheadingTag ?: 'Trekking Experience';

            // 2. Heading Prefix & Tailwind Classes (e.g. "Package for")
            $headingPrefix = $node->getAttribute('data-heading-prefix');
            $headingPrefixClass = $node->getAttribute('data-heading-prefix-class');
            $prefixNodes = $xpath->query('.//*[contains(@class, "trekking-heading-prefix")] | .//*[@id="i392uh"]', $scope);
            if ($prefixNodes->length > 0) {
                $rawText = trim($prefixNodes->item(0)->textContent);
                if (!empty($rawText)) $headingPrefix = $rawText;
                $rawClass = trim($prefixNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-heading-prefix\b/', '', $rawClass));
                if (!empty($cleanClass)) $headingPrefixClass = $cleanClass;
            }
            $headingPrefix = $headingPrefix ?: 'Package for';

            // 3. Heading Accent & Tailwind Classes (e.g. "Mt.Kilimanjaro")
            $headingAccent = $node->getAttribute('data-heading-accent');
            $headingAccentClass = $node->getAttribute('data-heading-accent-class');
            $accentNodes = $xpath->query('.//*[contains(@class, "trekking-heading-accent")] | .//*[@id="iire5v"]', $scope);
            if ($accentNodes->length > 0) {
                $rawText = trim($accentNodes->item(0)->textContent);
                if (!empty($rawText)) $headingAccent = $rawText;
                $rawClass = trim($accentNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-heading-accent\b/', '', $rawClass));
                if (!empty($cleanClass)) $headingAccentClass = $cleanClass;
            }
            $headingAccent = $headingAccent ?: 'Mt.Kilimanjaro';

            // 4. Heading Suffix & Tailwind Classes (e.g. "Adventure")
            $headingSuffix = $node->getAttribute('data-heading-suffix');
            $headingSuffixClass = $node->getAttribute('data-heading-suffix-class');
            $suffixNodes = $xpath->query('.//*[contains(@class, "trekking-heading-suffix")] | .//*[@id="ij3e7b"]', $scope);
            if ($suffixNodes->length > 0) {
                $rawText = trim($suffixNodes->item(0)->textContent);
                if (!empty($rawText)) $headingSuffix = $rawText;
                $rawClass = trim($suffixNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-heading-suffix\b/', '', $rawClass));
                if (!empty($cleanClass)) $headingSuffixClass = $cleanClass;
            }
            $headingSuffix = $headingSuffix ?: 'Adventure';

            // 5. Description Paragraph & Tailwind Classes
            $description = $node->getAttribute('data-description');
            $descriptionClass = $node->getAttribute('data-description-class');
            $descNodes = $xpath->query('.//*[contains(@class, "trekking-desc")] | .//*[@id="i5hhup"]', $scope);
            if ($descNodes->length > 0) {
                $rawText = trim($descNodes->item(0)->textContent);
                if (!empty($rawText)) $description = $rawText;
                $rawClass = trim($descNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-desc\b/', '', $rawClass));
                if (!empty($cleanClass)) $descriptionClass = $cleanClass;
            }

            // 6. Cursive Text & Tailwind Classes
            $cursiveText = $node->getAttribute('data-cursive-text');
            $cursiveClass = $node->getAttribute('data-cursive-class');
            $cursiveNodes = $xpath->query('.//*[contains(@class, "trekking-cursive")] | .//*[@id="ik5thi"]', $scope);
            if ($cursiveNodes->length > 0) {
                $rawText = trim($cursiveNodes->item(0)->textContent);
                if (!empty($rawText)) $cursiveText = $rawText;
                $rawClass = trim($cursiveNodes->item(0)->getAttribute('class') ?? '');
                $cleanClass = trim(preg_replace('/\btrekking-cursive\b/', '', $rawClass));
                if (!empty($cleanClass)) $cursiveClass = $cleanClass;
            }
            $cursiveText = $cursiveText ?: 'Trusted Partner for a Safe and Memorable Kilimanjaro Adventure';

            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.trekking-slider-block", [
                    "tourType" => $tourType,
                    "subheadingTag" => $subheadingTag,
                    "subheadingClass" => $subheadingClass,
                    "headingPrefix" => $headingPrefix,
                    "headingPrefixClass" => $headingPrefixClass,
                    "headingAccent" => $headingAccent,
                    "headingAccentClass" => $headingAccentClass,
                    "headingSuffix" => $headingSuffix,
                    "headingSuffixClass" => $headingSuffixClass,
                    "description" => $description,
                    "descriptionClass" => $descriptionClass,
                    "cursiveText" => $cursiveText,
                    "cursiveClass" => $cursiveClass,
                    "bgImage" => $bgImage,
                    "limit" => $limit,
                    "autoplay" => $autoplay
                ])',
                [
                    'tourType' => $tourType,
                    'subheadingTag' => $subheadingTag,
                    'subheadingClass' => $subheadingClass,
                    'headingPrefix' => $headingPrefix,
                    'headingPrefixClass' => $headingPrefixClass,
                    'headingAccent' => $headingAccent,
                    'headingAccentClass' => $headingAccentClass,
                    'headingSuffix' => $headingSuffix,
                    'headingSuffixClass' => $headingSuffixClass,
                    'description' => $description,
                    'descriptionClass' => $descriptionClass,
                    'cursiveText' => $cursiveText,
                    'cursiveClass' => $cursiveClass,
                    'bgImage' => $bgImage,
                    'limit' => $limit,
                    'autoplay' => $autoplay
                ]
            );

            $replaceNode($targetNode, $rendered);
            $trekkingProcessed = true;
        }

        // 5. Parse Dynamic Tour Types Selects
        foreach ($xpath->query('//select[contains(@class, "dynamic-tour-types")]') as $node) {
            // Remove existing dummy options
            while ($node->hasChildNodes()) {
                $node->removeChild($node->firstChild);
            }
            
            // Add a default placeholder option
            $defaultOption = $dom->createElement('option', 'Select Trip Type');
            $defaultOption->setAttribute('value', '');
            $defaultOption->setAttribute('disabled', 'disabled');
            $defaultOption->setAttribute('selected', 'selected');
            $node->appendChild($defaultOption);

            // Dynamically load Tour Types from DB
            $tourTypes = \App\Models\TourType::orderBy('name')->get();
            foreach ($tourTypes as $tourType) {
                $option = $dom->createElement('option', htmlspecialchars($tourType->name));
                $option->setAttribute('value', $tourType->name);
                $node->appendChild($option);
            }
        }

        // 6. Parse Quick Nav (Posts) Block
        foreach ($xpath->query('//div[contains(@class, "safaris-quick-nav-block")]') as $node) {
            // Set parent style to position: relative to support absolute layout
            $parentNode = $node->parentNode;
            if ($parentNode) {
                $currentStyle = $parentNode->getAttribute('style') ?: '';
                if (stripos($currentStyle, 'position:') === false) {
                    $parentNode->setAttribute('style', rtrim($currentStyle, ';') . '; position: relative;');
                }
            }

            $categorySlug = $node->getAttribute('data-post-category') ?: 'all';
            $target = $node->getAttribute('data-target') ?: '_self';
            
            $query = \App\Models\Post::where('is_published', true);
            $categoryName = 'Quick Navigation';
            
            if ($categorySlug !== 'all') {
                $category = \App\Models\PostCategory::where('slug', $categorySlug)->first();
                if ($category) {
                    $query->where('post_category_id', $category->id);
                    $categoryName = $category->name;
                }
            }
            
            $posts = $query->orderBy('published_at', 'desc')->get();
            
            $rendered = view('frontend.partials.quick-nav', compact('posts', 'categoryName', 'target'))->render();
            $replaceNode($node, $rendered);
        }

        // 7. Parse Kilimanjaro Success Rate Calculator Block
        foreach ($xpath->query('//div[contains(@class, "safaris-kili-calculator-block")]') as $node) {
            $customTitle = null;
            $titleNodes = $xpath->query('.//*[contains(@class, "kili-title")]', $node);
            if ($titleNodes->length > 0) {
                $customTitle = trim($titleNodes->item(0)->textContent);
            }
            if (!$customTitle) {
                $customTitle = $node->getAttribute('data-title') ?: null;
            }

            $customSubtitle = null;
            $subNodes = $xpath->query('.//*[contains(@class, "kili-sub")]', $node);
            if ($subNodes->length > 0) {
                $customSubtitle = trim($subNodes->item(0)->textContent);
            }
            if (!$customSubtitle) {
                $customSubtitle = $node->getAttribute('data-subtitle') ?: null;
            }

            $customImage = null;
            $imgNodes = $xpath->query('.//img', $node);
            if ($imgNodes->length > 0 && !empty($imgNodes->item(0)->getAttribute('src'))) {
                $customImage = trim($imgNodes->item(0)->getAttribute('src'));
            }
            if (empty($customImage)) {
                $customImage = $node->getAttribute('data-image') ?: null;
            }

            $customBadge = null;
            $badgeNodes = $xpath->query('.//*[contains(@class, "kili-badge")]', $node);
            if ($badgeNodes->length > 0) {
                $customBadge = trim($badgeNodes->item(0)->textContent);
            }
            if (!$customBadge) {
                $customBadge = $node->getAttribute('data-badge') ?: null;
            }

            $customHeading = null;
            $headingNodes = $xpath->query('.//*[contains(@class, "kili-heading")]', $node);
            if ($headingNodes->length > 0) {
                $customHeading = trim($headingNodes->item(0)->textContent);
            }
            if (!$customHeading) {
                $customHeading = $node->getAttribute('data-heading') ?: null;
            }

            $customDescription = null;
            $descNodes = $xpath->query('.//*[contains(@class, "kili-desc")]', $node);
            if ($descNodes->length > 0) {
                $customDescription = trim($descNodes->item(0)->textContent);
            }
            if (!$customDescription) {
                $customDescription = $node->getAttribute('data-description') ?: null;
            }

            $rendered = \Illuminate\Support\Facades\Blade::render(
                '<livewire:frontend.kilimanjaro-calculator '
                . ':custom-title="$customTitle" '
                . ':custom-subtitle="$customSubtitle" '
                . ':custom-image="$customImage" '
                . ':custom-badge="$customBadge" '
                . ':custom-heading="$customHeading" '
                . ':custom-description="$customDescription" />',
                [
                    'customTitle' => $customTitle,
                    'customSubtitle' => $customSubtitle,
                    'customImage' => $customImage,
                    'customBadge' => $customBadge,
                    'customHeading' => $customHeading,
                    'customDescription' => $customDescription,
                ]
            );
            $replaceNode($node, $rendered);
        }

        // 8. Parse Hero Section Backgrounds
        $hasYoutubeHero = false;
        foreach ($xpath->query('//section[contains(@class, "hero")]') as $node) {
            $bgType = $node->getAttribute('data-bg-type') ?: 'image';
            $bgImage = $node->getAttribute('data-bg-image') ?: '';
            $bgVideo = $node->getAttribute('data-bg-video') ?: '';
            $bgVideoUrl = $node->getAttribute('data-bg-video-url') ?: '';
            $overlayOpacity = $node->hasAttribute('data-overlay-opacity') ? $node->getAttribute('data-overlay-opacity') : '0.4';

            $bgContainer = null;
            foreach ($xpath->query('.//div[contains(@class, "hero-bg-container")]', $node) as $child) {
                $bgContainer = $child;
                break;
            }

            if ($bgContainer) {
                while ($bgContainer->hasChildNodes()) {
                    $bgContainer->removeChild($bgContainer->firstChild);
                }

                $bgContainer->setAttribute('style', rtrim($bgContainer->getAttribute('style'), ';') . '; overflow: hidden;');

                $html = '';
                
                // Final Background Image
                $finalImg = $bgImage ?: 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1920&q=80';
                $html .= '<div class="hero-bg" style="background-color: #4b5563; background-image: url(\'' . htmlspecialchars($finalImg) . '\') !important; background-size: cover; background-position: center; width: 100%; height: 100%; position: absolute; inset: 0; z-index: 0;"></div>';

                if ($bgType === 'image') {
                    if ((float)$overlayOpacity > 0) {
                        $html .= '<div class="hero-bg-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,' . htmlspecialchars($overlayOpacity) . ') !important; z-index: 2;"></div>';
                    }
                } else if (($bgType === 'video_upload' && $bgVideo) || ($bgType === 'video_url' && $bgVideoUrl)) {
                    $url = $bgType === 'video_upload' ? $bgVideo : $bgVideoUrl;
                    
                    if (preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|live\/|watch\?v=|\&v=)([^#\&\?]*).*/', $url, $matches) && strlen($matches[2]) === 11) {
                        $ytId = $matches[2];
                        $hasYoutubeHero = true;
                        
                        // Wrapper is initially invisible (opacity: 0) to hide YouTube's black loading screen
                        $html .= '<div class="yt-hero-wrapper" style="position: absolute; top: 50%; left: 50%; width: 150vw; height: 150vh; transform: translate(-50%, -50%); min-width: 100%; min-height: 100%; pointer-events: none; z-index: 1; opacity: 0; transition: opacity 0.5s ease;">';
                        $html .= '<div class="yt-hero-player" data-yt-id="' . $ytId . '"></div>';
                        $html .= '</div>';
                    } else {
                        $html .= '<video class="hero-bg-video" autoplay="autoplay" loop="loop" muted="muted" playsinline="playsinline" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none; z-index: 1;">';
                        $html .= '<source src="' . htmlspecialchars($url) . '" type="video/mp4">';
                        $html .= '</video>';
                    }
                    if ((float)$overlayOpacity > 0) {
                        $html .= '<div class="hero-bg-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,' . htmlspecialchars($overlayOpacity) . ') !important; z-index: 2;"></div>';
                    }
                }

                $tempDom = new \DOMDocument();
                libxml_use_internal_errors(true);
                @$tempDom->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                libxml_clear_errors();
                
                $importedNode = $dom->importNode($tempDom->getElementsByTagName('body')->item(0), true);
                while ($importedNode->hasChildNodes()) {
                    $bgContainer->appendChild($importedNode->firstChild);
                }
            }
        }

        if ($hasYoutubeHero) {
            $ytScript = '
            <script>
                (function() {
                    if (window.YT && window.YT.Player) {
                        initHeroYT();
                        return;
                    }

                    // Load YouTube iframe API once. Guard prevents duplicate loads
                    // when multiple hero sections are on the same page.
                    if (!window._ytApiLoading) {
                        window._ytApiLoading = true;
                        var tag = document.createElement("script");
                        tag.src = "https://www.youtube.com/iframe_api";
                        var firstScriptTag = document.getElementsByTagName("script")[0];
                        firstScriptTag ? firstScriptTag.parentNode.insertBefore(tag, firstScriptTag) : document.head.appendChild(tag);
                    }

                    var oldOnReady = window.onYouTubeIframeAPIReady;
                    window.onYouTubeIframeAPIReady = function() {
                        if (oldOnReady) oldOnReady();
                        initHeroYT();
                    };
                })();

                function initHeroYT() {
                    document.querySelectorAll(".yt-hero-player").forEach(function(el) {
                        if (el.getAttribute("data-yt-initialized")) return;
                        el.setAttribute("data-yt-initialized", "true");
                        
                        var player = new YT.Player(el, {
                            videoId: el.getAttribute("data-yt-id"),
                            width: "100%",
                            height: "100%",
                            playerVars: {
                                autoplay: 1,
                                controls: 0,
                                mute: 1,
                                rel: 0,
                                modestbranding: 1,
                                playsinline: 1,
                                disablekb: 1,
                                iv_load_policy: 3
                            },
                            events: {
                                onReady: function(e) {
                                    e.target.mute();
                                    e.target.playVideo();
                                },
                                onStateChange: function(e) {
                                    if (e.data === YT.PlayerState.PLAYING) {
                                        // Fade in the video only after it actually starts playing!
                                        if (e.target.getIframe() && e.target.getIframe().parentNode) {
                                            e.target.getIframe().parentNode.style.opacity = "1";
                                        }
                                        
                                        if (!el.ytInterval) {
                                            el.ytInterval = setInterval(function() {
                                                var duration = player.getDuration();
                                                var current = player.getCurrentTime();
                                                if (duration > 0 && (duration - current) < 0.4) {
                                                    player.seekTo(0);
                                                }
                                            }, 100);
                                        }
                                    } else if (e.data === YT.PlayerState.ENDED) {
                                        player.seekTo(0);
                                        player.playVideo();
                                    }
                                }
                            }
                        });
                    });
                }
            </script>';

            $tempDom = new \DOMDocument();
            libxml_use_internal_errors(true);
            @$tempDom->loadHTML('<?xml encoding="utf-8" ?><body>' . $ytScript . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            $importedNode = $dom->importNode($tempDom->getElementsByTagName('body')->item(0), true);
            while ($importedNode->hasChildNodes()) {
                $dom->getElementsByTagName('body')->item(0)->appendChild($importedNode->firstChild);
            }
        }

        // 9. Parse Page Header Block
        foreach ($xpath->query('//section[contains(@class, "safaris-page-header-block")]') as $node) {
            // Replace background image if page has featured_image
            $bgContainer = null;
            foreach ($xpath->query('.//div[contains(@class, "dynamic-bg-container")]', $node) as $child) {
                $bgContainer = $child;
                break;
            }
            if ($bgContainer) {
                $imgUrl = null;
                if (!empty($this->featured_image)) {
                    $imgUrl = \Illuminate\Support\Facades\Storage::url($this->featured_image);
                } else {
                    $globalHeroImage = app(\App\Settings\GeneralSetting::class)?->tour_hero_image;
                    if ($globalHeroImage) {
                        $imgUrl = \Illuminate\Support\Facades\Storage::url($globalHeroImage);
                    }
                }
                
                if ($imgUrl) {
                    while ($bgContainer->hasChildNodes()) {
                        $bgContainer->removeChild($bgContainer->firstChild);
                    }
                    $img = $dom->createElement('img');
                    $img->setAttribute('src', $imgUrl);
                    $img->setAttribute('alt', $this->title ?? 'Page Header');
                    $img->setAttribute('class', 'absolute inset-0 w-full h-full object-cover opacity-85');
                    $bgContainer->appendChild($img);
                }
            }

            // Replace H1 Title
            $h1Node = null;
            foreach ($xpath->query('.//h1[contains(@class, "dynamic-page-title")]', $node) as $child) {
                $h1Node = $child;
                break;
            }
            if ($h1Node && !empty($this->title)) {
                $h1Node->nodeValue = htmlspecialchars($this->title);
            }
        }

        // 10. Parse Dynamic Reviews Block
        $reviewsProcessed = false;
        foreach ($xpath->query('//section[contains(@class, "safaris-reviews-section")] | //section[contains(@class, "reviews-section")] | //section[@id="reviews"] | //div[contains(@class, "safaris-reviews-block")]') as $node) {
            if ($reviewsProcessed || !$node || !$node->parentNode) {
                continue;
            }

            $platform = $node->getAttribute('data-platform') ?: 'all';
            $mode = $node->getAttribute('data-mode') ?: 'all';
            $limit = (int) ($node->getAttribute('data-limit') ?: 6);
            $layout = $node->getAttribute('data-layout') ?: 'slider';
            $subheading = $node->getAttribute('data-subheading') ?: 'What Travellers Say';
            $heading = $node->getAttribute('data-heading') ?: 'Genuine Reviews From Our Guests';
            $description = $node->getAttribute('data-description') ?: null;
            $tripadvisorUrl = $node->getAttribute('data-tripadvisor-url') ?: 'https://www.tripadvisor.com/Attraction_Review-g297913-d33011633-Reviews-Fargin_Safaris-Arusha_Arusha_Region.html';
            $googleUrl = $node->getAttribute('data-google-url') ?: 'https://www.google.com/search?q=Fargin+Safaris+reviews';
            $safaribookingsUrl = $node->getAttribute('data-safaribookings-url') ?: 'https://www.safaribookings.com';
            $showBadges = $node->getAttribute('data-show-badges') !== 'false';
            $showAddReview = $node->getAttribute('data-show-add-review') !== 'false';

            // Find outer section if wrapped
            $targetNode = $node;
            $curr = $node->parentNode;
            while ($curr && $curr instanceof \DOMElement && $curr->nodeName !== 'body' && $curr->nodeName !== '#document') {
                if ($curr->nodeName === 'section' && (str_contains($curr->getAttribute('class') ?? '', 'safaris-reviews-section') || str_contains($curr->getAttribute('class') ?? '', 'reviews-section') || $curr->getAttribute('id') === 'reviews')) {
                    $targetNode = $curr;
                    break;
                }
                $curr = $curr->parentNode;
            }

            $rendered = \Illuminate\Support\Facades\Blade::render(
                '@livewire("frontend.reviews-block", [
                    "platform" => $platform,
                    "mode" => $mode,
                    "limit" => $limit,
                    "layout" => $layout,
                    "subheading" => $subheading,
                    "heading" => $heading,
                    "description" => $description,
                    "tripadvisorUrl" => $tripadvisorUrl,
                    "googleUrl" => $googleUrl,
                    "safaribookingsUrl" => $safaribookingsUrl,
                    "showBadges" => $showBadges,
                    "showAddReview" => $showAddReview
                ])',
                [
                    'platform' => $platform,
                    'mode' => $mode,
                    'limit' => $limit,
                    'layout' => $layout,
                    'subheading' => $subheading,
                    'heading' => $heading,
                    'description' => $description,
                    'tripadvisorUrl' => $tripadvisorUrl,
                    'googleUrl' => $googleUrl,
                    'safaribookingsUrl' => $safaribookingsUrl,
                    'showBadges' => $showBadges,
                    'showAddReview' => $showAddReview,
                ]
            );

            $replaceNode($targetNode, $rendered);
            $reviewsProcessed = true;
        }

        $htmlOut = $dom->saveHTML();
        $htmlOut = str_replace(['<?xml encoding="utf-8" ?><body>', '</body>'], '', $htmlOut);
        return trim($htmlOut);
        } finally {
            $this->renderingHtml = false;
        }
    }
}
