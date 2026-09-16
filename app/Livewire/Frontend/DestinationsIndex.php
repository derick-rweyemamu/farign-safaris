<?php

namespace App\Livewire\Frontend;

use App\Models\Country;
use App\Models\Destination;
use Livewire\Component;
use Livewire\WithPagination;

class DestinationsIndex extends Component
{
    use WithPagination;

    /** @var array<int> Selected country IDs for filtering */
    public array $selectedCountries = [];

    /** @var string Text search query */
    public string $search = '';

    protected $queryString = [
        'selectedCountries' => ['as' => 'countries', 'except' => []],
        'search'            => ['as' => 'q',         'except' => ''],
    ];

    public function updatedSelectedCountries(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleCountry(int $countryId): void
    {
        if (in_array($countryId, $this->selectedCountries)) {
            $this->selectedCountries = array_values(array_filter(
                $this->selectedCountries,
                fn ($id) => $id !== $countryId
            ));
        } else {
            $this->selectedCountries[] = $countryId;
        }
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->selectedCountries = [];
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Destination::where('is_active', true)
            ->where('show_on_page', true)
            ->with('city.country');

        // Filter by country
        if (!empty($this->selectedCountries)) {
            $query->whereHas('city.country', function ($q) {
                $q->whereIn('countries.id', $this->selectedCountries);
            });
        }

        // Filter by search
        if (!empty(trim($this->search))) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('tagline', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $destinations = $query->orderBy('name')->paginate(12)->onEachSide(1);

        // Countries that have at least one active destination shown on page
        $countries = Country::whereHas('cities.destinations', function ($q) {
            $q->where('is_active', true)->where('show_on_page', true);
        })->withCount(['cities as destination_count' => function ($q) {
            $q->join('destinations', 'destinations.city_id', '=', 'cities.id')
              ->where('destinations.is_active', true)
              ->where('destinations.show_on_page', true);
        }])->orderBy('name')->get();

        $totalCount = Destination::where('is_active', true)->where('show_on_page', true)->count();

        return view('livewire.frontend.destinations-index', [
            'destinations' => $destinations,
            'countries'    => $countries,
            'totalCount'   => $totalCount,
        ]);
    }
}
