<?php

namespace App\Livewire\Admin\Activities;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\HasReports;
use App\Traits\InteractsWithMediaUploads;

#[Layout('admin::layouts.app')]
#[Title('Activities')]
class ActivityList extends Component
{
    use WithPagination, WithFileUploads, HasReports, AuthorizesRequests, \App\Traits\HasLocaleTranslations, InteractsWithMediaUploads;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $activity_id;
    public $slug;
    public $is_active = true;
    public $image;
    public $existingImage;

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
        $this->activity_id = null;
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->is_active = true;
        $this->image = null;
        $this->existingImage = null;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create tour activities');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit tour activities');
        $this->resetFields();
        $activity = Activity::findOrFail($id);
        $this->activity_id = $activity->id;
        $this->loadTranslationsFromModel($activity, ['name', 'description']);
        $this->slug = $activity->slug;
        $this->is_active = $activity->is_active;
        $this->existingImage = $activity->image;
        $this->image = null;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit tour activities' : 'create tour activities');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:activities,slug,' . $this->activity_id,
            'is_active' => 'boolean',
            'image' => $this->imageRule('image', 2048),
        ], ['names.en.required' => 'The English name is required.']);

        $data = [
            'slug' => $this->slug,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            $data['image'] = $this->saveImageField($this->image, 'activities');
        } elseif (empty($this->existingImage)) {
            $data['image'] = null;
        }

        if ($this->isEdit) {
            $a = Activity::find($this->activity_id);
            $a->fill($data);
            $this->applyTranslationsToModel($a, ['name', 'description']);
            $this->dispatch('notify', message: __('Activity updated successfully!'), type: 'success');
        } else {
            $a = new Activity($data);
            $this->applyTranslationsToModel($a, ['name', 'description']);
            $this->dispatch('notify', message: __('Activity created successfully!'), type: 'success');
        }
        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete tour activities');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteActivity(): void
    {
        $this->authorize('delete tour activities');
        Activity::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Activity deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit tour activities');
        $activity = Activity::findOrFail($id);
        $activity->update(['is_active' => !$activity->is_active]);
        $this->dispatch('notify', message: __('Activity status updated!'), type: 'success');
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
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

        $query = Activity::whereBetween('created_at', [$start, $end])
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($a) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'name' => $a->name,
                    'slug' => $a->slug,
                    'status' => $a->is_active ? 'Active' : 'Inactive',
                    'created_at' => $a->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Activities_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Activities_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view tour activities');
        
        $activities = Activity::query()
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

        return view('livewire.admin.activities.activity-list', [
            'activities' => $activities,
        ]);
    }
}
