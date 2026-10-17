<?php

namespace App\Livewire\Admin\Destinations;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Destination;
use App\Models\City;
use App\Models\Country;
use App\Models\Activity;
use App\Models\Animal;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Destinations')]
class DestinationList extends Component
{
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $destination_id;
    public $city_id;
    public $selectedCountryId = '';
    public $filteredCities;
    public $name;
    public $tagline;
    public $slug;
    public $description;
    public $featured_image;
    public $existingFeaturedImage;
    public $gallery = [];
    public $existingGallery = [];
    public $is_active = true;
    public $is_featured = false;
    public $show_on_page = true;

    // Concession Fees
    public $concession_fee_adult_high_season = 0.00;
    public $concession_fee_adult_low_season  = 0.00;
    public $concession_fee_child_high_season = 0.00;
    public $concession_fee_child_low_season  = 0.00;

    // Crater Fees
    public $crater_fee_adult_high_season = 0.00;
    public $crater_fee_adult_low_season  = 0.00;
    public $crater_fee_child_high_season = 0.00;
    public $crater_fee_child_low_season  = 0.00;

    // Park Fees
    public $park_fee_adult_high_season = 0.00;
    public $park_fee_adult_low_season  = 0.00;
    public $park_fee_child_high_season = 0.00;
    public $park_fee_child_low_season  = 0.00;

    // Pass Fees
    public $pass_fee_adult_high_season = 0.00;
    public $pass_fee_adult_low_season  = 0.00;
    public $pass_fee_child_high_season = 0.00;
    public $pass_fee_child_low_season  = 0.00;

    // Relationship Selections
    public $selectedActivities = [];
    public $selectedAnimals = [];

    // SEO Fields
    public $seo_title;
    public $seo_description;

    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function resetFields()
    {
        $this->destination_id = null;
        $this->city_id = null;
        $this->selectedCountryId = '';
        $this->filteredCities = collect();
        $this->name = '';
        $this->tagline = '';
        $this->slug = '';
        $this->description = '';
        $this->featured_image = null;
        $this->existingFeaturedImage = null;
        $this->gallery = [];
        $this->existingGallery = [];
        $this->is_active = true;
        $this->is_featured = false;
        $this->show_on_page = true;
        
        $this->concession_fee_adult_high_season = 0.00;
        $this->concession_fee_adult_low_season  = 0.00;
        $this->concession_fee_child_high_season = 0.00;
        $this->concession_fee_child_low_season  = 0.00;
        $this->crater_fee_adult_high_season     = 0.00;
        $this->crater_fee_adult_low_season      = 0.00;
        $this->crater_fee_child_high_season     = 0.00;
        $this->crater_fee_child_low_season      = 0.00;
        $this->park_fee_adult_high_season       = 0.00;
        $this->park_fee_adult_low_season        = 0.00;
        $this->park_fee_child_high_season       = 0.00;
        $this->park_fee_child_low_season        = 0.00;
        $this->pass_fee_adult_high_season       = 0.00;
        $this->pass_fee_adult_low_season        = 0.00;
        $this->pass_fee_child_high_season       = 0.00;
        $this->pass_fee_child_low_season        = 0.00;

        $this->selectedActivities = [];
        $this->selectedAnimals    = [];

        $this->seo_title = '';
        $this->seo_description = '';
        $this->isEdit = false;
    }

    public function updatedSelectedCountryId($value)
    {
        $this->city_id = null;
        $this->filteredCities = $value
            ? City::where('country_id', $value)->orderBy('name')->get()
            : collect();
    }

    // ── Auto-calculate child fees at 50% of adult fees ──────────────────────

