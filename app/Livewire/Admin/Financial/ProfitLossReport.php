<?php

namespace App\Livewire\Admin\Financial;

use App\Models\ProfitLoss;
use App\Models\Expense;
use App\Models\InvoiceExpense;
use App\Services\FinancialService;
use App\Traits\HasReports;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin::layouts.app')]
#[Title('Profit & Loss Report')]
class ProfitLossReport extends Component
{
    use HasReports;

    public $startDate;
    public $endDate;
    
    public string $selectedCurrency = 'USD';
    public float $exchangeRate = 1.0;
    public string $currencySymbol = '$';

    public $reportData = [];
    public $monthlySnapshots = [];
    
    public string $currentReportType = 'general_expenses';

    public function mount()
    {
        $settings = app(\App\Settings\GeneralSetting::class);
        $this->selectedCurrency = $settings->currency_default ?? 'USD';
        $this->updateCurrencyMeta();

        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->calculateReport();
    }

    public function updatedSelectedCurrency($value): void
    {
        $this->updateCurrencyMeta();
        $this->calculateReport();
    }

    protected function updateCurrencyMeta(): void
    {
        $currency = \App\Models\Currency::where('code', $this->selectedCurrency)->first();
        if ($currency) {
            $this->exchangeRate = (float)($currency->exchange_rate ?: 1.0);
            $this->currencySymbol = $currency->symbol ?: '$';
        } else {
            $this->exchangeRate = 1.0;
            $this->currencySymbol = '$';
        }
    }

    public function calculateReport()
    {
        $this->updateCurrencyMeta();
        $service = new FinancialService();
        $base = $service->calculateForRange(
            Carbon::parse($this->startDate),
            Carbon::parse($this->endDate)
        );

        $rate = $this->exchangeRate;
        $this->reportData = [
            'revenue' => (float)$base['revenue'] * $rate,
            'general_expenses' => (float)$base['general_expenses'] * $rate,
            'invoice_expenses' => (float)$base['invoice_expenses'] * $rate,
            'total_expenses' => (float)$base['total_expenses'] * $rate,
            'net_profit' => (float)$base['net_profit'] * $rate,
            'base_revenue' => (float)$base['revenue'],
            'base_total_expenses' => (float)$base['total_expenses'],
            'base_net_profit' => (float)$base['net_profit'],
        ];

        $this->monthlySnapshots = ProfitLoss::where('year', Carbon::parse($this->startDate)->year)
            ->orderBy('month', 'asc')
            ->get();
    }

    public function syncCurrentMonth()
    {
        $service = new FinancialService();
        $service->syncMonthlySnapshot(now()->month, now()->year);
        $this->calculateReport();
        $this->dispatch('notify', message: __('Current month snapshot updated!'), type: 'success');
    }

    public function openExpenseReportModal($type)
    {
        if (!auth()->user()->can('view profit loss')) {
            abort(403);
        }
        
        $this->currentReportType = $type;
        $this->showReportModal = true;
        
        // For monthly P&L use the full snapshot year range
        if ($type === 'monthly_profit_loss') {
            $this->reportDateRange = 'custom';
            $this->reportStartDate = Carbon::parse($this->startDate)->startOfYear()->format('Y-m-d');
            $this->reportEndDate   = Carbon::parse($this->endDate)->endOfMonth()->format('Y-m-d');
        } else {
            // Use the global start/end dates from the Profit & Loss page as defaults for custom range
            $this->reportDateRange = 'custom';
            $this->reportStartDate = $this->startDate;
            $this->reportEndDate = $this->endDate;
        }
        
        // Select all available columns by default
        $this->selectedColumns = array_keys($this->availableColumns);
        
        $this->resetErrorBag();
    }

