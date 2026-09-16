<?php

namespace App\Livewire\Admin\Financial;

use App\Models\ActivityLog;
use App\Models\ExpenseType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('admin::layouts.app')]
#[Title('Expense Types Management')]
class ExpenseTypesList extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search   = '';
    public string $sortBy   = 'name';
    public string $sortDir  = 'asc';
    public int    $perPage  = 10;

    // Modal state
    public bool   $showModal       = false;
    public bool   $showDeleteModal = false;
    public ?int   $editingId       = null;
    public ?int   $deletingId      = null;

    // Form fields
    public $name;
    public $description;

    public function mount()
    {
        $this->authorize('view expense type');
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:expense_types,name,' . ($this->editingId ?? 'NULL'),
            'description' => 'nullable|string',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create expense type');
        $this->reset(['name', 'description', 'editingId']);
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit expense type');
        $type = ExpenseType::findOrFail($id);
        $this->editingId = $type->id;
        $this->name = $type->name;
        $this->description = $type->description;
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit expense type' : 'create expense type');
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->editingId) {
            $type = ExpenseType::findOrFail($this->editingId);
            $type->update($data);
            ActivityLog::record('expense_type.updated', "Updated expense type: {$this->name}");
        } else {
            ExpenseType::create($data);
            ActivityLog::record('expense_type.created', "Created expense type: {$this->name}");
        }

        $this->dispatch('notify', message: __('Expense Type saved successfully!'), type: 'success');
        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete expense type');
        $this->deletingId       = $id;
        $this->showDeleteModal  = true;
    }

    public function deleteExpenseType(): void
    {
        $this->authorize('delete expense type');
        $type = ExpenseType::findOrFail($this->deletingId);
        
        // Prevent deletion if in use
        if ($type->invoiceExpenses()->exists() || $type->generalExpenses()->exists()) {
            $this->dispatch('notify', message: __('Cannot delete expense type because it is in use.'), type: 'error');
            $this->showDeleteModal = false;
            $this->deletingId = null;
            return;
        }

        $name = $type->name;
        $type->delete();

        ActivityLog::record('expense_type.deleted', "Deleted expense type: {$name}");
        $this->dispatch('notify', message: __('Expense Type deleted successfully.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->resetPage();
    }

    public function render()
    {
        $expenseTypes = ExpenseType::withCount(['invoiceExpenses', 'generalExpenses'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.financial.expense-types-list', compact('expenseTypes'));
    }
}
