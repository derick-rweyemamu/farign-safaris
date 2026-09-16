<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\InvoiceExpense;
use App\Models\Payroll;
use App\Models\ProfitLoss;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Calculate profit and loss for a specific date range.
     */
    public function calculateForRange(Carbon $startDate, Carbon $endDate)
    {
        $start = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->endOfDay();

        // Revenue: Sum of base_grand_total from Invoices
        $revenue = Invoice::whereBetween('created_date', [$start->toDateString(), $end->toDateString()])
            ->sum('base_grand_total');

        // General Expenses: Sum of base_amount
        $generalExpenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('base_amount');

        // Invoice-specific Expenses: Sum of base_amount
        $invoiceExpenses = InvoiceExpense::whereBetween('created_at', [$start, $end])
            ->sum('base_amount');

        // Payroll: Sum of net_salary
        $payroll = Payroll::whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->sum('net_salary');

        $totalExpenses = (float)$generalExpenses + (float)$invoiceExpenses + (float)$payroll;
        $netProfit = (float)$revenue - $totalExpenses;

        return [
            'revenue' => (float)$revenue,
            'general_expenses' => (float)$generalExpenses,
            'invoice_expenses' => (float)$invoiceExpenses,
            'payroll' => (float)$payroll,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Sync/Update historical ProfitLoss records for a specific month.
     */
    public function syncMonthlySnapshot(int $month, int $year)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $data = $this->calculateForRange($startDate, $endDate);

        return ProfitLoss::updateOrCreate(
            ['month' => $month, 'year' => $year],
            [
                'total_revenue' => $data['revenue'],
                'total_general_expenses' => $data['general_expenses'],
                'total_invoice_expenses' => $data['invoice_expenses'],
                'total_payroll' => $data['payroll'],
                'net_profit' => $data['net_profit'],
            ]
        );
    }
}
