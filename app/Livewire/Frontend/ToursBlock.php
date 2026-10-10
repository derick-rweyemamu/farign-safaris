<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use App\Models\Tour;
use App\Models\Country;
use App\Models\TourCategory;

class ToursBlock extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $categorySlug = 'all';
    public $tourTypeSlug = 'all';
    public $countryId = 'all';
    public $limit = 5;
    public $heading = null;
    public $headingAccent = null;
    public $subheading = null;
    public $layout = 'auto'; // 'auto', 'safarihq', 'grid'
    
    public $tourPage = 1;
    
    public $activeCategoryTab = null;
    public $activeCountryId = null;

    protected const FALLBACK_HERO = 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&q=80';

    /**
     * Parse the comma-separated country id list coming from the page builder.
     */
    public static function parseCountryIds($value): array
    {
        if ($value === 'all' || $value === null || $value === '') {
            return [];
        }
        $raw = is_array($value) ? $value : explode(',', (string) $value);
        return array_values(array_filter(array_map(fn($v) => (int) trim((string) $v), $raw)));
    }

    /**
     * Base tour query for a set of countries (Tour -> Destination -> City -> Country).
     */
    public static function toursForCountriesQuery(array $countryIds, $categorySlug = 'all', $tourTypeSlug = 'all')
    {
        $query = Tour::with(['category', 'destinations.city.country', 'itineraries'])->orderBy('name');

        if ($categorySlug !== 'all' && !empty($categorySlug)) {
            $slugs = explode(',', $categorySlug);
            $query->whereHas('category', fn($q) => $q->whereIn('slug', $slugs));
        }

        if ($tourTypeSlug !== 'all' && !empty($tourTypeSlug)) {
            $query->whereHas('tourType', fn($q) => $q->where('slug', $tourTypeSlug));
        }

        if (!empty($countryIds)) {
            $query->where(function ($q) use ($countryIds) {
                // Tours linked to destinations whose city belongs to the country
                $q->whereHas('destinations.city', fn($c) => $c->whereIn('country_id', $countryIds))
                  // Tours whose itinerary location is a destination in the country
                  ->orWhereHas('itineraries', function ($i) use ($countryIds) {
                      $i->whereIn('location', function ($d) use ($countryIds) {
                          $d->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(destinations.name, '$.en'))")
                            ->from('destinations')
                            ->whereNull('destinations.deleted_at')
                            ->whereIn('city_id', fn($c) => $c->select('id')->from('cities')->whereIn('country_id', $countryIds));
                      });
                  });
            });
        }

        return $query;
    }

    /**
     * Build display metadata for a country from its destinations in the database.
     */
    public static function countryMeta($country): array
    {
        $meta = [
            'name' => $country->name,
            'tagline' => __('Safaris and signature journeys in :country', ['country' => $country->name]),
            'hero_image' => self::FALLBACK_HERO,
            'description' => __('Explore extraordinary landscapes, wildlife reserves, and signature safari journeys across :country.', ['country' => $country->name]),
            'highlights' => [],
        ];

        $destinations = \App\Models\Destination::whereHas('city', fn($q) => $q->where('country_id', $country->id))
            ->orderByDesc('is_featured')
            ->get();

        if ($destinations->isEmpty()) {
            return $meta;
        }

        $meta['highlights'] = $destinations->pluck('name')->filter()->take(5)->values()->all();

        $withImage = $destinations->first(fn($d) => !empty($d->featured_image)) ?? $destinations->first();
        if (!empty($withImage->featured_image)) {
            $img = $withImage->featured_image;
            $meta['hero_image'] = str_starts_with($img, 'http') ? $img : \Illuminate\Support\Facades\Storage::url($img);
        }
        if (!empty($withImage->tagline)) {
            $meta['tagline'] = $withImage->tagline;
        } elseif (!empty($meta['highlights'])) {
            $meta['tagline'] = implode(' · ', array_slice($meta['highlights'], 0, 3));
        }
        if (!empty($withImage->description)) {
            $meta['description'] = \Illuminate\Support\Str::limit(trim(strip_tags($withImage->description)), 220);
        }

        return $meta;
    }

    /**
     * Country data for the page-builder multiselect + live preview (all from DB).
     */
    public static function editorCountryData(): array
    {
        $withDestinations = \App\Models\City::whereHas('destinations')->pluck('country_id')->unique()->filter()->all();

        return Country::orderBy('name')->get(['id', 'name'])->map(function ($c) use ($withDestinations) {
            $hasDest = in_array($c->id, $withDestinations);
            $row = [
                'id' => $c->id,
                'name' => $c->name,
                'destinations_count' => 0,
                'tours_count' => 0,
                'tagline' => null,
                'description' => null,
                'image' => null,
                'tours' => [],
            ];
            $meta = self::countryMeta($c);
            $row['tagline'] = $meta['tagline'];
            $row['description'] = $meta['description'];
            $row['image'] = $meta['hero_image'];

            if ($hasDest) {
                $toursQuery = self::toursForCountriesQuery([$c->id]);
                $row['destinations_count'] = \App\Models\Destination::whereHas('city', fn($q) => $q->where('country_id', $c->id))->count();
                $row['tours_count'] = (clone $toursQuery)->count();
                $row['tours'] = $toursQuery->take(4)->get()->map(fn($t) => [
                    'name' => $t->name,
                    'days' => $t->duration_days,
                    'price' => $t->adult_price,
                    'image' => $t->featured_image
                        ? (str_starts_with($t->featured_image, 'http') ? $t->featured_image : \Illuminate\Support\Facades\Storage::url($t->featured_image))
                        : $meta['hero_image'],
                ])->values()->all();
            }
            return $row;
        })
        // Countries that actually have destinations first
        ->sortByDesc('destinations_count')->values()->all();
    }

    public function mount()
    {
        // 1. Setup category tabs if categorySlug has comma-separated values
        if ($this->categorySlug !== 'all' && !empty($this->categorySlug)) {
            $slugs = explode(',', $this->categorySlug);
            $firstCategory = TourCategory::whereIn('slug', $slugs)->orderBy('sort_order', 'asc')->first();
            $this->activeCategoryTab = $firstCategory ? $firstCategory->slug : $slugs[0];
        } else {
            $this->activeCategoryTab = 'all';
        }

        // 2. Setup country tabs
        $countries = $this->resolveCountries();
        if ($countries->isNotEmpty()) {
            $this->activeCountryId = $countries->first()->id;
        }
    }

    public function setActiveTab($slug)
    {
        $this->activeCategoryTab = $slug;
        $this->resetPage();
    }

    public function setActiveCountry($id)
    {
        $this->activeCountryId = (int)$id;
        $this->tourPage = 1;
        $this->resetPage();
    }

    public function nextTourPage()
    {
        $perPage = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 3;
        $total = $this->getActiveCountryToursQuery()->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($this->tourPage < $totalPages) {
            $this->tourPage++;
        }
    }

    public function previousTourPage()
    {
        if ($this->tourPage > 1) {
            $this->tourPage--;
        }
    }

    public function setTourPage($page)
    {
        $perPage = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 3;
        $total = $this->getActiveCountryToursQuery()->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $this->tourPage = max(1, min((int)$page, $totalPages));
    }

    public function resolveCountries()
    {
        if ($this->layout === 'grid') {
            return collect();
        }

        $countryIds = self::parseCountryIds($this->countryId);

        // 1. Countries explicitly selected in the Page Builder (keep the selection order)
        if (!empty($countryIds)) {
            $countries = Country::whereIn('id', $countryIds)->get();
            $orderMap = array_flip($countryIds);
            return $countries->sortBy(fn($c) => $orderMap[$c->id] ?? 999)->values();
        }

        // 2. 'all' -> every country that has destinations in the database
        $activeCountryIds = \App\Models\City::whereHas('destinations')
            ->pluck('country_id')
            ->unique()
            ->filter()
            ->values();

        if ($activeCountryIds->isNotEmpty()) {
            return Country::whereIn('id', $activeCountryIds)->orderBy('name')->get();
        }

        // Fallback: if no destinations exist in the database yet, fallback to popular East Africa countries
        return Country::whereIn('name', ['Tanzania', 'Kenya', 'Uganda', 'Rwanda', 'Zanzibar'])->orderBy('name')->get();
    }

    public function getActiveCountryProperty()
    {
        if ($this->activeCountryId) {
            $found = Country::find($this->activeCountryId);
            if ($found) return $found;
        }
        $countries = $this->resolveCountries();
        return $countries->first();
    }

    public function getActiveCountryToursQuery()
    {
        if (!$this->activeCountryId || !Country::whereKey($this->activeCountryId)->exists()) {
            return Tour::query()->whereRaw('1=0');
        }

        return self::toursForCountriesQuery([(int) $this->activeCountryId], $this->categorySlug, $this->tourTypeSlug);
    }

    public function getActiveCountryToursProperty()
    {
        $limit = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 3;
        return $this->getActiveCountryToursQuery()->take($limit)->get();
    }

    public function getCountryMeta($country): array
    {
        return self::countryMeta($country);
    }

    public function getTourHighlights($tour): string
    {
        // 1. From destinations relation if present
        if ($tour->destinations && $tour->destinations->isNotEmpty()) {
            $destNames = $tour->destinations->map(function($d) {
                return is_array($d->name) ? ($d->name['en'] ?? reset($d->name)) : $d->name;
            })->filter()->take(4)->join(' • ');
            if (!empty($destNames)) {
                return $destNames;
            }
        }

        // 2. From itineraries
        if ($tour->itineraries && $tour->itineraries->isNotEmpty()) {
            $locs = $tour->itineraries->map(function($i) {
                if (!empty($i->location)) {
                    return $i->location;
                }
                $t = is_array($i->title) ? ($i->title['en'] ?? reset($i->title)) : $i->title;
                if (!empty($t)) {
                    $clean = preg_replace('/^(Day\s+\d+[:\s-]*|Explore\s+|Transfer\s+to\s+|Game\s+Drive\s+in\s+|Visit\s+|Departure\s+from\s+)/i', '', trim($t));
                    return trim(explode('-', $clean)[0]);
                }
                return null;
            })->filter()->unique()->take(3)->values()->join(' • ');

            if (!empty($locs)) {
                return $locs;
            }
        }

        // 3. Fallback
        return ($tour->duration_days ? $tour->duration_days . ' Days' : 'Signature Journey') . ($tour->adult_price ? ' • From $' . number_format($tour->adult_price) : '');
    }

    public function render()
    {
        $countries = $this->resolveCountries();
        $isMultiCountry = ($this->layout !== 'grid') && $countries->isNotEmpty();

        if ($isMultiCountry && $countries->isNotEmpty()) {
            if (!$this->activeCountryId || !$countries->contains('id', $this->activeCountryId)) {
                $this->activeCountryId = $countries->first()->id;
            }
            $activeCountry = Country::find($this->activeCountryId) ?: $countries->first();
            $activeMeta = $this->getCountryMeta($activeCountry);

            // Pagination parameters
            $perPage = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 3;
            $toursQuery = $this->getActiveCountryToursQuery();
            $totalTours = $toursQuery->count();
            $totalPages = max(1, (int) ceil($totalTours / $perPage));

            if ($this->tourPage > $totalPages) {
                $this->tourPage = $totalPages;
            }
            if ($this->tourPage < 1) {
                $this->tourPage = 1;
            }

            $offset = ($this->tourPage - 1) * $perPage;
            $rawTours = (clone $toursQuery)->skip($offset)->take($perPage)->get();

            $tours = $rawTours->map(function($t) use ($activeMeta) {
                $t->highlights_text = $this->getTourHighlights($t);
                if ($t->featured_image) {
                    $t->thumb_url = str_starts_with($t->featured_image, 'http')
                        ? $t->featured_image
                        : \Illuminate\Support\Facades\Storage::url($t->featured_image);
                } else {
                    $t->thumb_url = $activeMeta['hero_image'];
                }
                return $t;
            });

            $countriesWithMeta = $countries->map(function($c) {
                $c->meta_data = $this->getCountryMeta($c);
                return $c;
            });

            $defaultHeading = $countries->count() === 1 ? $activeCountry->name : ($countries->count() . ' Destinations.');
            $defaultAccent = $countries->count() === 1 ? 'Signature Safaris.' : 'One Journey.';

            return view('livewire.frontend.tours-block', [
                'countries' => $countriesWithMeta,
                'activeCountry' => $activeCountry,
                'activeCountryId' => $this->activeCountryId,
                'activeMeta' => $activeMeta,
                'tours' => $tours,
                'totalTours' => $totalTours,
                'totalPages' => $totalPages,
                'tourPage' => $this->tourPage,
                'perPage' => $perPage,
                'hasPrevPage' => $this->tourPage > 1,
                'hasNextPage' => $this->tourPage < $totalPages,
                'isMultiCountry' => true,
                'heading' => $this->heading ?: $defaultHeading,
                'headingAccent' => $this->headingAccent ?: $defaultAccent,
                'subheading' => $this->subheading ?: __("Choose a destination to explore Africa's iconic safaris and signature experiences"),
            ]);
        }

        // Standard Single-country or Category Grid fallback
        $categories = collect();
        if ($this->categorySlug !== 'all' && !empty($this->categorySlug)) {
            $slugs = explode(',', $this->categorySlug);
            $categories = TourCategory::whereIn('slug', $slugs)
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        $query = Tour::with(['category', 'destinations']);

        if ($this->activeCategoryTab && $this->activeCategoryTab !== 'all') {
            $query->whereHas('category', function($q) {
                $q->where('slug', $this->activeCategoryTab);
            });
        } elseif ($this->categorySlug !== 'all') {
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

        $selectedCountryIds = self::parseCountryIds($this->countryId);
        if (!empty($selectedCountryIds)) {
            $countryFilter = self::toursForCountriesQuery($selectedCountryIds)->reorder()->select('tours.id')->toBase();
            $query->whereIn('id', $countryFilter);
        }

        $limit = (int) preg_replace('/[^0-9]/', '', (string) $this->limit) ?: 6;
        $tours = $query->take($limit)->get();
        $isMultiCountry = false;

        return view('livewire.frontend.tours-block', compact('tours', 'categories', 'isMultiCountry'));
    }
}
