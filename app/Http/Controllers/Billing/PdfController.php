<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Estimation;
use App\Models\Itinerary;
use App\Models\CostSheet;
use App\Settings\GeneralSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PdfController extends Controller
{
    use AuthorizesRequests;

    public function invoice(Invoice $invoice)
    {
        $user = auth()->user();

        // Allow if user has print permission OR if they are the client who owns this invoice
        if (!$user->can('print invoice')) {
            $client = $user->client;
            abort_if(!$client || $client->id !== $invoice->client_id, 403, 'Unauthorized.');
        }

        $settings = app(GeneralSetting::class);
        $invoice->load(['client', 'items']);
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'settings' => $settings
        ]);

        \App\Models\ActivityLog::record('invoice.pdf_downloaded', "Downloaded PDF for Invoice {$invoice->invoice_number}");

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function receipt(Invoice $invoice)
    {
        $user = auth()->user();

        // Allow if user has print permission OR if they are the client who owns this invoice
        if (!$user->can('print invoice')) {
            $client = $user->client;
            abort_if(!$client || $client->id !== $invoice->client_id, 403, 'Unauthorized.');
        }

        $settings = app(GeneralSetting::class);
        $invoice->load(['client', 'payments']);
        
        $pdf = Pdf::loadView('pdf.receipt', [
            'invoice' => $invoice,
            'settings' => $settings
        ]);

        \App\Models\ActivityLog::record('receipt.pdf_downloaded', "Downloaded Receipt PDF for Invoice {$invoice->invoice_number}");

        return $pdf->download("Receipt-{$invoice->invoice_number}.pdf");
    }

    public function estimate(Estimation $estimate)
    {
        $user = auth()->user();

        // Allow if user has print permission OR if they are the client who owns this estimate
        if (!$user->can('print estimate')) {
            $client = $user->client;
            abort_if(!$client || $client->id !== $estimate->client_id, 403, 'Unauthorized.');
        }

        $settings = app(GeneralSetting::class);
        $estimate->load(['client', 'items']);
        
        $pdf = Pdf::loadView('pdf.estimate', [
            'estimate' => $estimate,
            'settings' => $settings
        ]);

        \App\Models\ActivityLog::record('estimate.pdf_downloaded', "Downloaded PDF for Estimate {$estimate->estimate_number}");

        return $pdf->download("Estimate-{$estimate->estimate_number}.pdf");
    }

    public function itinerary(Itinerary $itinerary)
    {
        $this->authorize('view itineraries');
        $settings = app(GeneralSetting::class);
        $itinerary->load(['days.destination', 'days.accommodation', 'days.mealPlan', 'days.activities', 'days.transports', 'days.animals']);
        
        $pdf = Pdf::loadView('pdf.itinerary', [
            'itinerary' => $itinerary,
            'settings'  => $settings
        ]);

        \App\Models\ActivityLog::record('itinerary.pdf_downloaded', "Downloaded PDF for Itinerary Proposal {$itinerary->reference_number}");

        return $pdf->download("Proposal-{$itinerary->reference_number}.pdf");
    }

    public function profitLoss(Request $request)
    {
        $user = auth()->user();
        if (!$user->can('view profit loss')) {
            abort(403, 'Unauthorized.');
        }

        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $settings = app(GeneralSetting::class);
        $currencyCode = $request->get('currency', $settings->currency_default ?? 'USD');
        $currencyObj = \App\Models\Currency::where('code', $currencyCode)->first();
        $rate = (float)($currencyObj ? $currencyObj->exchange_rate : 1.0);

        $service = new \App\Services\FinancialService();
        $baseReportData = $service->calculateForRange(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );

        $reportData = [
            'revenue' => (float)$baseReportData['revenue'] * $rate,
            'general_expenses' => (float)$baseReportData['general_expenses'] * $rate,
            'invoice_expenses' => (float)$baseReportData['invoice_expenses'] * $rate,
            'payroll' => (float)$baseReportData['payroll'] * $rate,
            'total_expenses' => (float)$baseReportData['total_expenses'] * $rate,
            'net_profit' => (float)$baseReportData['net_profit'] * $rate,
        ];

        $pdf = Pdf::loadView('pdf.profit-loss', [
            'settings' => $settings,
            'reportData' => $reportData,
            'currencyCode' => $currencyCode,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        \App\Models\ActivityLog::record('profit_loss.pdf_downloaded', "Downloaded Profit & Loss PDF for range {$startDate} to {$endDate} in {$currencyCode}");

        return $pdf->download("Profit-Loss-Report-{$currencyCode}.pdf");
    }
}
