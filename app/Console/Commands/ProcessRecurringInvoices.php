<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Settings\GeneralSetting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('invoices:process-recurring')]
#[Description('Generate new invoices from recurring templates that are due today.')]
class ProcessRecurringInvoices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(GeneralSetting $settings)
    {
        $today = Carbon::today();

        $invoices = Invoice::where('is_recurring', true)
            ->whereDate('recurring_next_date', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('recurring_end_date')
                      ->orWhereDate('recurring_end_date', '>=', $today);
            })
            ->with('items')
            ->get();

        $count = 0;
        foreach ($invoices as $parent) {
            // Determine next number
            $prefix = rtrim($settings->invoice_prefix, '-');
            $datePart = date('ymd');

            $lastInvToday = Invoice::withTrashed()
                ->where('invoice_number', 'like', "{$prefix}-{$datePart}%")
                ->orderByRaw('CAST(SUBSTRING_INDEX(invoice_number, "-", -1) AS UNSIGNED) DESC')
                ->first();

            if ($lastInvToday) {
                $parts = explode('-', $lastInvToday->invoice_number);
                $lastSequence = end($parts);
                $lastNum = is_numeric($lastSequence) ? (int) $lastSequence : 0;
                $nextNumber = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $newInvoiceNumber = "{$prefix}-{$datePart}-{$nextNumber}";

            // Duplicate the invoice
            $newInvoice = $parent->replicate([
                'is_recurring',
                'recurring_frequency',
                'recurring_next_date',
                'recurring_end_date',
                'sent_at',
                'paid_at'
            ]);

            $newInvoice->invoice_number = $newInvoiceNumber;
            $newInvoice->created_date = $today;
            $newInvoice->due_date = $today->copy()->addDays($settings->invoice_due_days);
            $newInvoice->status = 'draft';
            $newInvoice->balance_due = $parent->total;
            $newInvoice->parent_id = $parent->id;
            $newInvoice->save();

            // Duplicate items
            foreach ($parent->items as $item) {
                $newItem = $item->replicate(['invoice_id']);
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }

            // Update parent next date
            $nextDate = Carbon::parse($parent->recurring_next_date);
            switch ($parent->recurring_frequency) {
                case 'weekly':
                    $nextDate->addWeek();
                    break;
                case 'monthly':
                    $nextDate->addMonth();
                    break;
                case 'quarterly':
                    $nextDate->addMonths(3);
                    break;
                case 'yearly':
                    $nextDate->addYear();
                    break;
            }
            
            $parent->recurring_next_date = $nextDate;
            $parent->save();

            $count++;
            $this->info("Generated recurring invoice {$newInvoiceNumber} for Client ID: {$parent->client_id}");
        }

        $this->info("Successfully processed {$count} recurring invoices.");
    }
}
