<?php

namespace App\Livewire\Admin\Tours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TourTag;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Tour Tags')]
class TourTagList extends Component
{
    use WithPagination, AuthorizesRequests, \App\Traits\HasLocaleTranslations;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $tag_id, $slug, $is_active = true;
    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public bool $isSlugManuallyEdited = false;
    public bool $shouldUpdateSlug = false;

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
        if (!$this->isSlugManuallyEdited && (!$this->isEdit || $this->shouldUpdateSlug)) {
            $this->slug = Str::slug($this->names['en'] ?? '');
        }
    }

    public function updatedSlug($value)
    {
        $this->isSlugManuallyEdited = !empty($value);
    }

    public function updatedShouldUpdateSlug($value)
    {
        if ($value) { $this->isSlugManuallyEdited = false; $this->slug = Str::slug($this->names['en'] ?? ''); }
    }

    public function resetFields()
    {
        $this->tag_id = null;
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->is_active = true;
        $this->isEdit = false;
        $this->isSlugManuallyEdited = false;
        $this->shouldUpdateSlug = false;
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
        $tag = TourTag::findOrFail($id);
        $this->tag_id = $tag->id;
        $this->loadTranslationsFromModel($tag, ['name', 'description']);
        $this->slug = $tag->slug;
        $this->is_active = $tag->is_active;
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
            'slug' => 'required|string|max:255|unique:tour_tags,slug,' . $this->tag_id,
            'is_active' => 'boolean',
        ], ['names.en.required' => 'The English name is required.']);
        $data = ['slug' => $this->slug, 'is_active' => $this->is_active];
        if ($this->isEdit) {
            $tag = TourTag::find($this->tag_id);
            $tag->fill($data);
            $this->applyTranslationsToModel($tag, ['name', 'description']);
            $this->dispatch('notify', message: __('Tag updated successfully!'), type: 'success');
        } else {
            $tag = new TourTag($data);
            $this->applyTranslationsToModel($tag, ['name', 'description']);
            $this->dispatch('notify', message: __('Tag created successfully!'), type: 'success');
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

    public function deleteTag(): void
    {
        $this->authorize('delete product');
        TourTag::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Tag deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product');
        $tag = TourTag::findOrFail($id);
        $tag->update(['is_active' => !$tag->is_active]);
        $this->dispatch('notify', message: __('Tag status updated!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view product');
        $tags = TourTag::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.tours.tour-tag-list', [
            'tags' => $tags,
        ]);
    }
}