    public function updatedConcessionFeeAdultHighSeason($value)
    {
        $this->concession_fee_child_high_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedConcessionFeeAdultLowSeason($value)
    {
        $this->concession_fee_child_low_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedCraterFeeAdultHighSeason($value)
    {
        $this->crater_fee_child_high_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedCraterFeeAdultLowSeason($value)
    {
        $this->crater_fee_child_low_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedParkFeeAdultHighSeason($value)
    {
        $this->park_fee_child_high_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedParkFeeAdultLowSeason($value)
    {
        $this->park_fee_child_low_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedPassFeeAdultHighSeason($value)
    {
        $this->pass_fee_child_high_season = round(floatval($value) * 0.5, 2);
    }

    public function updatedPassFeeAdultLowSeason($value)
    {
        $this->pass_fee_child_low_season = round(floatval($value) * 0.5, 2);
    }

    public function create()
    {
        $this->authorize('create destinations');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit destinations');
        $this->resetFields();

        $destination = Destination::with('city.country', 'activities', 'animals')->findOrFail($id);
        $this->destination_id = $destination->id;
        $this->city_id = $destination->city_id;

        // Pre-populate country filter
        if ($destination->city && $destination->city->country) {
            $this->selectedCountryId = $destination->city->country_id;
            $this->filteredCities = City::where('country_id', $destination->city->country_id)->orderBy('name')->get();
        }
        $this->name = $destination->name;
        $this->tagline = $destination->tagline;
        $this->slug = $destination->slug;
        $this->description = $destination->description;
        $this->is_active = $destination->is_active;
        $this->is_featured = $destination->is_featured;
        $this->show_on_page = $destination->show_on_page;
        $this->existingFeaturedImage = $destination->featured_image;
        $this->existingGallery = $destination->gallery ?? [];

        // Fee fields
        $this->concession_fee_adult_high_season = $destination->concession_fee_adult_high_season ?? 0.00;
        $this->concession_fee_adult_low_season  = $destination->concession_fee_adult_low_season  ?? 0.00;
        $this->concession_fee_child_high_season = $destination->concession_fee_child_high_season ?? 0.00;
        $this->concession_fee_child_low_season  = $destination->concession_fee_child_low_season  ?? 0.00;

        $this->crater_fee_adult_high_season     = $destination->crater_fee_adult_high_season     ?? 0.00;
        $this->crater_fee_adult_low_season      = $destination->crater_fee_adult_low_season      ?? 0.00;
        $this->crater_fee_child_high_season     = $destination->crater_fee_child_high_season     ?? 0.00;
        $this->crater_fee_child_low_season      = $destination->crater_fee_child_low_season      ?? 0.00;

        $this->park_fee_adult_high_season       = $destination->park_fee_adult_high_season       ?? 0.00;
        $this->park_fee_adult_low_season        = $destination->park_fee_adult_low_season        ?? 0.00;
        $this->park_fee_child_high_season       = $destination->park_fee_child_high_season       ?? 0.00;
        $this->park_fee_child_low_season        = $destination->park_fee_child_low_season        ?? 0.00;

        $this->pass_fee_adult_high_season       = $destination->pass_fee_adult_high_season       ?? 0.00;
        $this->pass_fee_adult_low_season        = $destination->pass_fee_adult_low_season        ?? 0.00;
        $this->pass_fee_child_high_season       = $destination->pass_fee_child_high_season       ?? 0.00;
        $this->pass_fee_child_low_season        = $destination->pass_fee_child_low_season        ?? 0.00;

        $this->seo_title = $destination->seo?->title ?? '';
        $this->seo_description = $destination->seo?->description ?? '';

        $this->selectedActivities = $destination->activities->pluck('id')->toArray();
        $this->selectedAnimals    = $destination->animals->pluck('id')->toArray();

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existingGallery[$index])) {
            unset($this->existingGallery[$index]);
            $this->existingGallery = array_values($this->existingGallery);

            if ($this->destination_id) {
                Destination::find($this->destination_id)->update([
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
        $this->authorize($this->isEdit ? 'edit destinations' : 'create destinations');
        
        $this->validate([
            'city_id'                          => 'required|exists:cities,id',
            'name'                             => 'required|string|max:255',
            'tagline'                          => 'nullable|string|max:255',
            'slug'                             => [
                'required',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->ignore($this->destination_id)->whereNull('deleted_at'),
            ],
            'description'                      => 'nullable|string',
            'is_active'                        => 'boolean',
            'is_featured'                      => 'boolean',
            'show_on_page'                     => 'boolean',
            'featured_image'                   => $this->imageRule('featured_image', 600),
            'gallery.*'                        => $this->galleryRule(600),
            'concession_fee_adult_high_season' => 'nullable|numeric|min:0',
            'concession_fee_adult_low_season'  => 'nullable|numeric|min:0',
            'concession_fee_child_high_season' => 'nullable|numeric|min:0',
            'concession_fee_child_low_season'  => 'nullable|numeric|min:0',
            'crater_fee_adult_high_season'     => 'nullable|numeric|min:0',
            'crater_fee_adult_low_season'      => 'nullable|numeric|min:0',
            'crater_fee_child_high_season'     => 'nullable|numeric|min:0',
            'crater_fee_child_low_season'      => 'nullable|numeric|min:0',
            'park_fee_adult_high_season'       => 'nullable|numeric|min:0',
            'park_fee_adult_low_season'        => 'nullable|numeric|min:0',
            'park_fee_child_high_season'       => 'nullable|numeric|min:0',
            'park_fee_child_low_season'        => 'nullable|numeric|min:0',
            'pass_fee_adult_high_season'       => 'nullable|numeric|min:0',
            'pass_fee_adult_low_season'        => 'nullable|numeric|min:0',
            'pass_fee_child_high_season'       => 'nullable|numeric|min:0',
            'pass_fee_child_low_season'        => 'nullable|numeric|min:0',
            'selectedActivities'               => 'nullable|array',
            'selectedActivities.*'             => 'exists:activities,id',
            'selectedAnimals'                  => 'nullable|array',
            'selectedAnimals.*'                => 'exists:animals,id',
        ]);

        $data = [
            'city_id'                          => $this->city_id,
            'name'                             => $this->name,
            'tagline'                          => $this->tagline,
            'slug'                             => $this->slug,
            'description'                      => $this->description,
            'is_active'                        => $this->is_active,
            'is_featured'                      => $this->is_featured,
            'show_on_page'                     => $this->show_on_page,
            'concession_fee_adult_high_season' => $this->concession_fee_adult_high_season ?? 0.00,
            'concession_fee_adult_low_season'  => $this->concession_fee_adult_low_season  ?? 0.00,
            'concession_fee_child_high_season' => $this->concession_fee_child_high_season ?? 0.00,
            'concession_fee_child_low_season'  => $this->concession_fee_child_low_season  ?? 0.00,
            'crater_fee_adult_high_season'     => $this->crater_fee_adult_high_season     ?? 0.00,
            'crater_fee_adult_low_season'      => $this->crater_fee_adult_low_season      ?? 0.00,
            'crater_fee_child_high_season'     => $this->crater_fee_child_high_season     ?? 0.00,
            'crater_fee_child_low_season'      => $this->crater_fee_child_low_season      ?? 0.00,
            'park_fee_adult_high_season'       => $this->park_fee_adult_high_season       ?? 0.00,
            'park_fee_adult_low_season'        => $this->park_fee_adult_low_season        ?? 0.00,
            'park_fee_child_high_season'       => $this->park_fee_child_high_season       ?? 0.00,
            'park_fee_child_low_season'        => $this->park_fee_child_low_season        ?? 0.00,
            'pass_fee_adult_high_season'       => $this->pass_fee_adult_high_season       ?? 0.00,
            'pass_fee_adult_low_season'        => $this->pass_fee_adult_low_season        ?? 0.00,
            'pass_fee_child_high_season'       => $this->pass_fee_child_high_season       ?? 0.00,
            'pass_fee_child_low_season'        => $this->pass_fee_child_low_season        ?? 0.00,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->saveImageField($this->featured_image, 'destinations/featured');
        }

        if (!empty($this->gallery)) {
            $galleryPaths = $this->saveGalleryField($this->gallery, 'destinations/gallery');

            if ($this->isEdit) {
                $existing = $this->existingGallery ?? [];
                $data['gallery'] = array_merge($existing, $galleryPaths);
            } else {
                $data['gallery'] = $galleryPaths;
            }
        } else if ($this->isEdit) {
            $data['gallery'] = $this->existingGallery;
        }

        if ($this->isEdit) {
            $model = Destination::find($this->destination_id);
            $model->update($data);
            $this->dispatch('notify', message: __('Destination updated successfully!'), type: 'success');
        } else {
            $model = Destination::create($data);
            $this->dispatch('notify', message: __('Destination created successfully!'), type: 'success');
        }

        $model->activities()->sync($this->selectedActivities);
        $model->animals()->sync($this->selectedAnimals);

        if ($this->seo_title || $this->seo_description) {
            $model->seo()->updateOrCreate([], [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ]);
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete destinations');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteDestination(): void
    {
        $this->authorize('delete destinations');
        $destination = Destination::findOrFail($this->deletingId);
        // Rename slug on delete so it never blocks creating a destination with the same slug
        $destination->update(['slug' => $destination->slug . '-deleted-' . time()]);
        $destination->delete();
        $this->dispatch('notify', message: __('Destination deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit destinations');
        $destination = Destination::findOrFail($id);
        $destination->update(['is_active' => !$destination->is_active]);
        $this->dispatch('notify', message: __('Destination status updated!'), type: 'success');
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
            'city' => 'City',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['name', 'slug', 'city', 'status', 'created_at'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = Destination::with('city')->whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($d) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'name' => $d->name,
                    'slug' => $d->slug,
                    'city' => $d->city?->name ?: '—',
                    'status' => $d->is_active ? 'Active' : 'Inactive',
                    'created_at' => $d->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Destinations_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Destinations_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view destinations');
        
        $destinations = Destination::query()
            ->with('city')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('city', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.destinations.destination-list', [
            'destinations'   => $destinations,
            'countries'      => Country::orderBy('name')->get(),
            'allActivities'  => Activity::orderBy('name')->get(),
            'allAnimals'     => Animal::orderBy('name')->get(),
        ]);
    }
}
