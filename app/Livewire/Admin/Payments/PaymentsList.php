<?php
/* [MODIFY] [PaymentsList.php](file:///d:/LOCALSERVER/laragon/www/ mwangwi-safaris/app/Livewire/Payments/PaymentsList.php) */

namespace App\Livewire\Admin\Payments;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Payments Management')]
class PaymentsList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests;

    public string $search = '';
    public string $sortBy = 'payment_date';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public bool $showCancelModal = false;
    public ?int $cancellingId = null;
    public ?int $deletingId = null;

    // Form fields (from PaymentForm)
    public $invoice_id;
    public $client_id;
    public $amount;
    public $payment_date;
    public $payment_method = 'bank_transfer';
    public $reference_number;
    public $notes;
    public $currency_symbol = '$';

    public function mount()
    {
        $this->authorize('view payments');
        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        $this->currency_symbol = $defaultCurrency?->symbol ?? '$';
    }

    protected function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
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
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->authorize('create payment');
        $this->reset(['invoice_id', 'client_id', 'amount', 'payment_method', 'reference_number', 'notes', 'editingId']);
        $this->payment_date = date('Y-m-d');
        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        $this->currency_symbol = $defaultCurrency?->symbol ?? '$';
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit payment');
        $payment = Payment::with('invoice')->findOrFail($id);
        $this->editingId = $payment->id;
        $this->invoice_id = $payment->invoice_id;
        $this->client_id = $payment->client_id;
        $this->amount = $payment->amount;
        $this->payment_date = $payment->payment_date->format('Y-m-d');
        $this->payment_method = $payment->payment_method;
        $this->reference_number = $payment->reference_number;
        $this->notes = $payment->notes;
        $this->currency_symbol = $payment->invoice?->currency_symbol ?? '$';
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function updatedInvoiceId($value)
    {
        if ($value) {
            $invoice = Invoice::find($value);
            if ($invoice) {
                $this->client_id = $invoice->client_id;
                $this->currency_symbol = $invoice->currency_symbol;
                if (!$this->editingId) {
                    $this->amount = $invoice->balance_due;
                }
            }
        } else {
            $settings = app(\App\Settings\GeneralSetting::class);
            $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
            $this->currency_symbol = $defaultCurrency?->symbol ?? '$';
        }
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit payment' : 'create payment');
        $this->validate();

        DB::transaction(function () {
            if ($this->editingId) {
                $payment = Payment::findOrFail($this->editingId);

                // When editing, amount and payment_date are not allowed to change
                $payment->update([
                    'invoice_id' => $this->invoice_id,
                    'client_id' => $this->client_id,
                    'payment_method' => $this->payment_method,
                    'reference_number' => $this->reference_number,
                    'notes' => $this->notes,
                ]);
            } else {
                $payment = Payment::create([
                    'invoice_id' => $this->invoice_id,
                    'client_id' => $this->client_id,
                    'amount' => $this->amount,
                    'payment_date' => $this->payment_date,
                    'payment_method' => $this->payment_method,
                    'reference_number' => $this->reference_number,
                    'notes' => $this->notes,
                ]);
            }

            $newInvoice = Invoice::findOrFail($this->invoice_id);
            if (!$this->editingId) {
                // Apply new amount to invoice balance only on create
                $newInvoice->decrement('balance_due', $this->amount);

                if ($newInvoice->balance_due <= 0) {
                    $newInvoice->update(['status' => 'paid', 'paid_at' => now()]);
                } else {
                    $newInvoice->update(['status' => 'partially_paid', 'paid_at' => null]);
                }
            }

            ActivityLog::record($this->editingId ? 'payment.updated' : 'payment.created', "Payment of {$this->amount} saved for invoice " . $newInvoice->invoice_number);
        });

        $this->dispatch('notify', message: __('Payment saved successfully!'), type: 'success');
        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmCancel(int $id): void
    {
        $this->authorize('delete payment');
        $this->cancellingId = $id;
        $this->showCancelModal = true;
    }

    public function cancelPayment(): void
    {
        $payment = Payment::findOrFail($this->cancellingId);
        $amount = $payment->amount;
        $invoiceNumber = $payment->invoice->invoice_number;

        DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $invoice->increment('balance_due', $payment->amount);

            if ($invoice->balance_due >= $invoice->total) {
                if ($invoice->status !== 'draft') {
                    $invoice->update(['status' => 'unpaid', 'paid_at' => null]);
                }
            } else if ($invoice->balance_due > 0) {
                $invoice->update(['status' => 'partially_paid', 'paid_at' => null]);
            }
            $payment->update(['status' => 'cancelled']);
        });

        ActivityLog::record('payment.cancelled', "Cancelled payment of {$amount} for invoice {$invoiceNumber}");
        $this->dispatch('notify', message: __('Payment cancelled successfully.'), type: 'success');

        $this->showCancelModal = false;
        $this->cancellingId = null;
        $this->resetPage();
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'date' => 'Date',
            'invoice' => 'Invoice #',
            'client' => 'Client',
            'amount' => 'Amount',
            'method' => 'Method',
            'reference' => 'Reference',
            'notes' => 'Notes',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['date', 'invoice', 'client', 'amount', 'method', 'reference'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = Payment::with(['invoice', 'client'])
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$start, $end]);

        $headers = collect($this->selectedColumns)
            ->map(fn($col) => $this->availableColumns[$col] ?? $col)
            ->toArray();

        $rowMapper = function ($payment) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match ($col) {
                    'date' => $payment->payment_date?->format('Y-m-d'),
                    'invoice' => $payment->invoice?->invoice_number ?? 'N/A',
                    'client' => $payment->client?->name ?? 'N/A',
                    'amount' => number_format((float) $payment->amount, 2),
                    'method' => $payment->payment_method,
                    'reference' => $payment->reference_number,
                    'notes' => $payment->notes,
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Payments_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Payments_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $payments = Payment::with(['invoice', 'client'])
            ->when(
                $this->search,
                fn($q) =>
                    $q->where('reference_number', 'like', "%{$this->search}%")
                        ->orWhereHas('invoice', fn($iq) => $iq->where('invoice_number', 'like', "%{$this->search}%"))
                        ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $invoices = Invoice::with('client')->orderBy('invoice_number', 'desc')->get();
        $clients = Client::orderBy('name')->get();

        return view('livewire.admin.payments.payments-list', compact('payments', 'invoices', 'clients'));
    }
}
