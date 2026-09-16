<?php

namespace App\Exports;

use App\Models\Expense;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralExpensesExport implements FromArray, ShouldAutoSize, WithStyles
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
        $expenses = Expense::with(['expenseType', 'user'])
            ->whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->orderBy('expense_date', 'asc')
            ->get();

        $rows = [];

        $rows[] = ['General Expenses Report'];
        $rows[] = ['Date Range: ' . $this->startDate->format('M d, Y') . ' to ' . $this->endDate->format('M d, Y')];
        $rows[] = [];

        $rows[] = ['Date', 'Type', 'User', 'Reference', 'Notes', 'Amount', 'Currency', 'Base Amount'];

        $totalBase = 0;

        foreach ($expenses as $expense) {
            $rows[] = [
                $expense->expense_date->format('Y-m-d'),
                $expense->expenseType ? $expense->expenseType->name : '-',
                $expense->user ? $expense->user->name : '-',
                $expense->reference_no,
                $expense->notes,
                $expense->amount,
                $expense->currency_code,
                $expense->base_amount,
            ];
            $totalBase += (float)$expense->base_amount;
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', '', '', 'Total (Base)', $totalBase];

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
