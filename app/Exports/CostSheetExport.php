<?php

namespace App\Exports;

use App\Models\CostSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CostSheetExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $costSheet;
    protected $isStandard;

    public function __construct(CostSheet $costSheet)
    {
        $this->costSheet = $costSheet;
        $this->isStandard = ($costSheet->calculation_type ?? 'standard') === 'standard';
    }

    public function array(): array
    {
        $this->costSheet->load(['destination', 'items', 'createdBy']);

        $rows = [];

        // Header Info (Keep for both layouts, combine into single cell to avoid Column A stretch)
        $rows[] = ['Cost Sheet: ' . $this->costSheet->title];
        $rows[] = ['Reference Number: ' . $this->costSheet->reference_number];
        $rows[] = ['Destination: ' . ($this->costSheet->destination?->name ?? '')];
        $rows[] = ['Guests: ' . $this->costSheet->num_adults . ' Adults, ' . $this->costSheet->num_children . ' Children'];
        $rows[] = ['Duration: ' . $this->costSheet->duration_days . ' Days'];
        $rows[] = ['Season: ' . ucfirst($this->costSheet->season)];
        $rows[] = ['Status: ' . ucfirst($this->costSheet->status)];
        $rows[] = []; // Empty row separator

        if ($this->isStandard) {
            // Standard Calculation
            $rows[] = ['Category', 'Description', 'Unit Cost', 'Quantity', 'Line Total'];
            foreach ($this->costSheet->items as $item) {
                $desc = $item->name . ($item->rate_label ? ' - ' . $item->rate_label : '');
                $rows[] = [
                    $item->item_type,
                    $desc,
                    $item->unit_cost,
                    $item->quantity,
                    $item->line_total
                ];
            }
            $rows[] = ['', '', '', 'Subtotal', $this->costSheet->subtotal];
            if ($this->costSheet->markup_percentage > 0) {
                $rows[] = ['', '', '', 'Markup (' . $this->costSheet->markup_percentage . '%)', $this->costSheet->markup_amount];
            }
            $rows[] = ['', '', '', 'Grand Total', $this->costSheet->grand_total];
            
            return $rows;
        }

        // Matrix Calculation matching the requested layout
        $matrixDays = $this->costSheet->matrix_days ?: [];
        $numDays = count($matrixDays);
        $children = max(0, (int) $this->costSheet->num_children);
        
        // Build Header Row
        $header = [
            '7 Pax',
            '6 Pax',
            '5 Pax',
            '4 Pax',
            '3 Pax',
            '2 Pax',
            '1 Pax',
            'Cost Fee'
        ];
        foreach ($matrixDays as $di => $day) {
            $header[] = $day['label'] ?? 'Day ' . ($di + 1);
        }
        $rows[] = $header;

        $colTotals = array_fill(1, 7, 0.0);
        $dayTotals = array_fill(0, $numDays, 0.0);

        foreach ($this->costSheet->matrix_rows ?: [] as $row) {
            $rowData = [];
            
            $rowTotal = 0;
            foreach ($matrixDays as $di => $day) {
                $amt = (float)($row['amounts'][$di] ?? 0);
                $rowTotal += $amt;
            }

            // Group Total Cost for 7 down to 1 Pax
            $isPerPax = $row['is_per_pax'] ?? false;
            for ($a = 7; $a >= 1; $a--) {
                // Calculate total group cost for this line item (no per-person division!)
                $groupCost = $isPerPax ? ($rowTotal * $a) : $rowTotal;
                $rowData[] = round($groupCost, 2);
                $colTotals[$a] += $groupCost;
            }

            // Cost Fee
            $rowData[] = $row['label'] ?? 'Custom';

            // Days
            foreach ($matrixDays as $di => $day) {
                $amt = (float)($row['amounts'][$di] ?? 0);
                $rowData[] = $amt;
                $dayTotals[$di] += $amt;
            }

            $rows[] = $rowData;
        }

        $markupPct = (float) $this->costSheet->markup_percentage;

        // Subtotal Row (or just TOTAL if no markup)
        $subtotalLabel = $markupPct > 0 ? 'Subtotal' : 'TOTAL';
        $subtotalRow = [];
        for ($a = 7; $a >= 1; $a--) {
            $subtotalRow[] = round($colTotals[$a], 2);
        }
        $subtotalRow[] = $subtotalLabel;
        foreach ($matrixDays as $di => $day) {
            $subtotalRow[] = round($dayTotals[$di], 2);
        }
        $rows[] = $subtotalRow;

        // Markup Row
        if ($markupPct > 0) {
            $markupRow = [];
            for ($a = 7; $a >= 1; $a--) {
                $markupRow[] = round($colTotals[$a] * ($markupPct / 100), 2);
            }
            $markupRow[] = 'Markup (' . $markupPct . '%)';
            foreach ($matrixDays as $di => $day) {
                $markupRow[] = round($dayTotals[$di] * ($markupPct / 100), 2);
            }
            $rows[] = $markupRow;

            // Grand Total Row
            $grandTotalRow = [];
            for ($a = 7; $a >= 1; $a--) {
                $grandTotalRow[] = round($colTotals[$a] * (1 + ($markupPct / 100)), 2);
            }
            $grandTotalRow[] = 'TOTAL';
            foreach ($matrixDays as $di => $day) {
                $grandTotalRow[] = round($dayTotals[$di] * (1 + ($markupPct / 100)), 2);
            }
            $rows[] = $grandTotalRow;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        if ($this->isStandard) {
            $sheet->getStyle('A1:B7')->getFont()->setBold(true);
            $sheet->getStyle("A{$highestRow}:E{$highestRow}")->getFont()->setBold(true);
            return [];
        }

        // --- Matrix Layout Styles ---

        // 1. Merge Header Cells to prevent distortion of columns below
        for ($i = 1; $i <= 7; $i++) {
            $sheet->mergeCells("A{$i}:H{$i}");
        }

        // Bold the top info header labels
        $sheet->getStyle('A1:A7')->getFont()->setBold(true);

        // Matrix Header Row (Row 9)
        $sheet->getStyle("A9:{$highestCol}9")->getFont()->setBold(true);
        $sheet->getStyle("A9:{$highestCol}9")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF2F4F8'); // Light gray header background
        
        // 2. Color the column of the currently selected number of pax
        $adults = (int) $this->costSheet->num_adults;
        if ($adults >= 1 && $adults <= 7) {
            // Map 7 Pax -> A, 6 Pax -> B, ..., 1 Pax -> G
            $paxColumns = [7 => 'A', 6 => 'B', 5 => 'C', 4 => 'D', 3 => 'E', 2 => 'F', 1 => 'G'];
            $targetCol = $paxColumns[$adults];

            // Apply Blue Text and Light Blue Background to the entire column in the matrix
            $sheet->getStyle("{$targetCol}9:{$targetCol}{$highestRow}")->getFont()->getColor()->setARGB('FF3B3BFF'); // Blue Text
            $sheet->getStyle("{$targetCol}9:{$targetCol}{$highestRow}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF4F6FF'); // Very Light Blue Background
        }

        // 3. Style the bottom TOTAL row
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFont()->getColor()->setARGB('FFFF0000'); // Red Text
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFDF2F2'); // Very Light Red Background

        // If there is a subtotal/markup before the grand total, bold those too
        if ($this->costSheet->markup_percentage > 0) {
            $sheet->getStyle("A" . ($highestRow - 1) . ":{$highestCol}" . ($highestRow - 1))->getFont()->setBold(true);
            $sheet->getStyle("A" . ($highestRow - 2) . ":{$highestCol}" . ($highestRow - 2))->getFont()->setBold(true);
        }

        return [];
    }
}
