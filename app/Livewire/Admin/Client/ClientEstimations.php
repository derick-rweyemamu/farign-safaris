<?php

namespace App\Livewire\Admin\Client;

use App\Models\Estimation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin::layouts.client')]
#[Title('My Estimations')]
class ClientEstimations extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'date';
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

    public function updateStatus($id, $status)
    {
        $client = auth()->user()->client;
        if (!$client) {
            abort(403);
        }

        $estimate = Estimation::where('client_id', $client->id)->findOrFail($id);

        if (in_array($estimate->status, ['sent', 'draft'])) {
            $estimate->update(['status' => $status]);

            if ($status === 'accepted' && !$estimate->invoice) {
                // Auto-convert to invoice
                $settings = app(\App\Settings\GeneralSetting::class);

                \Illuminate\Support\Facades\DB::transaction(function () use ($estimate, $settings) {
                    $prefix = rtrim($settings->invoice_prefix, '-');
                    $datePart = date('ymd');
                    $lastInvToday = \App\Models\Invoice::withTrashed()
                        ->where('invoice_number', 'like', "{$prefix}-{$datePart}%")
                        ->orderByRaw('CAST(SUBSTRING_INDEX(invoice_number, "-", -1) AS UNSIGNED) DESC')
                        ->first();

                    if ($lastInvToday) {
                        $parts = explode('-', $lastInvToday->invoice_number);
                        $lastSequence = end($parts);
                        $lastNum = is_numeric($lastSequence) ? (int) $lastSequence : 0;
                        $nextNumber = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
                    } else {
                        $nextNumber = '0001';
                    }
                    $invoiceNumber = "{$prefix}-{$datePart}-{$nextNumber}";

                    $invoice = \App\Models\Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'client_id' => $estimate->client_id,
                        'user_id' => $estimate->created_by ?? 1,
                        'estimate_id' => $estimate->id,
                        'created_date' => now(),
                        'due_date' => now()->addDays($settings->invoice_due_days),
                        'subtotal' => $estimate->subtotal,
                        'tax_total' => $estimate->tax,
                        'discount' => $estimate->discount,
                        'total' => $estimate->total,
                        'balance_due' => $estimate->total,
                        'notes' => $estimate->notes,
                        'status' => 'unpaid',
                        'tax_rate_index' => $estimate->tax_rate_index,
                        'discount_index' => $estimate->discount_index,
                        'currency_code' => $estimate->currency_code, // Preserving original currency
                        'currency_symbol' => $estimate->currency_symbol,
                        'exchange_rate' => $estimate->exchange_rate,
                        'base_grand_total' => $estimate->base_grand_total,
                    ]);

                    foreach ($estimate->items as $item) {
                        $invoice->items()->create([
                            'product_id' => $item->product_id,
                            'item_name' => $item->item_name ?? 'Service/Product',
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'tax' => 0,
                            'discount_value' => $item->discount_value,
                            'discount_type' => $item->discount_type ?? 'value',
                            'tax_applied' => $item->tax_applied,
                            'total' => $item->total,
                        ]);
                    }
                    
                    \App\Models\ActivityLog::record('invoice.created', "Invoice {$invoiceNumber} auto-created because Client accepted Estimation {$estimate->estimate_number}");
                });
                
                $this->dispatch('notify', message: __('Estimation accepted and Invoice automatically created!'), type: 'success');
            } else {
                $this->dispatch('notify', message: __('Estimation has been') . $status . '.', type: 'success');
            }
        } else {
            $this->dispatch('notify', message: __('You cannot change the status of this estimation.'), type: 'error');
        }
    }

    public function render()
    {
        $client = auth()->user()->client;

        if (!$client) {
            abort(403, 'Client account not found.');
        }

        $estimations = Estimation::where('client_id', $client->id)
            ->when($this->search, fn($q) => $q->where('estimate_number', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.client.client-estimations', compact('estimations'));
    }
}