    public function getAvailableColumnsProperty(): array
    {
        if ($this->currentReportType === 'general_expenses') {
            return [
                'date' => 'Date',
                'category' => 'Category',
                'user' => 'User',
                'notes' => 'Notes',
                'amount' => 'Amount',
                'currency' => 'Currency',
                'base_amount' => 'Base Amount',
            ];
        }

        if ($this->currentReportType === 'invoice_expenses') {
            return [
                'date' => 'Date',
                'invoice_no' => 'Invoice #',
                'category' => 'Category',
                'notes' => 'Notes',
                'amount' => 'Amount',
                'currency' => 'Currency',
                'base_amount' => 'Base Amount',
            ];
        }

        if ($this->currentReportType === 'monthly_profit_loss') {
            return [
                'period'            => 'Period',
                'revenue'           => 'Revenue (' . $this->selectedCurrency . ')',
                'general_expenses'  => 'General Expenses (' . $this->selectedCurrency . ')',
                'invoice_expenses'  => 'Invoice Expenses (' . $this->selectedCurrency . ')',
                'total_expenses'    => 'Total Expenses (' . $this->selectedCurrency . ')',
                'net_profit'        => 'Net Profit (' . $this->selectedCurrency . ')',
                'profit_margin'     => 'Profit Margin (%)',
            ];
        }

        return [];
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();
        $headers = collect($this->selectedColumns)->map(fn($col) => $this->availableColumns[$col] ?? $col)->toArray();
        $title = str_replace('_', ' ', ucwords($this->currentReportType, '_')) . ' Report';

        // --- Monthly Profit & Loss snapshot export ---
        if ($this->currentReportType === 'monthly_profit_loss') {
            $rate   = $this->exchangeRate;
            $symbol = $this->currencySymbol;

            $snapshots = ProfitLoss::whereBetween('year', [
                    Carbon::parse($start)->year,
                    Carbon::parse($end)->year,
                ])
                ->orderBy('year')->orderBy('month')
                ->get();

            $rowMapper = function($snap) use ($rate) {
                $revenue         = (float)$snap->total_revenue * $rate;
                $genExp          = (float)$snap->total_general_expenses * $rate;
                $invExp          = (float)$snap->total_invoice_expenses * $rate;
                $totalExp        = $genExp + $invExp;
                $netProfit       = (float)$snap->net_profit * $rate;
                $profitMargin    = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

                $row = [];
                foreach ($this->selectedColumns as $col) {
                    $row[] = match($col) {
                        'period'           => date('F', mktime(0, 0, 0, $snap->month, 1)) . ' ' . $snap->year,
                        'revenue'          => number_format($revenue, 2),
                        'general_expenses' => number_format($genExp, 2),
                        'invoice_expenses' => number_format($invExp, 2),
                        'total_expenses'   => number_format($totalExp, 2),
                        'net_profit'       => number_format($netProfit, 2),
                        'profit_margin'    => $profitMargin . '%',
                        default            => '',
                    };
                }
                return $row;
            };

            if ($this->reportFormat === 'pdf') {
                return $this->exportToPdf($title, $headers, $snapshots->toQuery(), $rowMapper);
            }
            return $this->exportToCsv($title, $headers, $snapshots->toQuery(), $rowMapper);
        }

        if ($this->currentReportType === 'general_expenses') {
            $query = Expense::with(['expenseType', 'user'])
                ->whereBetween('expense_date', [$start, $end]);

            $rowMapper = function($expense) {
                $row = [];
                foreach ($this->selectedColumns as $col) {
                    $row[] = match($col) {
                        'date' => $expense->expense_date->format('Y-m-d'),
                        'category' => $expense->expenseType ? $expense->expenseType->name : '-',
                        'user' => $expense->user ? $expense->user->name : '-',
                        'notes' => $expense->notes,
                        'amount' => $expense->amount,
                        'currency' => $expense->currency_code,
                        'base_amount' => number_format($expense->base_amount, 2),
                        default => ''
                    };
                }
                return $row;
            };
        } elseif ($this->currentReportType === 'invoice_expenses') {
            $query = InvoiceExpense::with(['invoice', 'expenseType'])
                ->whereBetween('created_at', [$start, $end]);

            $rowMapper = function($expense) {
                $row = [];
                foreach ($this->selectedColumns as $col) {
                    $row[] = match($col) {
                        'date' => $expense->created_at->format('Y-m-d'),
                        'invoice_no' => $expense->invoice ? $expense->invoice->invoice_number : '-',
                        'category' => $expense->expenseType ? $expense->expenseType->name : '-',
                        'notes' => $expense->notes,
                        'amount' => $expense->amount,
                        'currency' => $expense->currency_code,
                        'base_amount' => number_format($expense->base_amount, 2),
                        default => ''
                    };
                }
                return $row;
            };
        }

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf($title, $headers, $query, $rowMapper);
        }

        return $this->exportToCsv($title, $headers, $query, $rowMapper);
    }

    public function render()
    {
        return view('livewire.admin.financial.profit-loss-report', [
            'currencies' => \App\Models\Currency::where('is_active', true)->get(),
        ]);
    }
}
