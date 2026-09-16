<?php

namespace App\Livewire\Admin\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Products')]
class ProductList extends Component
{
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $category = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $product_id, $category_id, $name, $sku, $slug, $description, $unit_price, $cost_price, $stock_quantity, $unit = 'pcs', $is_active = true, $image, $existingImage;

    public bool $shouldUpdateSlug = false;
    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        if (!$this->isEdit || $this->shouldUpdateSlug) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetFields()
    {
        $this->product_id = null;
        $this->category_id = '';
        $this->name = '';
        $this->sku = '';
        $this->slug = '';
        $this->description = '';
        $this->unit_price = '';
        $this->cost_price = '';
        $this->stock_quantity = '';
        $this->unit = 'pcs';
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->isEdit = false;
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
        $product = Product::findOrFail($id);
        $this->product_id = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->slug = $product->slug;
        $this->description = $product->description;
        $this->unit_price = $product->unit_price;
        $this->cost_price = $product->cost_price;
        $this->stock_quantity = $product->stock_quantity;
        $this->unit = $product->unit;
        $this->is_active = $product->is_active;
        $this->existingImage = $product->image;



        $this->isEdit = true;
        $this->showModal = true;
    }



    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $this->product_id,
            'slug' => 'required|string|max:255|unique:products,slug,' . $this->product_id,
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'boolean',
            'image' => $this->imageRule('image', 600),
        ]);

        $data = [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'slug' => $this->slug,
            'description' => $this->description,
            'unit_price' => $this->unit_price,
            'cost_price' => $this->cost_price ?: 0,
            'stock_quantity' => $this->stock_quantity ?: 0,
            'unit' => $this->unit,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['image'] = $this->saveImageField($this->image, 'products');
        }



        if ($this->isEdit) {
            Product::find($this->product_id)->update($data);
            $this->dispatch('notify', message: __('Product updated successfully!'), type: 'success');
        } else {
            Product::create($data);
            $this->dispatch('notify', message: __('Product created successfully!'), type: 'success');
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

    public function deleteProduct(): void
    {
        $this->authorize('delete product');
        Product::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Product deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'sku' => 'SKU',
            'name' => 'Name',
            'category' => 'Category',
            'unit_price' => 'Unit Price',
            'cost_price' => 'Cost Price',
            'stock' => 'Stock',
            'unit' => 'Unit',
            'status' => 'Status',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['sku', 'name', 'category', 'unit_price', 'stock', 'status'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = Product::with('category')
            ->whereBetween('created_at', [$start, $end])
            ->when($this->category, fn($q) => $q->where('category_id', $this->category))
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($p) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'category' => $p->category?->name ?? 'N/A',
                    'unit_price' => number_format($p->unit_price, 2),
                    'cost_price' => number_format($p->cost_price, 2),
                    'stock' => $p->stock_quantity,
                    'unit' => $p->unit,
                    'status' => $p->is_active ? 'Active' : 'Inactive',
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Products_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Products_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view product');
        $products = Product::with('category')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                $query->where('category_id', $this->category);
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        $categories = ProductCategory::orderBy('name', 'asc')->get();

        return view('livewire.admin.products.product-list', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
