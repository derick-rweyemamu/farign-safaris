<?php

namespace App\Exports;

use App\Models\InvoiceExpense;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExpensesExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $expenses = InvoiceExpense::with(['invoice', 'expenseType'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $rows = [];

        $rows[] = ['Invoice / Tour Expenses Report'];
        $rows[] = ['Date Range: ' . $this->startDate->format('M d, Y') . ' to ' . $this->endDate->format('M d, Y')];
        $rows[] = [];

        $rows[] = ['Date', 'Invoice No.', 'Type', 'Notes', 'Amount', 'Currency', 'Base Amount'];

        $totalBase = 0;

        foreach ($expenses as $expense) {
            $rows[] = [
                $expense->created_at->format('Y-m-d'),
                $expense->invoice ? $expense->invoice->invoice_number : '-',
                $expense->expenseType ? $expense->expenseType->name : '-',
                $expense->notes,
                $expense->amount,
                $expense->currency_code,
                $expense->base_amount,
            ];
            $totalBase += (float)$expense->base_amount;
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', '', 'Total (Base)', $totalBase];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            4 => ['font' => ['bold' => true]],
        ];
    }
}
