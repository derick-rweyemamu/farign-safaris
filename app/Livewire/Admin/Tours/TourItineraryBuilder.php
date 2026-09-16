<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\Country;
use App\Models\City;
use App\Models\Destination;
use App\Models\Accommodation;
use App\Models\MealPlan;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\Animal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

#[Layout('admin::layouts.app')]
#[Title('Tour Itinerary Builder')]
class TourItineraryBuilder extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Tour $tour;
    public $currency_symbol = '$';
    
    // Dropdown Data
    public $countries;
    public $filteredCities;
    public $destinations;          // all destinations (used as fallback)
    public $filteredDestinations;  // filtered by selected city
    public $accommodations;        // all accommodations (used as fallback)
    public $filteredAccommodations; // filtered by selected city
    public $mealPlans;
    public $availableActivities;
    public $transports;
    public $allAnimals;

    // Country / City filter state
    public $selectedCountryId = '';
    public $selectedCityId    = '';

    // Accommodation Country / City filter state
    public $selectedAccCountryId = '';
    public $selectedAccCityId    = '';
    public $filteredAccCities;

    // Active Edit Day
    public $activeDayId = null;

    public $dayForm = [
        'day_number' => 1,
        'title' => '',
        'description' => '',
        'location' => '',
        'accommodation' => '',
        'accommodation_id' => '',
        'accommodation_amount' => '',
        'meals' => '',
        'meal_plan_id' => '',
        'meal_plan_amount' => '',
        'adult_price' => '',
        'child_price' => '',
        'selected_activities' => [],
        'activity_amounts' => [],
        'additional_activities' => [],
        'selected_transports' => [],
        'transport_amounts' => [],
        'animals' => [],
        'featured_image' => null,
        'existing_image' => null,
    ];

    public function mount($id)
    {
        $this->authorize('edit product'); // Reuse product permissions or tour specific ones
        $this->tour = Tour::with('itineraries')->findOrFail($id);
        $this->loadDropdownData();

        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        $this->currency_symbol = $defaultCurrency?->symbol ?? '$';
    }

    public function loadDropdownData()
    {
        $this->countries              = Country::orderBy('name')->get();
        $this->filteredCities         = collect();
        $this->destinations           = Destination::where('is_active', true)->orderBy('name')->get();
        $this->filteredDestinations   = collect();
        $this->accommodations         = Accommodation::with('city')->where('is_active', true)->orderBy('name')->get();
        $this->filteredAccommodations = collect();
        $this->filteredAccCities      = collect();
        $this->mealPlans              = MealPlan::where('is_active', true)->orderBy('name')->get();
        $this->availableActivities    = Activity::where('is_active', true)->orderBy('name')->get();
        $this->transports             = Transport::where('is_active', true)->orderBy('name')->get();
        $this->allAnimals             = Animal::orderBy('name')->get();
    }

    public function updatedSelectedCountryId($value)
    {
        $this->selectedCityId       = '';
        $this->filteredDestinations = collect();

        if ($value) {
            $this->filteredCities = City::where('country_id', $value)->orderBy('name')->get();
        } else {
            $this->filteredCities = collect();
        }

        // Auto-sync Accommodation Country if not explicitly set
        if (!$this->selectedAccCountryId) {
            $this->selectedAccCountryId   = $value;
            $this->filteredAccCities      = $this->filteredCities;
            $this->filteredAccommodations = $value ? Accommodation::with('city')->where('is_active', true)
                ->whereHas('city', fn($q) => $q->where('country_id', $value))
                ->orderBy('name')->get() : collect();
        }
    }

    public function updatedSelectedCityId($value)
    {
        $this->filteredDestinations = collect();

        if ($value) {
            $this->filteredDestinations = Destination::where('is_active', true)
                ->where('city_id', $value)->orderBy('name')->get();

            // Auto-sync Accommodation City if not explicitly set
            if (!$this->selectedAccCityId) {
                $this->selectedAccCityId      = $value;
                $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)
                    ->where('city_id', $value)->orderBy('name')->get();
            }
        }
    }

    public function updatedSelectedAccCountryId($value)
    {
        $this->selectedAccCityId      = '';
        $this->filteredAccommodations = collect();

        if ($value) {
            $this->filteredAccCities      = City::where('country_id', $value)->orderBy('name')->get();
            $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)
                ->whereHas('city', fn($q) => $q->where('country_id', $value))
                ->orderBy('name')->get();
        } else {
            $this->filteredAccCities      = collect();
            $this->filteredAccommodations = collect();
        }
    }

    public function updatedSelectedAccCityId($value)
    {
        $this->filteredAccommodations = collect();

        if ($value) {
            $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)
                ->where('city_id', $value)->orderBy('name')->get();
        } elseif ($this->selectedAccCountryId) {
            $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)
                ->whereHas('city', fn($q) => $q->where('country_id', $this->selectedAccCountryId))
                ->orderBy('name')->get();
        }
    }

    public function addDay()
    {
        if ($this->tour->duration_days && $this->tour->itineraries()->count() >= $this->tour->duration_days) {
            $this->dispatch('notify', message: __('You have reached the maximum number of days defined for this tour.'), type: 'error');
            return;
        }

        $nextDayNumber = $this->tour->itineraries()->max('day_number') + 1;
        $day = $this->tour->itineraries()->create([
            'day_number' => $nextDayNumber,
            'title' => 'Arrival / Purpose of Day',
            'description' => ''
        ]);
        
        $this->tour->load('itineraries');
        $this->editDay($day->id);
    }

    public function editDay($id)
    {
        $day = TourItinerary::with('pivotActivities', 'transports')->findOrFail($id);
        $this->activeDayId = $day->id;
        $day->load('animals');

        // Reset filter states
        $this->selectedCountryId      = '';
        $this->selectedCityId         = '';
        $this->filteredCities         = collect();
        $this->filteredDestinations   = collect();

        $this->selectedAccCountryId   = '';
        $this->selectedAccCityId      = '';
        $this->filteredAccCities      = collect();
        $this->filteredAccommodations = collect();

        // 1. Try populating Destination location filters if location is set
        if (!empty($day->location)) {
            $dest = Destination::with('city.country')->where('name', 'LIKE', '%' . $day->location . '%')->first();
            if ($dest && $dest->city) {
                $this->selectedCountryId    = $dest->city->country_id;
                $this->filteredCities       = City::where('country_id', $dest->city->country_id)->orderBy('name')->get();
                $this->selectedCityId       = $dest->city_id;
                $this->filteredDestinations = Destination::where('is_active', true)->where('city_id', $dest->city_id)->orderBy('name')->get();
            }
        }

        // 2. Populate Accommodation location filters if accommodation_id is saved
        if ($day->accommodation_id) {
            $acc = Accommodation::with('city.country')->find($day->accommodation_id);
            if ($acc && $acc->city) {
                $this->selectedAccCountryId   = $acc->city->country_id;
                $this->filteredAccCities      = City::where('country_id', $acc->city->country_id)->orderBy('name')->get();
                $this->selectedAccCityId      = $acc->city_id;
                $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)->where('city_id', $acc->city_id)->orderBy('name')->get();
            }
        } elseif ($this->selectedCityId) {
            // Default Accommodation location filters to Destination city
            $this->selectedAccCountryId   = $this->selectedCountryId;
            $this->filteredAccCities      = $this->filteredCities;
            $this->selectedAccCityId      = $this->selectedCityId;
            $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)->where('city_id', $this->selectedCityId)->orderBy('name')->get();
        }

        $this->dayForm = [
            'day_number'      => $day->day_number,
            'title'           => $day->title,
            'description'     => $day->description,
            'location'        => $day->location,
            'accommodation'   => $day->accommodation,
            'accommodation_id'=> $day->accommodation_id,
            'accommodation_amount' => $day->accommodation_amount,
            'meals'           => $day->meals,
            'meal_plan_id'    => $day->meal_plan_id,
            'meal_plan_amount' => $day->meal_plan_amount,
            'adult_price'     => $day->adult_price,
            'child_price'     => $day->child_price,
            'selected_activities' => $day->pivotActivities ? $day->pivotActivities->filter(fn($a) => is_null($a->pivot->amount) || $a->pivot->amount <= 0)->pluck('id')->toArray() : [],
            'activity_amounts' => $day->pivotActivities ? $day->pivotActivities->filter(fn($a) => !is_null($a->pivot->amount) && $a->pivot->amount > 0)->pluck('pivot.amount', 'id')->toArray() : [],
            'additional_activities' => $day->pivotActivities ? $day->pivotActivities->filter(fn($a) => !is_null($a->pivot->amount) && $a->pivot->amount > 0)->pluck('id')->toArray() : [],
            'selected_transports' => $day->transports ? $day->transports->pluck('id')->toArray() : [],
            'transport_amounts' => $day->transports ? $day->transports->pluck('pivot.amount', 'id')->toArray() : [],
            'animals'         => $day->animals ? $day->animals->pluck('id')->toArray() : [],
            'featured_image'  => null,
            'existing_image'  => $day->featured_image
        ];

        $this->dispatch('quill-update', content: $day->description);
    }

    public function saveDay()
    {
        $this->validate([
            'dayForm.day_number' => [
                'required', 
                'integer', 
                'min:1',
                Rule::unique('tour_itineraries', 'day_number')
                    ->where('tour_id', $this->tour->id)
                    ->ignore($this->activeDayId)
            ],
            'dayForm.title' => 'required|string|max:255',
            'dayForm.description' => 'nullable|string',
            'dayForm.location' => 'nullable|string',
            'dayForm.accommodation' => 'nullable|string',
            'dayForm.accommodation_id' => 'nullable|exists:accommodations,id',
            'dayForm.accommodation_amount' => 'nullable|numeric|min:0',
            'dayForm.meals' => 'nullable|string',
            'dayForm.meal_plan_id' => 'nullable|exists:meal_plans,id',
            'dayForm.meal_plan_amount' => 'nullable|numeric|min:0',
            'dayForm.adult_price' => 'nullable|numeric|min:0',
            'dayForm.child_price' => 'nullable|numeric|min:0',
            'dayForm.selected_activities' => 'nullable|array',
            'dayForm.selected_transports' => 'nullable|array',
            'dayForm.animals' => 'nullable|array',
            'dayForm.featured_image' => 'nullable|string',
        ]);

        $day = TourItinerary::findOrFail($this->activeDayId);
        
        $day->update([
            'day_number' => $this->dayForm['day_number'],
            'title' => $this->dayForm['title'],
            'description' => $this->dayForm['description'],
            'location' => empty($this->dayForm['location']) ? null : $this->dayForm['location'],
            'accommodation' => empty($this->dayForm['accommodation']) ? null : $this->dayForm['accommodation'],
            'accommodation_id' => empty($this->dayForm['accommodation_id']) ? null : $this->dayForm['accommodation_id'],
            'accommodation_amount' => empty($this->dayForm['accommodation_amount']) ? null : $this->dayForm['accommodation_amount'],
            'meals' => empty($this->dayForm['meals']) ? null : $this->dayForm['meals'],
            'meal_plan_id' => empty($this->dayForm['meal_plan_id']) ? null : $this->dayForm['meal_plan_id'],
            'meal_plan_amount' => empty($this->dayForm['meal_plan_amount']) ? null : $this->dayForm['meal_plan_amount'],
            'adult_price' => empty($this->dayForm['adult_price']) ? null : $this->dayForm['adult_price'],
            'child_price' => empty($this->dayForm['child_price']) ? null : $this->dayForm['child_price'],
        ]);

        if (!empty($this->dayForm['featured_image'])) {
            $imagePath = is_string($this->dayForm['featured_image']) 
                ? $this->dayForm['featured_image'] 
                : $this->dayForm['featured_image']->store('tour-itineraries', 'public');
                
            $day->update([
                'featured_image' => $imagePath
            ]);
        } elseif (empty($this->dayForm['existing_image'])) {
            $day->update([
                'featured_image' => null
            ]);
        }

        // Included activities (no price)
        $activitySync = [];
        foreach ($this->dayForm['selected_activities'] ?? [] as $actId) {
            $activitySync[$actId] = ['amount' => null];
        }
        // Additional/optional activities (with price)
        foreach ($this->dayForm['additional_activities'] ?? [] as $actId) {
            $amount = isset($this->dayForm['activity_amounts'][$actId]) && $this->dayForm['activity_amounts'][$actId] !== '' ? $this->dayForm['activity_amounts'][$actId] : null;
            $activitySync[$actId] = ['amount' => $amount];
        }
        $day->pivotActivities()->sync($activitySync);

        $transportSync = [];
        foreach ($this->dayForm['selected_transports'] as $transId) {
            $amount = isset($this->dayForm['transport_amounts'][$transId]) && $this->dayForm['transport_amounts'][$transId] !== '' ? $this->dayForm['transport_amounts'][$transId] : null;
            $transportSync[$transId] = ['amount' => $amount];
        }
        $day->transports()->sync($transportSync);

        if (isset($this->dayForm['animals'])) {
            $day->animals()->sync($this->dayForm['animals']);
        }

        $this->tour->load('itineraries');
        $this->activeDayId = null;
        $this->dispatch('notify', message: __('Day details saved successfully!'), type: 'success');
    }

    public function deleteDay($id)
    {
        $dayToDelete = TourItinerary::findOrFail($id);
        $deletedDayNumber = $dayToDelete->day_number;
        
        $dayToDelete->delete();

        // Re-sequence the remaining days to fill the gap
        TourItinerary::where('tour_id', $this->tour->id)
            ->where('day_number', '>', $deletedDayNumber)
            ->decrement('day_number');

        $this->tour->load('itineraries');
        if ($this->activeDayId == $id) {
            $this->activeDayId = null;
        }
        $this->dispatch('notify', message: __('Day removed and schedule re-ordered!'), type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.tours.tour-itinerary-builder');
    }
}
