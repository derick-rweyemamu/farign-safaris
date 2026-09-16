<?php

namespace App\Livewire\Admin\Client;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin::layouts.client')]
#[Title('My Payments')]
class ClientPayments extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'payment_date';
    public string $sortDir = 'desc';
    public int $perPage = 10;

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

    public function render()
    {
        $client = auth()->user()->client;

        if (!$client) {
            abort(403, 'Client account not found.');
        }

        $payments = Payment::with('invoice')
            ->where('client_id', $client->id)
            ->when($this->search, fn($q) =>
                $q->where('reference_number', 'like', "%{$this->search}%")
                  ->orWhereHas('invoice', fn($iq) => $iq->where('invoice_number', 'like', "%{$this->search}%"))
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.client.client-payments', compact('payments'));
    }
}
