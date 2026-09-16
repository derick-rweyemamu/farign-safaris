<?php

namespace App\Livewire\Admin\CMS;

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Posts Management')]
class PostsList extends Component
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
    public ?int $post_category_id = null;
    public string $excerpt = '';
    public string $content = '';
    public string $featured_image = '';
    public $featuredImageUpload;
    public int $is_published = 0;
    public $published_at;

    // SEO Fields
    public $seo_title;
    public $seo_description;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . ($this->editingId ?? 'NULL'),
            'post_category_id' => 'nullable|exists:post_categories,id',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'featuredImageUpload' => $this->imageRule('featuredImageUpload', 600),
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
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
        $this->post_category_id = null;
        $this->excerpt = '';
        $this->content = '';
        $this->featured_image = '';
        $this->featuredImageUpload = null;
        $this->is_published = 0;
        $this->published_at = null;
        $this->seo_title = '';
        $this->seo_description = '';
        $this->editingId = null;
        $this->isEdit = false;
        $this->shouldUpdateSlug = false;
        $this->resetErrorBag();
    }

    public function openModal(): void
    {
        $this->authorize('create posts');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('edit posts');
        $this->resetForm();
        $this->editingId = $id;
        $this->isEdit = true;
        $post = Post::findOrFail($id);

        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->post_category_id = $post->post_category_id;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content ?? '';
        $this->featured_image = $post->featured_image ?? '';
        $this->is_published = $post->is_published ? 1 : 0;
        $this->published_at = $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : null;

        $this->seo_title = $post->seo?->title ?? '';
        $this->seo_description = $post->seo?->description ?? '';

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit posts' : 'create posts');
        $this->validate();

        if ($this->featuredImageUpload) {
            $this->featured_image = $this->saveImageField($this->featuredImageUpload, 'posts');
        } elseif (empty($this->featured_image)) {
            $this->featured_image = '';
        }

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'post_category_id' => $this->post_category_id ?: null,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image' => $this->featured_image ?: null,
            'is_published' => $this->is_published,
            'published_at' => $this->is_published
                ? ($this->published_at ?: now())
                : ($this->published_at ?: null),
            'author_id' => auth()->id(),
        ];

        if ($this->editingId) {
            $post = Post::findOrFail($this->editingId);
            $post->update($data);
            $this->dispatch('notify', message: __('Post updated successfully.'), type: 'success');
        } else {
            $post = Post::create($data);
            $this->dispatch('notify', message: __('Post created successfully.'), type: 'success');
        }

        if ($this->seo_title || $this->seo_description) {
            $post->seo()->updateOrCreate([], [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ]);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete posts');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorize('delete posts');
        if ($this->deletingId) {
            Post::findOrFail($this->deletingId)->delete();
            $this->dispatch('notify', message: __('Post deleted successfully.'), type: 'success');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function togglePublished(int $id): void
    {
        $this->authorize('publish posts');
        $post = Post::findOrFail($id);
        $post->update([
            'is_published' => !$post->is_published,
            'published_at' => !$post->is_published ? now() : $post->published_at
        ]);
        $this->dispatch('notify', message: __('Publication status updated.'), type: 'success');
    }

    public function duplicate(int $id): void
    {
        $this->authorize('create posts');
        $original = Post::findOrFail($id);

        $copy = $original->replicate();
        $copy->title = $original->title . ' (Copy)';
        
        $newSlug = $original->slug . '-copy';
        $counter = 1;
        while (Post::where('slug', $newSlug)->exists()) {
            $newSlug = $original->slug . '-copy-' . $counter;
            $counter++;
        }
        $copy->slug = $newSlug;
        $copy->is_published = false;
        $copy->published_at = null;
        $copy->author_id = auth()->id();
        $copy->save();

        if ($original->seo) {
            $copy->seo()->create([
                'title' => $original->seo->title,
                'description' => $original->seo->description,
            ]);
        }

        $this->dispatch('notify', message: __('Post duplicated successfully.'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view posts');
        $posts = Post::with(['author', 'category'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('slug', 'like', "%{$this->search}%"))
            ->when($this->statusFilter !== '', fn($q) => $q->where('is_published', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $categories = \App\Models\PostCategory::where('is_active', true)->get();

        return view('livewire.admin.cms.posts-list', compact('posts', 'categories'));
    }
}
