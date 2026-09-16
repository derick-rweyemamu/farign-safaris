<?php

namespace App\Livewire\Admin\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Product Categories')]
class ProductCategoryList extends Component {
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $category_id, $name, $slug, $description, $is_active = true, $image, $existingImage;
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
        $this->category_id = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create product category');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit product category');
        $this->resetFields();
        $category = ProductCategory::findOrFail($id);
        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->is_active = $category->is_active;
        $this->existingImage = $category->image;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product category' : 'create product category');
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:product_categories,slug,' . $this->category_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => $this->imageRule('image', 600), // 1MB Max
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['image'] = $this->saveImageField($this->image, 'product-categories');
        }

        if ($this->isEdit) {
            ProductCategory::find($this->category_id)->update($data);
            $this->dispatch('notify', message: __('Category updated successfully!'), type: 'success');
        } else {
            ProductCategory::create($data);
            $this->dispatch('notify', message: __('Category created successfully!'), type: 'success');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete product category');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteCategory(): void
    {
        $this->authorize('delete product category');
        ProductCategory::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Category deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product category');
        $category = ProductCategory::findOrFail($id);
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

        $query = ProductCategory::whereBetween('created_at', [$start, $end])
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
            return $this->exportToPdf('ProductCategories_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('ProductCategories_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view product category');
        $categories = ProductCategory::query()
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

        return view('livewire.admin.products.category-list', [
            'categories' => $categories,
        ]);
    }
}
