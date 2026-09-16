<?php

namespace App\Exports;

use App\Models\Payroll;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExport implements FromArray, ShouldAutoSize, WithStyles
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
        $payrolls = Payroll::with('employee')
            ->whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->orderBy('payment_date', 'asc')
            ->get();

        $rows = [];

        $rows[] = ['Payroll Report'];
        $rows[] = ['Date Range: ' . $this->startDate->format('M d, Y') . ' to ' . $this->endDate->format('M d, Y')];
        $rows[] = [];

        $rows[] = ['Payment Date', 'Employee', 'Pay Period', 'Status', 'Basic Salary', 'Total Earnings', 'Total Deductions', 'Net Salary'];

        $totalNet = 0;

        foreach ($payrolls as $payroll) {
            $period = date("F", mktime(0, 0, 0, $payroll->month, 10)) . ' ' . $payroll->year;
            $rows[] = [
                $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '-',
                $payroll->employee ? $payroll->employee->first_name . ' ' . $payroll->employee->last_name : '-',
                $period,
                ucfirst($payroll->status),
                $payroll->basic_salary,
                $payroll->total_earnings,
                $payroll->total_deductions,
                $payroll->net_salary,
            ];
            $totalNet += (float)$payroll->net_salary;
        }

        $rows[] = [];
        $rows[] = ['', '', '', '', '', '', 'Total Net Salary', $totalNet];

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
