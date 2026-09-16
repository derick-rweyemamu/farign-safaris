<?php

namespace App\Livewire\Admin\CMS;

use App\Models\PostCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

#[Layout('admin::layouts.app')]
#[Title('Post Categories')]
class PostCategoryList extends Component
{
    use WithPagination, WithFileUploads, \App\Traits\InteractsWithMediaUploads, \App\Traits\HasLocaleTranslations;

    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $slug = '';
    public string $featured_image = '';
    public $featuredImageUpload;
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:post_categories,slug,' . ($this->editingId ?? 'NULL'),
            'featured_image' => 'nullable|string',
            'featuredImageUpload' => $this->imageRule('featuredImageUpload', 600),
            'is_active' => 'boolean',
        ];
    }

    public function updatedNames($value): void
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($this->names['en'] ?? '');
        }
    }

    public function resetForm(): void
    {
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->featured_image = '';
        $this->featuredImageUpload = null;
        $this->is_active = true;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $this->editingId = $id;
        $category = PostCategory::findOrFail($id);
        $this->loadTranslationsFromModel($category, ['name', 'description']);
        $this->slug = $category->slug;
        $this->featured_image = $category->featured_image ?? '';
        $this->is_active = $category->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        if ($this->featuredImageUpload) {
            $this->featured_image = $this->saveImageField($this->featuredImageUpload, 'post-categories');
        }
        $data = ['slug' => $this->slug, 'featured_image' => $this->featured_image, 'is_active' => $this->is_active];
        if ($this->editingId) {
            $cat = PostCategory::findOrFail($this->editingId);
            $cat->fill($data);
            $this->applyTranslationsToModel($cat, ['name', 'description']);
            $this->dispatch('notify', message: __('Category updated successfully.'), type: 'success');
        } else {
            $cat = new PostCategory($data);
            $this->applyTranslationsToModel($cat, ['name', 'description']);
            $this->dispatch('notify', message: __('Category created successfully.'), type: 'success');
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            PostCategory::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Category deleted successfully.'), type: 'success');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive(int $id): void
    {
        $category = PostCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        $this->dispatch('notify', message: __('Category status updated.'), type: 'success');
    }

    public function render()
    {
        $categories = PostCategory::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))
            ->withCount('posts')
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.cms.post-category-list', compact('categories'));
    }
}
