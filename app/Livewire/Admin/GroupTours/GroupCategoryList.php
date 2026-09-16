<?php

namespace App\Livewire\Admin\GroupTours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GroupCategory;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Group Categories')]
class GroupCategoryList extends Component {
    use WithPagination, AuthorizesRequests, \App\Traits\HasLocaleTranslations;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $category_id, $slug, $is_active = true;
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

    public function updatedNames($value)
    {
        if (!$this->isEdit) { $this->slug = Str::slug($this->names['en'] ?? ''); }
    }

    public function resetFields()
    {
        $this->category_id = null;
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->is_active = true;
        $this->isEdit = false;
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
        $category = GroupCategory::findOrFail($id);
        $this->category_id = $category->id;
        $this->loadTranslationsFromModel($category, ['name', 'description']);
        $this->slug = $category->slug;
        $this->is_active = $category->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:group_categories,slug,' . $this->category_id,
            'is_active' => 'boolean',
        ], ['names.en.required' => 'The English name is required.']);
        $data = [
            'slug' => $this->slug, 
            'is_active' => $this->is_active,
            'name' => array_filter($this->names),
            'description' => array_filter($this->descriptions),
        ];
        if ($this->isEdit) {
            $cat = GroupCategory::find($this->category_id);
            $cat->fill($data);
            $this->applyTranslationsToModel($cat, ['name', 'description']);
            ActivityLog::record('group_category.updated', "Updated group category: {$this->names['en']}");
            $this->dispatch('notify', message: __('Category updated successfully!'), type: 'success');
        } else {
            $cat = new GroupCategory($data);
            $this->applyTranslationsToModel($cat, ['name', 'description']);
            ActivityLog::record('group_category.created', "Created group category: {$this->names['en']}");
            $this->dispatch('notify', message: __('Category created successfully!'), type: 'success');
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

    public function deleteCategory(): void
    {
        $this->authorize('delete product');
        $category = GroupCategory::findOrFail($this->deletingId);
        $name = $category->name;
        $category->delete();
        ActivityLog::record('group_category.deleted', "Deleted group category: {$name}");
        $this->dispatch('notify', message: __('Category deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product');
        $category = GroupCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        $status = $category->is_active ? 'activated' : 'deactivated';
        ActivityLog::record('group_category.updated', ucfirst($status) . " group category: {$category->name}");
        $this->dispatch('notify', message: __('Category status updated!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view product');
        $categories = GroupCategory::query()
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

        return view('livewire.admin.group-tours.group-category-list', [
            'categories' => $categories,
        ]);
    }
}
