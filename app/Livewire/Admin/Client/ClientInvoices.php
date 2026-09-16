<?php

namespace App\Livewire\Admin\Client;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin::layouts.client')]
#[Title('My Invoices')]
class ClientInvoices extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_date';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    public bool $showViewModal = false;
    public ?Invoice $viewingInvoice = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function viewInvoice(int $id): void
    {
        $client = auth()->user()->client;
        if (!$client) {
            abort(403);
        }

        $this->viewingInvoice = Invoice::with(['items', 'client'])
            ->where('client_id', $client->id)
            ->where('status', '!=', 'draft')
            ->findOrFail($id);

        $this->showViewModal = true;
    }

    public function closeView(): void
    {
        $this->showViewModal = false;
        $this->viewingInvoice = null;
    }

    public function render()
    {
        $client = auth()->user()->client;

        if (!$client) {
            abort(403, 'Client account not found.');
        }

        $invoices = Invoice::where('client_id', $client->id)
            ->where('status', '!=', 'draft')
            ->when($this->search, fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.client.client-invoices', compact('invoices'));
    }
}
