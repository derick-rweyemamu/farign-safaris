<?php

namespace App\Livewire\Admin\Itineraries;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\Destination;
use App\Models\Accommodation;
use App\Models\MealPlan;
use App\Models\Activity;
use App\Models\Transport;
use App\Models\Client;
use App\Models\Product;
use App\Models\Country;
use App\Models\City;
use App\Models\Animal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Itinerary Builder')]
class ItineraryBuilder extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Itinerary $itinerary;
    public $activeTab = 'days'; // details, days, pricing
    
    public $included = [];
    public $excluded = [];
    
    // Dropdown Data
    public $destinations;
    public $accommodations;
    public $mealPlans;
    public $activities;
    public $transports;
    public $clients;
    public $products;
    public $allAnimals;
    public $countries;
    public $filteredCities;
    public $filteredDestinations;
    public $filteredAccommodations;
    public $accommodationRates = [];
    public $currencies;

    // Country / City filter state
    public $selectedCountryId = '';
    public $selectedCityId    = '';

    // Accommodation Country / City filter state
    public $selectedAccCountryId = '';
    public $selectedAccCityId    = '';
    public $filteredAccCities;

    // Active Edit Day
    public $activeDayId = null;

    public function rules()
    {
        return [
            'itinerary.title' => 'required|string|max:255',
            'itinerary.reference_number' => 'nullable|string',
            'itinerary.client_id' => 'nullable|integer',
            'itinerary.product_id' => 'nullable|integer',
            'itinerary.client_name' => 'nullable|string',
            'itinerary.client_email' => 'nullable|email',
            'itinerary.start_date' => 'nullable|date',
            'itinerary.end_date' => 'nullable|date',
            'itinerary.adults' => 'nullable|integer',
            'itinerary.children' => 'nullable|integer',
            'itinerary.price_per_adult' => 'nullable|numeric',
            'itinerary.price_per_child' => 'nullable|numeric',
            'itinerary.total_price' => 'nullable|numeric',
            'itinerary.currency' => 'nullable|string',
            'included' => 'nullable|array',
            'excluded' => 'nullable|array',
            'itinerary.status' => 'nullable|string',
            'itinerary.duration_days' => 'nullable|integer',
        ];
    }
    public $dayForm = [
        'day' => '',
        'date' => '',
        'destination_id' => '',
        'location' => '',
        'description' => '',
        'accommodation' => '',
        'accommodation_id' => '',
        'room_type' => '',
        'season' => '',
        'class' => '',
        'accommodation_amount' => '',
        'meals' => '',
        'meal_plan_id' => '',
        'meal_plan_amount' => '',
        'adult_price' => '',
        'child_price' => '',
        'selected_activities' => [],
        'additional_activities' => [],
        'additional_activity_amounts' => [],
        'selected_transports' => [],
        'transport_amounts' => [],
        'animals' => [],
        'featured_image' => null,
        'existing_image' => null,
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->authorize('edit itineraries');
            $this->itinerary = Itinerary::with('days.activities', 'days.transports')->findOrFail($id);
        } else {
            $this->authorize('create itineraries');
            // Create a new draft instantly
            $this->itinerary = Itinerary::create([
                'title' => 'New Safari Itinerary',
                'status' => 'draft',
                'included' => [],
                'excluded' => [],
            ]);
            return redirect()->route('itineraries.builder', ['id' => $this->itinerary->id]);
        }

        $this->included = $this->itinerary->included ?? [];
        $this->excluded = $this->itinerary->excluded ?? [];

        $this->loadDropdownData();
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
        $this->activities             = Activity::where('is_active', true)->orderBy('name')->get();
        $this->transports             = Transport::where('is_active', true)->orderBy('name')->get();
        $this->clients                = Client::orderBy('name')->get();
        $this->products               = Product::where('is_active', true)->orderBy('name')->get();
        $this->allAnimals             = Animal::orderBy('name')->get();
        $this->currencies             = \App\Models\Currency::active()->orderBy('code')->get();
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

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    // -- Details & Pricing Updates --
    public function updatedIncluded()
    {
        $this->itinerary->included = $this->included;
        $this->itinerary->save();
    }

    public function updatedExcluded()
    {
        $this->itinerary->excluded = $this->excluded;
        $this->itinerary->save();
    }

    public function updatedItineraryClientId($value)
    {
        if ($value) {
            $client = Client::find($value);
            if ($client) {
                $this->itinerary->client_name = $client->name;
                $this->itinerary->client_email = $client->email;
                $this->itinerary->save();
            }
        }
    }

    public function updatedItineraryProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->itinerary->title = $product->name;
                $this->itinerary->duration_days = $product->duration_days;
                $this->itinerary->save();
            }
        }
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'itinerary.')) {
            $this->itinerary->save();
            
            if (in_array($property, ['itinerary.price_per_adult', 'itinerary.adults', 'itinerary.price_per_child', 'itinerary.children'])) {
                $this->calculateTotal();
            }
        } elseif ($property === 'dayForm.destination_id') {
            if ($value) {
                $dest = \App\Models\Destination::find($value);
                if ($dest) {
                    $this->dayForm['location'] = $dest->name;
                }
            }
        } elseif ($property === 'dayForm.accommodation_id') {
            if ($value) {
                $acc = \App\Models\Accommodation::with('rates')->find($value);
                $this->accommodationRates = $acc ? $acc->rates->toArray() : [];
                $this->dayForm['accommodation'] = $acc ? $acc->name : '';
            } else {
                $this->accommodationRates = [];
                $this->dayForm['accommodation'] = '';
            }
            $this->dayForm['room_type'] = '';
            $this->dayForm['season'] = '';
            $this->dayForm['class'] = '';
            $this->dayForm['accommodation_amount'] = '';
        } elseif (in_array($property, ['dayForm.room_type', 'dayForm.season', 'dayForm.class'])) {
            $this->autofillAccommodationPrice();
        } elseif ($property === 'dayForm.meal_plan_id') {
            // Auto-fill meal plan amount from system price when a plan is selected
            if ($value) {
                $plan = \App\Models\MealPlan::find($value);
                if ($plan && $plan->price > 0) {
                    $this->dayForm['meal_plan_amount'] = $plan->price;
                }
            } else {
                $this->dayForm['meal_plan_amount'] = '';
            }
        } elseif ($property === 'dayForm.selected_transports') {
            // Auto-fill transport amount from system price for newly added transports
            $amounts = $this->dayForm['transport_amounts'] ?? [];
            foreach ((array) $value as $transId) {
                if (!isset($amounts[$transId]) || $amounts[$transId] === '' || $amounts[$transId] === null) {
                    $transport = \App\Models\Transport::find($transId);
                    if ($transport && $transport->price > 0) {
                        $amounts[$transId] = $transport->price;
                    }
                }
            }
            $this->dayForm['transport_amounts'] = $amounts;
        }
    }

    public function calculateTotal()
    {
        $adultTotal = (float)($this->itinerary->price_per_adult ?? 0) * (int)($this->itinerary->adults ?? 0);
        $childTotal = (float)($this->itinerary->price_per_child ?? 0) * (int)($this->itinerary->children ?? 0);
        $this->itinerary->total_price = $adultTotal + $childTotal;
        $this->itinerary->save();
    }

    public function autofillAccommodationPrice()
    {
        if (empty($this->accommodationRates)) return;

        $rt = $this->dayForm['room_type'] ?? '';
        $s = $this->dayForm['season'] ?? '';
        $c = $this->dayForm['class'] ?? '';

        if ($rt && $s && $c) {
            foreach ($this->accommodationRates as $rate) {
                if ($rate['room_type'] == $rt && $rate['season'] == $s && $rate['class'] == $c) {
                    $this->dayForm['accommodation_amount'] = $rate['rack'];
                    return;
                }
            }
        }
    }

    public function clearMealPlan()
    {
        $this->dayForm['meal_plan_id'] = null;
        $this->dayForm['meal_plan_amount'] = '';
    }

    // -- Day Management --
    public function addDay()
    {
        if ($this->itinerary->duration_days && $this->itinerary->days()->count() >= $this->itinerary->duration_days) {
            $this->dispatch('notify', message: __('You have reached the maximum number of days for this itinerary.'), type: 'error');
            return;
        }

        $dayCount = $this->itinerary->days()->count() + 1;
        $day = $this->itinerary->days()->create([
            'day' => $dayCount,
            'description' => ''
        ]);
        
        $this->itinerary->load('days.activities', 'days.transports');
        $this->editDay($day->id);
    }

    public function editDay($id)
    {
        $day = ItineraryDay::with('activities', 'transports', 'animals')->findOrFail($id);
        $this->activeDayId = $day->id;
        
        // Reset filter states
        $this->selectedCountryId      = '';
        $this->selectedCityId         = '';
        $this->filteredCities         = collect();
        $this->filteredDestinations   = collect();

        $this->selectedAccCountryId   = '';
        $this->selectedAccCityId      = '';
        $this->filteredAccCities      = collect();
        $this->filteredAccommodations = collect();

        // 1. Try populating Destination location filters if location/destination is set
        if (!empty($day->location)) {
            $dest = Destination::with('city.country')->where('name', $day->location)->first();
            if ($dest && $dest->city) {
                $this->selectedCountryId    = $dest->city->country_id;
                $this->filteredCities       = City::where('country_id', $dest->city->country_id)->orderBy('name')->get();
                $this->selectedCityId       = $dest->city_id;
                $this->filteredDestinations = Destination::where('is_active', true)->where('city_id', $dest->city_id)->orderBy('name')->get();
            }
        }

        // 2. Populate Accommodation location filters if accommodation_id is saved
        if ($day->accommodation_id) {
            $acc = Accommodation::with('city.country', 'rates')->find($day->accommodation_id);
            if ($acc && $acc->city) {
                $this->selectedAccCountryId   = $acc->city->country_id;
                $this->filteredAccCities      = City::where('country_id', $acc->city->country_id)->orderBy('name')->get();
                $this->selectedAccCityId      = $acc->city_id;
                $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)->where('city_id', $acc->city_id)->orderBy('name')->get();
            }
            if ($acc) {
                $this->accommodationRates = $acc->rates->toArray();
            }
        } elseif ($this->selectedCityId) {
            // Default Accommodation location filters to Destination city
            $this->selectedAccCountryId   = $this->selectedCountryId;
            $this->filteredAccCities      = $this->filteredCities;
            $this->selectedAccCityId      = $this->selectedCityId;
            $this->filteredAccommodations = Accommodation::with('city')->where('is_active', true)->where('city_id', $this->selectedCityId)->orderBy('name')->get();
            $this->accommodationRates     = [];
        } else {
            $this->accommodationRates     = [];
        }

        $selectedActivities = [];
        $additionalActivities = [];
        $additionalActivityAmounts = [];
        foreach ($day->activities as $act) {
            if ($act->pivot->amount > 0) {
                $additionalActivities[] = $act->id;
                $additionalActivityAmounts[$act->id] = $act->pivot->amount;
            } else {
                $selectedActivities[] = $act->id;
            }
        }

        $this->dayForm = [
            'day' => preg_replace('/[^0-9]/', '', $day->day),
            'date' => $day->date ? $day->date->format('Y-m-d') : '',
            'destination_id' => $day->destination_id,
            'location' => $day->location,
            'description' => $day->description,
            'accommodation' => $day->accommodation,
            'accommodation_id' => $day->accommodation_id,
            'room_type' => '',
            'season' => '',
            'class' => '',
            'accommodation_amount' => $day->accommodation_amount,
            'meals' => $day->meals,
            'meal_plan_id' => $day->meal_plan_id,
            'meal_plan_amount' => $day->meal_plan_amount,
            'adult_price' => $day->adult_price,
            'child_price' => $day->child_price,
            'selected_activities' => $selectedActivities,
            'additional_activities' => $additionalActivities,
            'additional_activity_amounts' => $additionalActivityAmounts,
            'selected_transports' => $day->transports->pluck('id')->toArray(),
            'transport_amounts' => $day->transports->pluck('pivot.amount', 'id')->toArray(),
            'animals' => $day->animals ? $day->animals->pluck('id')->toArray() : [],
            'featured_image' => null,
            'existing_image' => $day->featured_image
        ];
        
        $this->dispatch('quill-update', content: $day->description);
    }

    public function saveDay()
    {
        $day = ItineraryDay::findOrFail($this->activeDayId);
        
        $day->update([
            'day' => $this->dayForm['day'],
            'date' => empty($this->dayForm['date']) ? null : $this->dayForm['date'],
            'destination_id' => empty($this->dayForm['destination_id']) ? null : $this->dayForm['destination_id'],
            'location' => empty($this->dayForm['location']) ? null : $this->dayForm['location'],
            'description' => $this->dayForm['description'],
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
                : $this->dayForm['featured_image']->store('itinerary-days', 'public');
                
            $day->update([
                'featured_image' => $imagePath
            ]);
        } elseif (empty($this->dayForm['existing_image'])) {
            $day->update([
                'featured_image' => null
            ]);
        }

        if (isset($this->dayForm['animals'])) {
            $day->animals()->sync($this->dayForm['animals']);
        }

        // Sync Pivots with amounts
        $activitySync = [];
        foreach ($this->dayForm['selected_activities'] as $actId) {
            $activitySync[$actId] = ['amount' => null];
        }
        foreach ($this->dayForm['additional_activities'] as $actId) {
            $amount = isset($this->dayForm['additional_activity_amounts'][$actId]) && $this->dayForm['additional_activity_amounts'][$actId] !== '' ? $this->dayForm['additional_activity_amounts'][$actId] : null;
            $activitySync[$actId] = ['amount' => $amount];
        }
        $day->activities()->sync($activitySync);

        $transportSync = [];
        foreach ($this->dayForm['selected_transports'] as $transId) {
            $amount = isset($this->dayForm['transport_amounts'][$transId]) && $this->dayForm['transport_amounts'][$transId] !== '' ? $this->dayForm['transport_amounts'][$transId] : null;
            $transportSync[$transId] = ['amount' => $amount];
        }
        $day->transports()->sync($transportSync);

        $this->itinerary->load('days.activities', 'days.transports');
        $this->activeDayId = null;
        $this->dispatch('notify', message: __('Day details saved successfully!'), type: 'success');
    }

    public function deleteDay($id)
    {
        ItineraryDay::findOrFail($id)->delete();
        $this->itinerary->load('days.activities', 'days.transports');
        if ($this->activeDayId == $id) {
            $this->activeDayId = null;
        }
        $this->dispatch('notify', message: __('Day removed!'), type: 'success');
    }

    // -- Generate & Actions --
    public function updateStatus($status)
    {
        $this->itinerary->update(['status' => $status]);
        $this->dispatch('notify', message: __('Status updated to') . ucfirst($status), type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.itineraries.itinerary-builder');
    }
}
