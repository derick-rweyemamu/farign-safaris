<?php

namespace App\Livewire\Admin\CMS;

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Pages Management')]
class PagesList extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    public array $statusOptions = [
        '0' => 'Draft',
        '1' => 'Published',
    ];

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public bool $isEdit = false;
    public bool $shouldUpdateSlug = false;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $featured_image = '';
    public $featuredImageUpload;
    public int $is_published = 0;
    public string $meta_title = '';
    public string $meta_description = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . ($this->editingId ?? 'NULL'),
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'featuredImageUpload' => $this->imageRule('featuredImageUpload', 600),
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ];
    }

    public function updatedTitle($value): void
    {
        if (!$this->isEdit || $this->shouldUpdateSlug) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->featured_image = '';
        $this->featuredImageUpload = null;
        $this->is_published = 0;
        $this->meta_title = '';
        $this->meta_description = '';
        $this->editingId = null;
        $this->isEdit = false;
        $this->shouldUpdateSlug = false;
        $this->resetErrorBag();
    }

    public function openModal(): void
    {
        $this->authorize('create pages');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('edit pages');
        $this->resetForm();
        $this->editingId = $id;
        $this->isEdit = true;
        $page = Page::findOrFail($id);

        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content ?? '';
        $this->featured_image = $page->featured_image ?? '';
        $this->is_published = $page->is_published ? 1 : 0;
        $this->meta_title = $page->seo?->title ?? $page->meta_title ?? '';
        $this->meta_description = $page->seo?->description ?? $page->meta_description ?? '';

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit pages' : 'create pages');
        $this->validate();

        if ($this->featuredImageUpload) {
            $this->featured_image = $this->saveImageField($this->featuredImageUpload, 'pages');
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'featured_image' => $this->featured_image,
            'is_published' => $this->is_published,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ];

        if ($this->editingId) {
            $page = Page::findOrFail($this->editingId);
            $page->update($data);
            $this->dispatch('notify', message: __('Page updated successfully.'), type: 'success');
        } else {
            $page = Page::create($data);
            $this->dispatch('notify', message: __('Page created successfully.'), type: 'success');
        }

        if ($this->meta_title || $this->meta_description) {
            $page->seo()->updateOrCreate([], [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete pages');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete pages');
        if ($this->deletingId) {
            Page::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Page deleted successfully.'), type: 'success');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function togglePublished(int $id): void
    {
        $this->authorize('publish pages');
        $page = Page::findOrFail($id);
        $page->update(['is_published' => !$page->is_published]);
        $this->dispatch('notify', message: __('Publication status updated.'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view pages');
        $pages = Page::query()
            ->select(['id', 'title', 'slug', 'featured_image', 'is_published', 'created_at', 'updated_at', 'deleted_at'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_published', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.cms.pages-list', compact('pages'));
    }
}
