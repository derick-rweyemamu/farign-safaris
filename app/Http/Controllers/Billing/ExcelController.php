<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\CostSheet;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    use AuthorizesRequests;

    public function costSheet(CostSheet $costSheet)
    {
        // $this->authorize('view cost sheets');

        // Clear output buffer to ensure no stray characters corrupt the Excel file
        if (ob_get_length()) {
            ob_end_clean();
        }

        return Excel::download(
            new \App\Exports\CostSheetExport($costSheet),
            "CostSheet-{$costSheet->reference_number}.xlsx",
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function generalExpenses(Request $request)
    {
        if (!auth()->user()->can('view profit loss')) {
            abort(403);
        }

        $startDate = \Carbon\Carbon::parse($request->get('start_date', now()->startOfYear()->format('Y-m-d')));
        $endDate = \Carbon\Carbon::parse($request->get('end_date', now()->endOfMonth()->format('Y-m-d')));

        if (ob_get_length()) ob_end_clean();

        return Excel::download(
            new \App\Exports\GeneralExpensesExport($startDate, $endDate),
            "General-Expenses-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.xlsx"
        );
    }

    public function invoiceExpenses(Request $request)
    {
        if (!auth()->user()->can('view profit loss')) {
            abort(403);
        }

        $startDate = \Carbon\Carbon::parse($request->get('start_date', now()->startOfYear()->format('Y-m-d')));
        $endDate = \Carbon\Carbon::parse($request->get('end_date', now()->endOfMonth()->format('Y-m-d')));

        if (ob_get_length()) ob_end_clean();

        return Excel::download(
            new \App\Exports\InvoiceExpensesExport($startDate, $endDate),
            "Invoice-Expenses-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.xlsx"
        );
    }

    public function payroll(Request $request)
    {
        if (!auth()->user()->can('view profit loss')) {
            abort(403);
        }

        $startDate = \Carbon\Carbon::parse($request->get('start_date', now()->startOfYear()->format('Y-m-d')));
        $endDate = \Carbon\Carbon::parse($request->get('end_date', now()->endOfMonth()->format('Y-m-d')));

        if (ob_get_length()) ob_end_clean();

        return Excel::download(
            new \App\Exports\PayrollExport($startDate, $endDate),
            "Payroll-{$startDate->format('Y-m-d')}-to-{$endDate->format('Y-m-d')}.xlsx"
        );
    }
}
