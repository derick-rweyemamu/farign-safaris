<?php

namespace App\Livewire\Admin\ActivityLog;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasReports;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Activity Log')]
class ActivityLogList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests;

    public string $search    = '';
    public string $action    = '';
    public string $dateFrom  = '';
    public string $dateTo    = '';
    public int    $perPage   = 20;
    public bool   $showDeleteModal = false;
    public ?int   $deletingId = null;

    public array  $selectedRows = [];
    public bool   $selectAll = false;
    public bool   $showBulkDeleteModal = false;

    public function updatedSearch(): void { $this->resetPage(); $this->resetSelection(); }
    public function updatedAction(): void { $this->resetPage(); $this->resetSelection(); }
    public function updatedDateFrom(): void { $this->resetPage(); $this->resetSelection(); }
    public function updatedDateTo(): void { $this->resetPage(); $this->resetSelection(); }
    public function updatingPage(): void { $this->resetSelection(); }

    public function resetSelection(): void
    {
        $this->selectedRows = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedRows = $this->getLogsQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedRows = [];
        }
    }

    public function confirmBulkDelete(): void
    {
        $this->authorize('delete activities');
        if (count($this->selectedRows) > 0) {
            $this->showBulkDeleteModal = true;
        }
    }

    public function deleteSelected(): void
    {
        $this->authorize('delete activities');
        if (count($this->selectedRows) > 0) {
            ActivityLog::whereIn('id', $this->selectedRows)->delete();
            $this->dispatch('notify', message: count($this->selectedRows) . ' activity records deleted.', type: 'success');
            $this->resetSelection();
            $this->resetPage();
            $this->showBulkDeleteModal = false;
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'action', 'dateFrom', 'dateTo']);
        $this->resetSelection();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete activities');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLog(): void
    {
        $this->authorize('delete activities');
        ActivityLog::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Activity record deleted.'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function openReportModal(): void
    {
        $this->authorize('print activities');
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['user', 'action', 'description', 'ip', 'time'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        $this->authorize('print activities');
        [$start, $end] = $this->getReportDateRange();

        $query = ActivityLog::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('action', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%")
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                )
            )
            ->when($this->action, fn($q) => $q->where('action', $this->action));

        $headers = ['User', 'Action', 'Description', 'IP Address', 'Time'];
        
        $rowMapper = function($log) {
            return [
                $log->user?->name ?? 'System',
                $log->action,
                $log->description,
                $log->ip_address,
                $log->created_at?->format('Y-m-d H:i:s')
            ];
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Activity_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Activity_Report', $headers, $query, $rowMapper);
    }

    private function getLogsQuery()
    {
        return ActivityLog::with('user')
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->where('action', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%")
                      ->orWhere('ip_address', 'like', "%{$this->search}%")
                      ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                )
            )
            ->when($this->action, fn($q) => $q->where('action', $this->action))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest();
    }

    public function render()
    {
        $this->authorize('view activities');
        $logs = $this->getLogsQuery()->paginate($this->perPage);

        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();

        return view('livewire.admin.activity-log.activity-log-list', compact('logs', 'actions'));
    }
}
