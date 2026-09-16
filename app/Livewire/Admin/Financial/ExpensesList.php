<?php

namespace App\Livewire\Admin\Financial;

use App\Models\ActivityLog;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Settings\GeneralSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('General Expenses Management')]
class ExpensesList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests;

    public string $search   = '';
    public string $sortBy   = 'expense_date';
    public string $sortDir  = 'desc';
    public int    $perPage  = 10;
    public string $typeFilter = '';

    // Modal state
    public bool   $showModal       = false;
    public bool   $showDeleteModal = false;
    public ?int   $editingId       = null;
    public ?int   $deletingId      = null;

    // Form fields
    public $expense_type_id;
    public $expense_date;
    public $amount;
    public $reference_no;
    public $notes;

    // Multi-Currency
    public $currency_code;
    public $currency_symbol;
    public $exchange_rate = 1.0;
    public $base_amount = 0;

    public function mount()
    {
        $this->authorize('view expenses');
    }

    protected function rules(): array
    {
        return [
            'expense_type_id' => 'required|exists:expense_types,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'currency_code' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
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

    public function openCreate(GeneralSetting $settings): void
    {
        $this->authorize('create expense');
        $this->reset(['expense_type_id', 'expense_date', 'amount', 'reference_no', 'notes', 'currency_code', 'currency_symbol', 'exchange_rate', 'base_amount', 'editingId']);
        
        $this->expense_date = date('Y-m-d');

        // Load default currency
        $defaultCurrency = Currency::where('code', $settings->currency_default)->first();
        if ($defaultCurrency) {
            $this->currency_code = $defaultCurrency->code;
            $this->currency_symbol = $defaultCurrency->symbol;
            $this->exchange_rate = $defaultCurrency->exchange_rate;
        }

        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit expense');
        $expense = Expense::findOrFail($id);
        $this->editingId = $expense->id;
        $this->expense_type_id = $expense->expense_type_id;
        $this->expense_date = $expense->expense_date?->format('Y-m-d');
        $this->amount = $expense->amount;
        $this->reference_no = $expense->reference_no;
        $this->notes = $expense->notes;

        $this->currency_code = $expense->currency_code;
        $this->currency_symbol = $expense->currency_symbol;
        $this->exchange_rate = (float)($expense->exchange_rate ?? 1.0);
        $this->base_amount = (float)($expense->base_amount ?? 0.0);
        
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function updatedCurrencyCode($value)
    {
        $currency = Currency::where('code', $value)->first();
        if ($currency) {
            $this->currency_symbol = $currency->symbol;
            $this->exchange_rate = $currency->exchange_rate;
            $this->calculateBaseAmount();
        }
    }

    public function updatedAmount()
    {
        $this->calculateBaseAmount();
    }

    public function updatedExchangeRate()
    {
        $this->calculateBaseAmount();
    }

    public function calculateBaseAmount()
    {
        $settings = app(\App\Settings\GeneralSetting::class);
        $defaultCurrency = $settings->currency_default ?? 'USD';

        if ($this->currency_code === $defaultCurrency) {
            $this->exchange_rate = 1.0;
            $rate = 1.0;
        } else {
            $rate = (float)($this->exchange_rate ?: 1.0);
        }

        $amt = (float)($this->amount ?: 0);
        $this->base_amount = $amt / $rate;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit expense' : 'create expense');
        $this->validate();
        $this->calculateBaseAmount();

        DB::transaction(function () {
            $data = [
                'expense_type_id' => $this->expense_type_id,
                'expense_date' => $this->expense_date,
                'amount' => $this->amount,
                'reference_no' => $this->reference_no,
                'notes' => $this->notes,
                'currency_code' => $this->currency_code,
                'currency_symbol' => $this->currency_symbol,
                'exchange_rate' => $this->exchange_rate,
                'base_amount' => $this->base_amount,
            ];

            if ($this->editingId) {
                $expense = Expense::findOrFail($this->editingId);
                $expense->update($data);
            } else {
                $data['user_id'] = auth()->id();
                $expense = Expense::create($data);
            }

            ActivityLog::record($this->editingId ? 'general_expense.updated' : 'general_expense.created', "General Expense of {$this->currency_symbol}{$this->amount} saved.");
        });

        $this->dispatch('notify', message: __('Expense saved successfully!'), type: 'success');
        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId       = $id;
        $this->showDeleteModal  = true;
    }

    public function deleteExpense(): void
    {
        $this->authorize('delete expense');
        $expense = Expense::findOrFail($this->deletingId);
        $amount = $expense->amount;
        $symbol = $expense->currency_symbol;
        $expense->delete();

        ActivityLog::record('general_expense.deleted', "Deleted general expense of {$symbol}{$amount}");
        $this->dispatch('notify', message: __('Expense deleted successfully.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->resetPage();
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'date' => 'Date',
            'type' => 'Type',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'reference' => 'Reference',
            'notes' => 'Notes',
        ];
    }

    public function openReportModal(): void
    {
        $this->authorize('view expense reports');
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['date', 'type', 'amount', 'currency', 'reference'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = Expense::with('expenseType')
            ->whereBetween('expense_date', [$start, $end])
            ->when($this->typeFilter, fn($q) => $q->where('expense_type_id', $this->typeFilter));

        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();

        $rowMapper = function($expense) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match($col) {
                    'date' => $expense->expense_date?->format('Y-m-d'),
                    'type' => $expense->expenseType?->name ?? 'N/A',
                    'amount' => number_format($expense->amount, 2),
                    'currency' => $expense->currency_code,
                    'reference' => $expense->reference_no,
                    'notes' => $expense->notes,
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Expenses_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Expenses_Report', $headers, $query, $rowMapper);
    }

    public function render()
    {
        $expenses = Expense::with(['expenseType', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('reference_no', 'like', "%{$this->search}%")
                  ->orWhere('notes', 'like', "%{$this->search}%")
                  ->orWhereHas('expenseType', fn($eq) => $eq->where('name', 'like', "%{$this->search}%"))
            )
            ->when($this->typeFilter, fn($q) =>
                $q->where('expense_type_id', $this->typeFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $expenseTypes = ExpenseType::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('livewire.admin.financial.expenses-list', compact('expenses', 'expenseTypes', 'currencies'));
    }
}
