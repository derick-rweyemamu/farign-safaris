<?php

namespace App\Livewire\Admin\Invoices;

use App\Models\ActivityLog;
use App\Models\ExpenseType;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin::layouts.app')]
#[Title('Invoice Expenses Management')]
class InvoiceExpensesList extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $sortBy   = 'created_at';
    public string $sortDir  = 'desc';
    public int    $perPage  = 10;

    // Modal state
    public bool   $showModal             = false;
    public bool   $showDeleteModal       = false;
    public bool   $showDeleteInvoiceModal = false;
    public bool   $showViewDetailsModal  = false;
    public ?int   $editingId             = null;
    public ?int   $deletingId            = null;
    public ?int   $deletingInvoiceId     = null;
    public ?Invoice $viewingInvoice      = null;

    // Form fields
    public $invoice_id;
    public array $items = [];
    
    // Form Metadata (Fetched from Invoice)
    public $currency_code = 'USD';
    public $currency_symbol = '$';
    public $exchange_rate = 1.0;

    public function mount()
    {
        $this->authorize('view invoice expenses');
        $this->resetToDefaultCurrency();
    }

    public function resetToDefaultCurrency(): void
    {
        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = \App\Models\Currency::where('code', $settings->currency_default)->first();
        if ($defaultCurrency) {
            $this->currency_code = $defaultCurrency->code;
            $this->currency_symbol = $defaultCurrency->symbol;
            $this->exchange_rate = (float)$defaultCurrency->exchange_rate;
        } else {
            $this->currency_code = 'USD';
            $this->currency_symbol = '$';
            $this->exchange_rate = 1.0;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'items' => 'required|array|min:1',
            'items.*.expense_type_id' => 'required|exists:expense_types,id',
            'items.*.amount' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string',
        ];
        return $rules;
    }

    protected function messages(): array
    {
        return [
            'items.*.expense_type_id.required' => 'The expense type is required.',
            'items.*.amount.required' => 'The amount is required.',
            'items.*.amount.numeric' => 'The amount must be a number.',
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

    public function updatedInvoiceId($value): void
    {
        if ($value) {
            $invoice = Invoice::find($value);
            if ($invoice) {
                $settings = app(\App\Settings\GeneralSetting::class);
                $this->currency_code = $invoice->currency_code;
                $this->currency_symbol = $invoice->currency_symbol;

                if ($this->currency_code === ($settings->currency_default ?? 'USD')) {
                    $this->exchange_rate = 1.0;
                } else {
                    $currency = \App\Models\Currency::where('code', $invoice->currency_code)->first();
                    $this->exchange_rate = (float)($currency ? $currency->exchange_rate : ($invoice->exchange_rate ?? 1.0));
                }
            }
        } else {
            $this->resetToDefaultCurrency();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'expense_type_id' => '',
            'amount' => '',
            'notes' => ''
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function openCreate(): void
    {
        $this->authorize('create invoice expense');
        $this->reset(['invoice_id', 'items', 'editingId']);
        $this->resetToDefaultCurrency();
        $this->addItem(); // Start with one empty item
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit invoice expense');
        $expense = InvoiceExpense::with('invoice')->findOrFail($id);
        $this->editingId = $expense->id;
        $this->invoice_id = $expense->invoice_id;
        
        $this->currency_code = $expense->currency_code;
        $this->currency_symbol = $expense->currency_symbol;
        $this->exchange_rate = (float)($expense->exchange_rate ?? 1.0);

        $this->items = [
            [
                'expense_type_id' => $expense->expense_type_id,
                'amount' => $expense->amount,
                'notes' => $expense->notes,
            ]
        ];
        
        $this->showModal = true;
        $this->showViewDetailsModal = false; // Close view modal if editing
        $this->resetErrorBag();
    }

    public function openView(int $invoiceId): void
    {
        $this->viewingInvoice = Invoice::with(['client', 'expenses.expenseType'])->findOrFail($invoiceId);
        $this->showViewDetailsModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit invoice expense' : 'create invoice expense');
        $this->validate();

        DB::transaction(function () {
            $invoice = Invoice::findOrFail($this->invoice_id);
            $settings = app(\App\Settings\GeneralSetting::class);
            $defaultCurrency = $settings->currency_default ?? 'USD';

            if ($this->currency_code === $defaultCurrency) {
                $rate = 1.0;
                $this->exchange_rate = 1.0;
            } else {
                $currencyObj = \App\Models\Currency::where('code', $this->currency_code)->first();
                $rate = (float)($currencyObj ? $currencyObj->exchange_rate : ($this->exchange_rate ?: 1.0));
                $this->exchange_rate = $rate;
            }

            foreach ($this->items as $index => $item) {
                $amt = (float)($item['amount'] ?: 0);
                $base_amount = $amt / $rate;

                if ($index === 0 && $this->editingId) {
                    // Update the existing one
                    $expense = InvoiceExpense::findOrFail($this->editingId);
                    $expense->update([
                        'invoice_id' => $this->invoice_id,
                        'expense_type_id' => $item['expense_type_id'],
                        'amount' => $item['amount'],
                        'notes' => $item['notes'],
                        'currency_code' => $this->currency_code,
                        'currency_symbol' => $this->currency_symbol,
                        'exchange_rate' => $this->exchange_rate,
                        'base_amount' => $base_amount,
                    ]);
                } else {
                    // Create new one (either first record if no editingId, or subsequent records)
                    InvoiceExpense::create([
                        'invoice_id' => $this->invoice_id,
                        'expense_type_id' => $item['expense_type_id'],
                        'amount' => $item['amount'],
                        'notes' => $item['notes'],
                        'currency_code' => $this->currency_code,
                        'currency_symbol' => $this->currency_symbol,
                        'exchange_rate' => $this->exchange_rate,
                        'base_amount' => $base_amount,
                    ]);
                }
            }

            $count = count($this->items);
            if ($this->editingId) {
                ActivityLog::record('expense.updated', "Expense(s) updated/added for invoice " . $invoice->invoice_number);
            } else {
                ActivityLog::record('expense.created', "{$count} expense(s) saved for invoice " . $invoice->invoice_number);
            }
        });

        $this->dispatch('notify', message: __('Expense(s) saved successfully!'), type: 'success');
        $this->showModal = false;
        
        // Refresh view modal if it was open
        if ($this->showViewDetailsModal && $this->viewingInvoice) {
            $this->openView($this->viewingInvoice->id);
        }

        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('delete invoice expense');
        $this->deletingId       = $id;
        $this->showDeleteModal  = true;
    }

    public function deleteExpense(): void
    {
        $this->authorize('delete invoice expense');
        $expense = InvoiceExpense::findOrFail($this->deletingId);
        $invoiceId = $expense->invoice_id;
        $amount = $expense->amount;
        $symbol = $expense->currency_symbol;
        $invoiceNumber = $expense->invoice->invoice_number;
        $expense->delete();

        ActivityLog::record('expense.deleted', "Deleted expense of {$symbol}{$amount} for invoice {$invoiceNumber}");
        $this->dispatch('notify', message: __('Expense deleted successfully.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        
        if ($this->showViewDetailsModal && $this->viewingInvoice && $this->viewingInvoice->id == $invoiceId) {
            $this->openView($invoiceId);
        }

        $this->resetPage();
    }

    public function confirmDeleteInvoiceExpenses(int $id): void
    {
        $this->authorize('delete invoice expense');
        $this->deletingInvoiceId = $id;
        $this->showDeleteInvoiceModal = true;
    }

    public function deleteInvoiceExpenses(): void
    {
        $this->authorize('delete invoice expense');
        $invoice = Invoice::findOrFail($this->deletingInvoiceId);
        $count = $invoice->expenses()->count();
        $invoice->expenses()->delete();

        ActivityLog::record('expense.deleted', "Deleted all ({$count}) expenses for invoice {$invoice->invoice_number}");
        $this->dispatch('notify', message: "All {$count} expenses deleted successfully.", type: 'success');

        $this->showDeleteInvoiceModal = false;
        $this->deletingInvoiceId = null;
        $this->resetPage();
    }

    public function render()
    {
        $invoices_with_expenses = Invoice::with(['client'])
            ->withSum('expenses', 'amount')
            ->whereHas('expenses')
            ->when($this->search, fn($q) =>
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
            )
            ->orderBy($this->sortBy === 'amount' ? 'expenses_sum_amount' : $this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $invoices = Invoice::with('client')->orderBy('invoice_number', 'desc')->get();
        $expenseTypes = ExpenseType::orderBy('name')->get();

        return view('livewire.admin.invoices.invoice-expenses-list', [
            'invoices_with_expenses' => $invoices_with_expenses,
            'invoices' => $invoices,
            'expenseTypes' => $expenseTypes
        ]);
    }
}
