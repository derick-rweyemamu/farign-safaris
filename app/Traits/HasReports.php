<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasReports
{
    public bool $showReportModal = false;
    public string $reportFormat = 'csv';
    public string $reportDateRange = 'this_month';
    public ?string $reportStartDate = null;
    public ?string $reportEndDate = null;
    public string $pdfOrientation = 'landscape';
    public array $selectedColumns = [];

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    protected function getReportDateRange(): array
    {
        $start = now();
        $end = now();

        switch ($this->reportDateRange) {
            case 'today':
                $start = now()->startOfDay();
                break;
            case 'yesterday':
                $start = now()->subDay()->startOfDay();
                $end = now()->subDay()->endOfDay();
                break;
            case 'last_7_days':
                $start = now()->subDays(7)->startOfDay();
                break;
            case 'last_30_days':
                $start = now()->subDays(30)->startOfDay();
                break;
            case 'this_month':
                $start = now()->startOfMonth();
                break;
            case 'last_month':
                $start = now()->subMonth()->startOfMonth();
                $end = now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $start = now()->startOfYear();
                break;
            case 'custom':
                $start = Carbon::parse($this->reportStartDate)->startOfDay();
                $end = Carbon::parse($this->reportEndDate)->endOfDay();
                break;
        }

        return [$start, $end];
    }

    protected function exportToCsv(string $filename, array $headers, $query, callable $rowMapper): StreamedResponse
    {
        $this->showReportModal = false;
        return response()->streamDownload(function () use ($headers, $query, $rowMapper) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, $headers);

            $query->chunk(100, function ($records) use ($handle, $rowMapper) {
                foreach ($records as $record) {
                    fputcsv($handle, $rowMapper($record));
                }
            });

            fclose($handle);
        }, $filename . '_' . now()->format('Y-m-d_His') . '.csv');
    }

    protected function exportToPdf(string $title, array $headers, $query, callable $rowMapper)
    {
        [$start, $end] = $this->getReportDateRange();
        $period = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
        
        $data = $query->get()->map($rowMapper)->toArray();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.generic-pdf', [
            'title' => $title,
            'headers' => $headers,
            'data' => $data,
            'period' => $period
        ])->setPaper('a4', $this->pdfOrientation);

        $this->showReportModal = false;
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $title . '_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
