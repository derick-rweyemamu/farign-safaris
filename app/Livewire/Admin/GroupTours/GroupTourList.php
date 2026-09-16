<?php

namespace App\Livewire\Admin\GroupTours;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\GroupTour;
use App\Models\GroupCategory;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Group Tours')]
class GroupTourList extends Component {
    use WithPagination, WithFileUploads, AuthorizesRequests, \App\Traits\InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $categoryFilter = '';

    #[Url]
    public int $perPage = 10;

    public $tour_id, $group_category_id, $title, $slug, $description, $start_date, $end_date, $price, $available_seats, $total_seats, $booked_seats = 0, $is_active = true, $image, $existingImage;
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

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedTitle($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetFields()
    {
        $this->tour_id = null;
        $this->group_category_id = null;
        $this->title = '';
        $this->slug = '';
        $this->description = '';
        $this->start_date = null;
        $this->end_date = null;
        $this->price = null;
        $this->available_seats = null;
        $this->total_seats = null;
        $this->booked_seats = 0;
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
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
        $tour = GroupTour::findOrFail($id);
        $this->tour_id = $tour->id;
        $this->group_category_id = $tour->group_category_id;
        $this->title = $tour->title;
        $this->slug = $tour->slug;
        $this->description = $tour->description;
        $this->start_date = $tour->start_date?->format('Y-m-d');
        $this->end_date = $tour->end_date?->format('Y-m-d');
        $this->price = $tour->price;
        $this->available_seats = $tour->available_seats;
        $this->total_seats = $tour->total_seats;
        $this->booked_seats = $tour->booked_seats;
        $this->is_active = $tour->is_active;
        $this->existingImage = $tour->featured_image;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit product' : 'create product');
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:group_tours,slug,' . $this->tour_id,
            'group_category_id' => 'required|exists:group_categories,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0',
            'total_seats' => 'nullable|integer|min:0',
            'booked_seats' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => $this->imageRule('image', 2048),
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'group_category_id' => $this->group_category_id,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'price' => $this->price,
            'total_seats' => $this->total_seats,
            'booked_seats' => $this->booked_seats,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['featured_image'] = $this->saveImageField($this->image, 'group-tours');
        }

        if ($this->isEdit) {
            GroupTour::find($this->tour_id)->update($data);
            ActivityLog::record('group_tour.updated', "Updated group tour: {$this->title}");
            $this->dispatch('notify', message: __('Tour updated successfully!'), type: 'success');
        } else {
            GroupTour::create($data);
            ActivityLog::record('group_tour.created', "Created group tour: {$this->title}");
            $this->dispatch('notify', message: __('Tour created successfully!'), type: 'success');
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

    public function deleteTour(): void
    {
        $this->authorize('delete product');
        $tour = GroupTour::findOrFail($this->deletingId);
        $title = $tour->title;
        $tour->delete();
        ActivityLog::record('group_tour.deleted', "Deleted group tour: {$title}");
        $this->dispatch('notify', message: __('Tour deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit product');
        $tour = GroupTour::findOrFail($id);
        $tour->update(['is_active' => !$tour->is_active]);
        $status = $tour->is_active ? 'activated' : 'deactivated';
        ActivityLog::record('group_tour.updated', ucfirst($status) . " group tour: {$tour->title}");
        $this->dispatch('notify', message: __('Tour status updated!'), type: 'success');
    }

    public function render()
    {
        $this->authorize('view product');
        $tours = GroupTour::query()
            ->with('category')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== '', function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('group_category_id', $this->categoryFilter);
            })
            ->orderBy('start_date', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.group-tours.group-tour-list', [
            'tours' => $tours,
            'categories' => GroupCategory::where('is_active', true)->get(),
        ]);
    }
}
