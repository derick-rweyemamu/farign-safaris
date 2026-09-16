<?php

namespace App\Livewire\Admin\Transports;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transport;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Transport')]
class TransportList extends Component
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

    public $transport_id;
    public $slug;
    public $type = 'transfer';
    public $price = 0;
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
        $this->transport_id = null;
        $this->initLocaleTranslations();
        $this->slug = '';
        $this->type = 'transfer';
        $this->price = 0;
        $this->is_active = true;
        $this->isEdit = false;
    }

    public function create()
    {
        $this->authorize('create transports');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit transports');
        $this->resetFields();
        $transport = Transport::findOrFail($id);
        $this->transport_id = $transport->id;
        $this->loadTranslationsFromModel($transport, ['name', 'description']);
        $this->slug = $transport->slug;
        $this->type = $transport->type;
        $this->price = $transport->price;
        $this->is_active = $transport->is_active;
        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->authorize($this->isEdit ? 'edit transports' : 'create transports');
        $this->validate([
            'names.en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:transports,slug,' . $this->transport_id,
            'type' => 'required|string|in:transfer,excursion',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ], ['names.en.required' => 'The English name is required.']);
        $data = ['slug' => $this->slug, 'type' => $this->type, 'price' => $this->price, 'is_active' => $this->is_active];
        if ($this->isEdit) {
            $t = Transport::find($this->transport_id);
            $t->fill($data);
            $this->applyTranslationsToModel($t, ['name', 'description']);
            $this->dispatch('notify', message: __('Transport updated successfully!'), type: 'success');
        } else {
            $t = new Transport($data);
            $this->applyTranslationsToModel($t, ['name', 'description']);
            $this->dispatch('notify', message: __('Transport created successfully!'), type: 'success');
        }
        $this->showModal = false;
        $this->resetFields();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete transports');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteTransport(): void
    {
        $this->authorize('delete transports');
        Transport::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Transport deleted successfully!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function toggleActive($id)
    {
        $this->authorize('edit transports');
        $transport = Transport::findOrFail($id);
        $transport->update(['is_active' => !$transport->is_active]);
        $this->dispatch('notify', message: __('Transport status updated!'), type: 'success');
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'name' => 'Name',
            'slug' => 'Slug',
            'type' => 'Type',
            'price' => 'Price',
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

        $query = Transport::whereBetween('created_at', [$start, $end])
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter, function ($q) {
                $status = $this->statusFilter === 'active' ? 1 : 0;
                $q->where('is_active', $status);
            });

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($t) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'name' => $t->name,
                    'slug' => $t->slug,
                    'type' => ucfirst($t->type),
                    'price' => $t->price ? '$' . number_format($t->price, 2) : '—',
                    'status' => $t->is_active ? 'Active' : 'Inactive',
                    'created_at' => $t->created_at?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Transports_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Transports_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $this->authorize('view transports');
        
        $transports = Transport::query()
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

        return view('livewire.admin.transports.transport-list', [
            'transports' => $transports,
        ]);
    }
}
