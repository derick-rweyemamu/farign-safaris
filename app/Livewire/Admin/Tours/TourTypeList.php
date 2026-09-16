<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TourType;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Tour Types')]
class TourTypeList extends Component
{
    use WithPagination, AuthorizesRequests;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $type_id, $slug, $is_active = true;
    public array $names = [];
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
        $this->type_id = null;
        $this->names = array_fill_keys($this->locales, '');
        $this->slug = '';
        $this->is_active = true;
        $this->isEdit = false;
        $this->isSlugManuallyEdited = false;
        $this->shouldUpdateSlug = false;
        $this->activeLocaleTab = 'en';
    }

    public function create()
    {
        $this->authorize('create product');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit product');
        $this->resetFields();
        $type = TourType::findOrFail($id);
        $this->type_id = $type->id;
        foreach ($this->locales as $locale) {
            $this->names[$locale] = $type->getTranslation('name', $locale, false) ?? '';
        }
        $this->slug = $type->slug;
        $this->is_active = $type->is_active;
        $this->isEdit = true;
        $this->isSlugManuallyEdited = true;
        $this->shouldUpdateSlug = false;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tour_types,slug,' . $this->type_id,
            'is_active' => 'boolean',
        ], [
            'names.en.required' => 'The English name is required.',
        ]);

        $data = [
            'slug' => $this->slug,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit) {
            $type = TourType::find($this->type_id);
            $type->update($data);
            foreach ($this->locales as $locale) {
                if (!empty($this->names[$locale])) {
                    $type->setTranslation('name', $locale, $this->names[$locale]);
                }
            }
            $type->save();
            $this->dispatch('notify', message: __('Tour type updated successfully!'), type: 'success');
        } else {
            $type = TourType::create($data);
            foreach ($this->locales as $locale) {
                if (!empty($this->names[$locale])) {
                    $type->setTranslation('name', $locale, $this->names[$locale]);
                }
            }
            $type->save();
            $this->dispatch('notify', message: __('Tour type created successfully!'), type: 'success');
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

    public function deleteType(): void
    {
        $this->authorize('delete product');
        TourType::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Tour type deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product');
        $type = TourType::findOrFail($id);
        $type->update(['is_active' => !$type->is_active]);
        $this->dispatch('notify', message: __('Tour type status updated!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view product');
        $types = TourType::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.tours.tour-type-list', [
            'types' => $types,
        ]);
    }
}
