<?php

namespace App\Livewire\Admin\MealPlans;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MealPlan;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Meal Plans')]
class MealPlanList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests, \App\Traits\HasLocaleTranslations;

    #[Url(history: true)]
    public $search = '';

    #[Url]
    public $typeFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    public $meal_plan_id;
    public $slug;
    public $type = 'Breakfast';
    public $price = 0.00;
    public $is_active = true;

    public $isEdit = false;
    public $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
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
        $this->meal_plan_id = null;
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->type = 'Breakfast';
        $this->price = 0.00;
        $this->is_active = true;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create meal plans');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit meal plans');
        $this->resetFields();
        $mealPlan = MealPlan::findOrFail($id);
        $this->meal_plan_id = $mealPlan->id;
        $this->loadTranslationsFromModel($mealPlan, ['name', 'description']);
        $this->slug = $mealPlan->slug;
        $this->type = $mealPlan->type;
        $this->price = $mealPlan->price;
        $this->is_active = $mealPlan->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit meal plans' : 'create meal plans');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:meal_plans,slug,' . $this->meal_plan_id,
            'type' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ], ['names.en.required' => 'The English name is required.']);
        $data = [
            'slug' => $this->slug, 'type' => $this->type,
            'price' => ($this->price === '' || $this->price === null) ? null : $this->price,
            'is_active' => $this->is_active,
        ];
        if ($this->isEdit) {
            $mp = MealPlan::find($this->meal_plan_id);
            $mp->fill($data);
            $this->applyTranslationsToModel($mp, ['name', 'description']);
            $this->dispatch('notify', message: __('Meal Plan updated successfully!'), type: 'success');
        } else {
            $mp = new MealPlan($data);
            $this->applyTranslationsToModel($mp, ['name', 'description']);
            $this->dispatch('notify', message: __('Meal Plan created successfully!'), type: 'success');
        }
        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete meal plans');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMealPlan(): void
    {
        $this->authorize('delete meal plans');
        MealPlan::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Meal Plan deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit meal plans');
        $mealPlan = MealPlan::findOrFail($id);
        $mealPlan->update(['is_active' => !$mealPlan->is_active]);
        $this->dispatch('notify', message: __('Meal Plan status updated!'), type: 'success');
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
            'type' => 'Type',
            'price' => 'Default Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['name', 'slug', 'type', 'price', 'status', 'created_at'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = MealPlan::whereBetween('created_at', [$start, $end])
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($m) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'name' => $m->name,
                    'slug' => $m->slug,
                    'type' => $m->type,
                    'price' => $m->price ? '$' . number_format($m->price, 2) : '—',
                    'status' => $m->is_active ? 'Active' : 'Inactive',
                    'created_at' => $m->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('MealPlans_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('MealPlans_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view meal plans');
        
        $mealPlans = MealPlan::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $query->where('is_active', $status);
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.meal-plans.meal-plan-list', [
            'mealPlans' => $mealPlans,
        ]);
    }
}
