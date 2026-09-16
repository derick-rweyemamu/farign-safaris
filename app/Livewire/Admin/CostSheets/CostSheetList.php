<?php

namespace App\Livewire\Admin\CostSheets;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CostSheet;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Cost Sheets')]
class CostSheetList extends Component
{
    use WithPagination, AuthorizesRequests;

    // ── List/Filter State ──────────────────────────────────────────────────

    #[Url(history: true)]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public int $perPage = 10;

    // ── Modal State ────────────────────────────────────────────────────────

    public bool $showDeleteModal  = false;
    public ?int $deletingId       = null;

    // ── Lifecycle / Watchers ──────────────────────────────────────────────

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    // ── Actions ───────────────────────────────────────────────────────────

    public function duplicate(int $id): void
    {
        $this->authorize('create cost sheets');
        $original = CostSheet::with('items')->findOrFail($id);
        $new = $original->replicate(['reference_number']);
        $new->reference_number = null; // will be auto-generated
        $new->title  = 'Copy of ' . $original->title;
        $new->status = 'draft';
        $new->save();

        foreach ($original->items as $item) {
            $new->items()->create($item->only([
                'item_type', 'item_id', 'name', 'rate_label',
                'unit_cost', 'quantity', 'line_total', 'notes', 'sort_order'
            ]));
        }

        $this->dispatch('notify', message: __('Cost sheet duplicated!'), type: 'success');
    }

    public function toggleStatus(int $id): void
    {
        $cs = CostSheet::findOrFail($id);
        if ($cs->status === 'draft') {
            $this->authorize('finalize cost sheets');
            $cs->update(['status' => 'finalized']);
            $this->dispatch('notify', message: __('Cost sheet finalized!'), type: 'success');
        } else {
            $this->authorize('edit cost sheets');
            $cs->update(['status' => 'draft']);
            $this->dispatch('notify', message: __('Cost sheet reverted to draft!'), type: 'success');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete cost sheets');
        $this->deletingId       = $id;
        $this->showDeleteModal  = true;
    }

    public function deleteCostSheet(): void
    {
        $this->authorize('delete cost sheets');
        CostSheet::findOrFail($this->deletingId)->delete();
        $this->dispatch('notify', message: __('Cost sheet deleted!'), type: 'success');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    // ── Render ────────────────────────────────────────────────────────────

    public function render()
    {
        $this->authorize('view cost sheets');

        $costSheets = CostSheet::with('destination')
            ->when($this->search, fn($q) => $q
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                ->orWhereHas('destination', fn($dq) => $dq->where('name', 'like', '%' . $this->search . '%'))
            )
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.cost-sheets.cost-sheet-list', [
            'costSheets' => $costSheets,
        ]);
    }
}
