<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Destination;
use App\Models\Accommodation;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\MealPlan;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Tours')]
class TourList extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    public $currency_symbol = '$';

    public function mount()
    {
        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        $this->currency_symbol = $defaultCurrency?->symbol ?? '$';
    }

    #[Url]
    public $category = '';

    #[Url]
    public $tourType = '';

    #[Url]
    public int $perPage = 10;

    public $tour_id, $tour_category_id, $tour_type_id, $tour_route_id, $name, $slug, $description, $featured_image, $existingImage;
    public $included_features = [], $excluded_features = [];
    public $min_age = 8, $max_age = 90, $accepts_children = true, $children_free_under_age = 6, $accepts_solo_travelers = true;
    public $adult_price, $child_price;
    public $price_2_pax, $price_3_pax, $price_4_pax, $price_5_pax, $price_6_pax, $price_7_plus_pax;
    public $duration_days, $duration_nights, $difficulty, $seasonal_months = [], $max_guests = 6, $min_guests = 1;
    public $gallery = [], $existingGallery = [];
    public $type = 'Safari';
    public $selectedTags = [];

    // SEO Fields
    public $seo_title;
    public $seo_description;

    public $new_included_feature = '';
    public $new_excluded_feature = '';

    public $selectedDestinations = [];
    public $selectedAccommodations = [];
    public $selectedActivities = [];
    public $selectedTransports = [];
    public $selectedMealPlans = [];

    public bool $shouldUpdateSlug = false;
    public bool $isSlugManuallyEdited = false;
    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedTourType()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        if (!$this->isSlugManuallyEdited && (!$this->isEdit || $this->shouldUpdateSlug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug($value)
    {
        $this->isSlugManuallyEdited = !empty($value);
    }

    public function updatedShouldUpdateSlug($value)
    {
        if ($value) {
            $this->isSlugManuallyEdited = false;
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedDurationDays($value)
    {
        if (is_numeric($value) && $value > 0) {
            $this->duration_nights = max(0, (int)$value - 1);
        } else {
            $this->duration_nights = null;
        }
    }

    public function addIncludedFeature()
    {
        if (!empty(trim($this->new_included_feature))) {
            $this->included_features[] = trim($this->new_included_feature);
            $this->new_included_feature = '';
        }
    }

    public function removeIncludedFeature($index)
    {
        unset($this->included_features[$index]);
        $this->included_features = array_values($this->included_features);
    }

    public function addExcludedFeature()
    {
        if (!empty(trim($this->new_excluded_feature))) {
            $this->excluded_features[] = trim($this->new_excluded_feature);
            $this->new_excluded_feature = '';
        }
    }

    public function removeExcludedFeature($index)
    {
        unset($this->excluded_features[$index]);
        $this->excluded_features = array_values($this->excluded_features);
    }

    public function resetFields()
    {
        $this->tour_id = null;
        $this->tour_category_id = null;
        $this->tour_type_id = null;
        $this->tour_route_id = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->featured_image = null;
        $this->existingImage = null;
        
        $this->included_features = [];
        $this->excluded_features = [];
        $this->new_included_feature = '';
        $this->new_excluded_feature = '';
        
        $this->min_age = 8;
        $this->max_age = 90;
        $this->accepts_children = true;
        $this->children_free_under_age = 6;
        $this->accepts_solo_travelers = true;
        $this->adult_price = null;
        $this->price_2_pax = null;
        $this->price_3_pax = null;
        $this->price_4_pax = null;
        $this->price_5_pax = null;
        $this->price_6_pax = null;
        $this->price_7_plus_pax = null;
        $this->child_price = null;

        $this->duration_days = null;
        $this->duration_nights = null;
        $this->difficulty = '';
        $this->seasonal_months = [];
        $this->max_guests = 6;
        $this->min_guests = 1;
        $this->gallery = [];
        $this->existingGallery = [];

        $this->seo_title = '';
        $this->seo_description = '';

        $this->selectedDestinations = [];
        $this->selectedAccommodations = [];
        $this->selectedActivities = [];
        $this->selectedTransports = [];
        $this->selectedMealPlans = [];
        $this->type = 'Safari';
        $this->selectedTags = [];

        $this->isEdit = false;
        $this->shouldUpdateSlug = false;
        $this->isSlugManuallyEdited = false;
    }

    public function create()
    {
        $this->authorize('create product'); // Assuming reusing product permissions or adjust
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit product');
        $this->resetFields();
        $tour = Tour::findOrFail($id);
        
        $this->tour_id = $tour->id;
        $this->tour_category_id = $tour->tour_category_id;
        $this->tour_type_id = $tour->tour_type_id;
        $this->tour_route_id = $tour->tour_route_id;
        $this->type = $tour->type;
        $this->name = $tour->name;
        $this->slug = $tour->slug;
        $this->description = $tour->description;
        $this->existingImage = $tour->featured_image;

        $this->included_features = is_array($tour->included_features) ? $tour->included_features : [];
        $this->excluded_features = is_array($tour->excluded_features) ? $tour->excluded_features : [];
        
        $this->min_age = $tour->min_age;
        $this->max_age = $tour->max_age;
        $this->accepts_children = $tour->accepts_children;
        $this->children_free_under_age = $tour->children_free_under_age;
        $this->accepts_solo_travelers = $tour->accepts_solo_travelers;
        $this->adult_price = $tour->adult_price;
        $this->price_2_pax = $tour->price_2_pax;
        $this->price_3_pax = $tour->price_3_pax;
        $this->price_4_pax = $tour->price_4_pax;
        $this->price_5_pax = $tour->price_5_pax;
        $this->price_6_pax = $tour->price_6_pax;
        $this->price_7_plus_pax = $tour->price_7_plus_pax;
        $this->child_price = $tour->child_price;

        $this->duration_days = $tour->duration_days;
        $this->duration_nights = $tour->duration_nights;
        $this->difficulty = $tour->difficulty;
        $shortToFull = [
            'jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April',
            'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August',
            'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'
        ];
        $this->seasonal_months = collect($tour->seasonal_months ?? [])
            ->map(function($m) use ($shortToFull) {
                $key = strtolower(substr(trim($m), 0, 3));
                return $shortToFull[$key] ?? trim($m);
            })->toArray();
        $this->max_guests = $tour->max_guests;
        $this->min_guests = $tour->min_guests ?? 1;
        $this->existingGallery = $tour->gallery ?? [];

        $this->selectedDestinations = $tour->destinations->pluck('id')->toArray();
        $this->selectedAccommodations = $tour->accommodations->pluck('id')->toArray();
        $this->selectedActivities = $tour->activities->pluck('id')->toArray();
        $this->selectedTransports = $tour->transports->pluck('id')->toArray();
        $this->selectedMealPlans = $tour->mealPlans->pluck('id')->toArray();
        $this->selectedTags = $tour->tags->pluck('id')->toArray();

        $this->seo_title = $tour->seo?->title ?? '';
        $this->seo_description = $tour->seo?->description ?? '';

        $this->isEdit = true;
        $this->isSlugManuallyEdited = true;
        $this->showModal = true;
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existingGallery[$index])) {
            unset($this->existingGallery[$index]);
            $this->existingGallery = array_values($this->existingGallery);

            if ($this->tour_id) {
                Tour::find($this->tour_id)->update([
                    'gallery' => $this->existingGallery
                ]);
            }
        }
    }

    public function removeTempGalleryImage($index)
    {
        if (isset($this->gallery[$index])) {
            unset($this->gallery[$index]);
            $this->gallery = array_values($this->gallery);
        }
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'tour_category_id' => 'required|exists:tour_categories,id',
            'tour_type_id' => 'required|exists:tour_types,id',
            'tour_route_id' => 'nullable|exists:tour_routes,id',
            'type' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tours,slug,' . $this->tour_id,
            'description' => 'required|string',
            'featured_image' => $this->imageRule('featured_image', 2048),
            
            'min_age' => 'required|integer|min:0',
            'max_age' => 'nullable|integer|min:0',
            'accepts_children' => 'boolean',
            'children_free_under_age' => 'required|integer|min:0',
            'accepts_solo_travelers' => 'boolean',
            'adult_price' => 'nullable|numeric|min:0',
            'price_2_pax' => 'nullable|numeric|min:0',
            'price_3_pax' => 'nullable|numeric|min:0',
            'price_4_pax' => 'nullable|numeric|min:0',
            'price_5_pax' => 'nullable|numeric|min:0',
            'price_6_pax' => 'nullable|numeric|min:0',
            'price_7_plus_pax' => 'nullable|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            
            'duration_days' => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|string|max:50',
            'seasonal_months' => 'nullable|array',
            'max_guests' => 'nullable|integer|min:0',
            'min_guests' => 'required|integer|min:1',
            'gallery.*' => $this->galleryRule(2048),

            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',

            'selectedDestinations' => 'nullable|array',
            'selectedAccommodations' => 'nullable|array',
            'selectedActivities' => 'nullable|array',
            'selectedTransports' => 'nullable|array',
            'selectedMealPlans' => 'nullable|array',
            'selectedTags' => 'nullable|array',
            'selectedTags.*' => 'exists:tour_tags,id',
        ]);

        $tourType = \App\Models\TourType::find($this->tour_type_id);

        $data = [
            'tour_category_id' => $this->tour_category_id,
            'tour_type_id' => $this->tour_type_id,
            'tour_route_id' => $this->tour_route_id ?: null,
            'type' => $tourType ? $tourType->name : 'Safari',
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'overview' => null,
            
            'included_features' => $this->included_features,
            'excluded_features' => $this->excluded_features,
            
            'min_age' => $this->min_age,
            'max_age' => $this->max_age,
            'accepts_children' => $this->accepts_children,
            'children_free_under_age' => $this->children_free_under_age,
            'accepts_solo_travelers' => $this->accepts_solo_travelers,
            'adult_price' => $this->adult_price ?: null,
            'price_2_pax' => $this->price_2_pax ?: null,
            'price_3_pax' => $this->price_3_pax ?: null,
            'price_4_pax' => $this->price_4_pax ?: null,
            'price_5_pax' => $this->price_5_pax ?: null,
            'price_6_pax' => $this->price_6_pax ?: null,
            'price_7_plus_pax' => $this->price_7_plus_pax ?: null,
            'child_price' => $this->child_price ?: null,

            'duration_days' => $this->duration_days ?: null,
            'duration_nights' => $this->duration_nights ?: null,
            'difficulty' => $this->difficulty ?: null,
            'seasonal_months' => !empty($this->seasonal_months) ? array_values(array_filter($this->seasonal_months)) : null,
            'max_guests' => $this->max_guests ?: null,
            'min_guests' => $this->min_guests,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->saveImageField($this->featured_image, 'tours');
        }

        if (!empty($this->gallery)) {
            $galleryPaths = $this->saveGalleryField($this->gallery, 'tours/gallery');
            if ($this->isEdit) {
                $existing = is_array($this->existingGallery) ? $this->existingGallery : [];
                $data['gallery'] = array_merge($existing, $galleryPaths);
            } else {
                $data['gallery'] = $galleryPaths;
            }
        } else if ($this->isEdit) {
            $data['gallery'] = $this->existingGallery;
        }

        if ($this->isEdit) {
            $tour = Tour::find($this->tour_id);
            $tour->update($data);
            $this->dispatch('notify', message: __('Tour updated successfully!'), type: 'success');
        } else {
            $tour = Tour::create($data);
            $this->dispatch('notify', message: __('Tour created successfully!'), type: 'success');
        }

        $tour->destinations()->sync($this->selectedDestinations);
        $tour->accommodations()->sync($this->selectedAccommodations);
        $tour->activities()->sync($this->selectedActivities);
        $tour->transports()->sync($this->selectedTransports);
        $tour->mealPlans()->sync($this->selectedMealPlans);
        $tour->tags()->sync($this->selectedTags);

        if ($this->seo_title || $this->seo_description) {
            $tour->seo()->updateOrCreate([], [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ]);
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete product');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteTour(): void
    {
        $this->authorize('delete product');
        Tour::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Tour deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function duplicate($id)
    {
        $this->authorize('create product');
        
        $original = Tour::with([
            'categories', 'destinations', 'accommodations', 'activities', 'transports', 'mealPlans', 'tags',
            'itineraries.animals', 'itineraries.pivotActivities', 'itineraries.transports'
        ])->findOrFail($id);

        $newTour = $original->replicate();
        $newTour->name = $original->name . ' (Copy)';
        $newTour->slug = \Illuminate\Support\Str::slug($newTour->name) . '-' . time();
        $newTour->save();

        $newTour->categories()->sync($original->categories->pluck('id'));
        $newTour->destinations()->sync($original->destinations->pluck('id'));
        $newTour->accommodations()->sync($original->accommodations->pluck('id'));
        $newTour->activities()->sync($original->activities->pluck('id'));
        $newTour->transports()->sync($original->transports->pluck('id'));
        $newTour->mealPlans()->sync($original->mealPlans->pluck('id'));
        $newTour->tags()->sync($original->tags->pluck('id'));

        foreach ($original->itineraries as $day) {
            $newDay = $day->replicate();
            $newDay->tour_id = $newTour->id;
            $newDay->save();

            if ($day->animals->isNotEmpty()) {
                $newDay->animals()->sync($day->animals->pluck('id'));
            }

            if ($day->pivotActivities->isNotEmpty()) {
                $syncActivities = [];
                foreach ($day->pivotActivities as $act) {
                    $syncActivities[$act->id] = ['amount' => $act->pivot->amount];
                }
                $newDay->pivotActivities()->sync($syncActivities);
            }

            if ($day->transports->isNotEmpty()) {
                $syncTransports = [];
                foreach ($day->transports as $trans) {
                    $syncTransports[$trans->id] = ['amount' => $trans->pivot->amount];
                }
                $newDay->transports()->sync($syncTransports);
            }
        }

        $seo = $original->seo;
        if ($seo) {
            $newTour->seo()->create([
                'title' => $seo->title,
                'description' => $seo->description,
            ]);
        }

        $this->dispatch('notify', message: __('Tour duplicated successfully!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view product');
        $tours = Tour::with(['category'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($query) {
                $query->where('tour_category_id', $this->category);
            })
            ->when($this->tourType, function ($query) {
                $query->where('tour_type_id', $this->tourType);
            })
            ->latest()
            ->paginate($this->perPage);

        $categories = TourCategory::orderBy('sort_order', 'asc')->get();
        $tourTypes = \App\Models\TourType::where('is_active', true)->orderBy('name', 'asc')->get();
        $tourRoutes = \App\Models\TourRoute::where('is_active', true)->orderBy('name', 'asc')->get();
        $destinations = Destination::where('is_active', true)->get();
        $accommodations = Accommodation::where('is_active', true)->get();
        $allActivities = Activity::orderBy('name', 'asc')->get();
        $allTransports = Transport::orderBy('name', 'asc')->get();
        $allMealPlans = MealPlan::orderBy('name', 'asc')->get();
        $allTags = \App\Models\TourTag::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('livewire.admin.tours.tour-list', [
            'tours' => $tours,
            'categories' => $categories,
            'tourTypes' => $tourTypes,
            'tourRoutes' => $tourRoutes,
            'destinations' => $destinations,
            'allAccommodations' => $accommodations,
            'allActivities' => $allActivities,
            'allTransports' => $allTransports,
            'allMealPlans' => $allMealPlans,
            'allTags' => $allTags,
        ]);
    }
}
