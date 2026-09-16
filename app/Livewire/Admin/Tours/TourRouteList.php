<?php

namespace App\Livewire\Admin\Tours;

use App\Models\TourRoute;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Tour Routes')]
class TourRouteList extends Component
{
    use WithPagination, AuthorizesRequests, \App\Traits\HasLocaleTranslations;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $route_id, $slug, $is_active = true;
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
        $this->route_id = null;
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
        $route = TourRoute::findOrFail($id);
        $this->route_id = $route->id;
        $this->loadTranslationsFromModel($route, ['name', 'description']);
        $this->slug = $route->slug;
        $this->is_active = $route->is_active;
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
            'slug' => 'required|string|max:255|unique:tour_routes,slug,' . $this->route_id,
            'is_active' => 'boolean',
        ], ['names.en.required' => 'The English name is required.']);

        $data = ['slug' => $this->slug, 'is_active' => $this->is_active];
        if ($this->isEdit) {
            $route = TourRoute::find($this->route_id);
            $route->fill($data);
            $this->applyTranslationsToModel($route, ['name', 'description']);
        } else {
            $route = new TourRoute($data);
            $this->applyTranslationsToModel($route, ['name', 'description']);
        }
        $this->showModal = false;
        session()->flash('success', $this->isEdit ? __('Route updated successfully.') : __('Route created successfully.'));
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product');
        $route = TourRoute::find($id);
        if ($route) {
            $route->update(['is_active' => !$route->is_active]);
        }
    }

    public function confirmDelete($id)
    {
        $this->authorize('delete product');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $this->authorize('delete product');
        if ($this->deletingId) {
            TourRoute::find($this->deletingId)?->delete();
            $this->showDeleteModal = false;
            session()->flash('success', __('Route deleted successfully.'));
        }
    }

    public function render()
    {
        $routes = TourRoute::query()
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.tours.tour-route-list', [
            'routes' => $routes,
        ]);
    }
}
