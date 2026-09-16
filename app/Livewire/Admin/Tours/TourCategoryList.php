<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\TourCategory;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Tour Categories')]
class TourCategoryList extends Component {
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $category_id, $slug, $is_active = true, $image, $existingImage, $sort_order = 0;
    public array $names = [];
    public array $descriptions = [];
    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public bool $isSlugManuallyEdited = false;
    public bool $shouldUpdateSlug = false;

    public array $locales = ['en', 'fr', 'de', 'es', 'it', 'zh'];
    public string $activeLocaleTab = 'en';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedNames($value)
    {
        $enName = $this->names['en'] ?? '';
        if (!$this->isSlugManuallyEdited && (!$this->isEdit || $this->shouldUpdateSlug)) {
            $this->slug = Str::slug($enName);
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
            $this->slug = Str::slug($this->names['en'] ?? '');
        }
    }

    public function resetFields()
    {
        $this->category_id = null;
        $this->names = array_fill_keys($this->locales, '');
        $this->descriptions = array_fill_keys($this->locales, '');
        $this->slug = '';
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->sort_order = 0;
        $this->isEdit = false;
        $this->isSlugManuallyEdited = false;
        $this->shouldUpdateSlug = false;
        $this->activeLocaleTab = 'en';
    }

    public function create()
    {
        $this->authorize('create tour categories');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit tour categories');
        $this->resetFields();
        $category = TourCategory::findOrFail($id);
        $this->category_id = $category->id;
        // Load each locale's translation
        foreach ($this->locales as $locale) {
            $this->names[$locale] = $category->getTranslation('name', $locale, false) ?? '';
            $this->descriptions[$locale] = $category->getTranslation('description', $locale, false) ?? '';
        }
        $this->slug = $category->slug;
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order;
        $this->existingImage = $category->image;
        $this->isEdit = true;
        $this->isSlugManuallyEdited = true;
        $this->shouldUpdateSlug = false;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit tour categories' : 'create tour categories');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tour_categories,slug,' . $this->category_id,
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'image' => $this->imageRule('image', 600),
        ], [
            'names.en.required' => 'The English name is required.',
        ]);

        $data = [
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'sort_order' => (int) $this->sort_order,
        ];

        if ($this->image) {
            $data['image'] = $this->saveImageField($this->image, 'tour-categories');
        } elseif (empty($this->existingImage)) {
            $data['image'] = null;
        }

        if ($this->isEdit) {
            $category = TourCategory::find($this->category_id);
            $category->update($data);
            foreach ($this->locales as $locale) {
                if (!empty($this->names[$locale])) {
                    $category->setTranslation('name', $locale, $this->names[$locale]);
                }
                if (!empty($this->descriptions[$locale])) {
                    $category->setTranslation('description', $locale, $this->descriptions[$locale]);
                }
            }
            $category->save();
            $this->dispatch('notify', message: __('Category updated successfully!'), type: 'success');
        } else {
            $category = TourCategory::create($data);
            foreach ($this->locales as $locale) {
                if (!empty($this->names[$locale])) {
                    $category->setTranslation('name', $locale, $this->names[$locale]);
                }
                if (!empty($this->descriptions[$locale])) {
                    $category->setTranslation('description', $locale, $this->descriptions[$locale]);
                }
            }
            $category->save();
            $this->dispatch('notify', message: __('Category created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete tour categories');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        $this->authorize('delete tour categories');
        TourCategory::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Category deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit tour categories');
        $category = TourCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        $this->dispatch('notify', message: __('Category status updated!'), type: 'success');
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['name', 'slug', 'status', 'created_at'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = TourCategory::whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($c) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'description' => $c->description,
                    'status' => $c->is_active ? 'Active' : 'Inactive',
                    'created_at' => $c->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('TourCategories_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('TourCategories_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view tour categories');
        $categories = TourCategory::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.tours.tour-category-list', [
            'categories' => $categories,
        ]);
    }
}
