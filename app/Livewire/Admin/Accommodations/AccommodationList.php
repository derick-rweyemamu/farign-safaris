<?php

namespace App\Livewire\Admin\Accommodations;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Accommodation;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Accommodations')]
class AccommodationList extends Component
{
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

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
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $accommodation_id;
    public $city_id;
    public $selectedCountryId = '';
    public $filteredCities;
    public $name;
    public $slug;
    public $description;
    public $featured_image;
    public $existingFeaturedImage;
    public $gallery = [];
    public $existingGallery = [];
    public $is_active = true;

    // SEO Fields
    public $seo_title;
    public $seo_description;

    // Rates array for single/double/family and budget/mid-range/luxury price combinations
    public $rates = [];

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
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetFields()
    {
        $this->accommodation_id = null;
        $this->city_id = null;
        $this->selectedCountryId = '';
        $this->filteredCities = collect();
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->featured_image = null;
        $this->existingFeaturedImage = null;
        $this->gallery = [];
        $this->existingGallery = [];
        $this->is_active = true;
        $this->seo_title = '';
        $this->seo_description = '';
        $this->rates = [
            [
                'room_type' => 'single',
                'season' => 'high season',
                'class' => 'budget',
                'sto' => '',
                'rack' => ''
            ]
        ];
        $this->isEdit = false;
    }

    public function updatedSelectedCountryId($value)
    {
        $this->city_id = null;
        $this->filteredCities = $value
            ? City::where('country_id', $value)->orderBy('name')->get()
            : collect();
    }

    public function addRate()
    {
        $this->rates[] = [
            'room_type' => 'single',
            'season' => 'high season',
            'class' => 'budget',
            'sto' => '',
            'rack' => ''
        ];
    }

    public function removeRate($index)
    {
        unset($this->rates[$index]);
        $this->rates = array_values($this->rates);

        if (empty($this->rates)) {
            $this->addRate();
        }
    }

    public function create()
    {
        $this->authorize('create accommodations');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit accommodations');
        $this->resetFields();

        $accommodation = Accommodation::with('city.country', 'seo')->findOrFail($id);
        $this->accommodation_id = $accommodation->id;
        $this->city_id = $accommodation->city_id;

        // Pre-populate country filter
        if ($accommodation->city && $accommodation->city->country) {
            $this->selectedCountryId = $accommodation->city->country_id;
            $this->filteredCities = City::where('country_id', $accommodation->city->country_id)->orderBy('name')->get();
        }
        $this->name = $accommodation->name;
        $this->slug = $accommodation->slug;
        $this->description = $accommodation->description;
        $this->is_active = $accommodation->is_active;
        $this->existingFeaturedImage = $accommodation->featured_image;
        $this->existingGallery = $accommodation->gallery ?? [];

        $this->seo_title = $accommodation->seo?->title ?? '';
        $this->seo_description = $accommodation->seo?->description ?? '';

        $this->isEdit = true;

        $this->rates = $accommodation->rates()->get()->map(function ($rate) {
            return [
                'room_type' => $rate->room_type,
                'season' => $rate->season ?? 'high season',
                'class' => $rate->class,
                'sto' => $rate->sto,
                'rack' => $rate->rack,
            ];
        })->toArray();

        if (empty($this->rates)) {
            $this->rates = [
                [
                    'room_type' => 'single',
                    'season' => 'high season',
                    'class' => 'budget',
                    'sto' => '',
                    'rack' => ''
                ]
            ];
        }

        $this->showModal = true;
    }

    public function removeGalleryImage($index)
    {
        if (isset($this->existingGallery[$index])) {
            unset($this->existingGallery[$index]);
            $this->existingGallery = array_values($this->existingGallery);

            if ($this->accommodation_id) {
                Accommodation::find($this->accommodation_id)->update([
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
        $this->authorize($this->isEdit ? 'edit accommodations' : 'create accommodations');

        $this->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:accommodations,slug,' . $this->accommodation_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'featured_image' => $this->imageRule('featured_image', 600),
            'gallery.*' => $this->galleryRule(600),

            'rates.*.room_type' => 'required|string|in:single,double,triple,family',
            'rates.*.season' => 'required|string|in:peak season,high season,low season',
            'rates.*.class' => 'required|string|in:budget,mid-range,luxury',
            'rates.*.sto' => 'required|numeric|min:0',
            'rates.*.rack' => 'required|numeric|min:0',
        ], [
            'rates.*.sto.required' => 'STO price is required.',
            'rates.*.rack.required' => 'RACK price is required.',
        ]);

        $data = [
            'city_id' => $this->city_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->featured_image) {
            $data['featured_image'] = $this->saveImageField($this->featured_image, 'accommodations/featured');
        }

        if (!empty($this->gallery)) {
            $galleryPaths = $this->saveGalleryField($this->gallery, 'accommodations/gallery');

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
            $model = Accommodation::find($this->accommodation_id);
            $model->update($data);
            $this->dispatch('notify', message: __('Accommodation updated successfully!'), type: 'success');
        } else {
            $model = Accommodation::create($data);
            $this->dispatch('notify', message: __('Accommodation created successfully!'), type: 'success');
        }

        if ($this->seo_title || $this->seo_description) {
            $model->seo()->updateOrCreate([], [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ]);
        }

        // Save pricing rates
        $model->rates()->delete();
        foreach ($this->rates as $rateData) {
            $model->rates()->create([
                'room_type' => $rateData['room_type'],
                'season' => $rateData['season'] ?? 'high season',
                'class' => $rateData['class'],
                'sto' => $rateData['sto'],
                'rack' => $rateData['rack'],
            ]);
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete accommodations');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteAccommodation(): void
    {
        $this->authorize('delete accommodations');
        Accommodation::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Accommodation deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit accommodations');
        $accommodation = Accommodation::findOrFail($id);
        $accommodation->update(['is_active' => !$accommodation->is_active]);
        $this->dispatch('notify', message: __('Accommodation status updated!'), type: 'success');
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

        $query = Accommodation::with('city')->whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function ($a) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match ($col) {
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'city' => $a->city?->name ?: '—',
                    'status' => $a->is_active ? 'Active' : 'Inactive',
                    'created_at' => $a->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Accommodations_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Accommodations_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view accommodations');

        $accommodations = Accommodation::query()
            ->with(['city', 'rates'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('city', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.accommodations.accommodation-list', [
            'accommodations' => $accommodations,
            'countries' => Country::orderBy('name')->get(),
        ]);
    }
}
