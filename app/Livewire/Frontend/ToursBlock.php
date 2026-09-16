<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use App\Models\Tour;

class ToursBlock extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $categorySlug = 'all';
    public $tourTypeSlug = 'all';
    public $countryId = 'all';
    public $limit = 5;
    
    public $activeCategoryTab = null;

    public function mount()
    {
        // If categorySlug is specifically set (comma-separated), and not 'all'
        if ($this->categorySlug !== 'all' && !empty($this->categorySlug)) {
            $slugs = explode(',', $this->categorySlug);
            
            $firstCategory = \App\Models\TourCategory::whereIn('slug', $slugs)
                ->orderBy('sort_order', 'asc')
                ->first();
                
            $this->activeCategoryTab = $firstCategory ? $firstCategory->slug : $slugs[0];
        } else {
            $this->activeCategoryTab = 'all';
        }
    }

    public function setActiveTab($slug)
    {
        $this->activeCategoryTab = $slug;
        $this->resetPage(); // Reset pagination when switching tabs
    }

    public function render()
    {
        // Fetch categories for tabs if applicable
        $categories = collect();
        if ($this->categorySlug !== 'all' && !empty($this->categorySlug)) {
            $slugs = explode(',', $this->categorySlug);
            $categories = \App\Models\TourCategory::whereIn('slug', $slugs)
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        $query = Tour::with(['category', 'destinations']);
        
        // Filter by the active tab category
        if ($this->activeCategoryTab && $this->activeCategoryTab !== 'all') {
            $query->whereHas('category', function($q) {
                $q->where('slug', $this->activeCategoryTab);
            });
        } elseif ($this->categorySlug !== 'all') {
            // Fallback: If for some reason activeCategoryTab is 'all' but categorySlug is restricted
            $slugs = explode(',', $this->categorySlug);
            $query->whereHas('category', function($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            });
        }

        if ($this->tourTypeSlug !== 'all') {
            $query->whereHas('tourType', function($q) {
                $q->where('slug', $this->tourTypeSlug);
            });
        }
        
        if ($this->countryId !== 'all') {
            $query->whereHas('itineraries', function($q) {
                $q->whereIn('location', function($subq) {
                    $subq->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(destinations.name, '$.en'))")
                         ->from('destinations')
                         ->whereIn('city_id', function($c) {
                             $c->select('id')
                               ->from('cities')
                               ->where('country_id', $this->countryId);
                         });
                });
            });
        }
        $limit = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 5;
        $tours = $query->take($limit)->get();

        return view('livewire.frontend.tours-block', compact('tours', 'categories'));
    }
}
