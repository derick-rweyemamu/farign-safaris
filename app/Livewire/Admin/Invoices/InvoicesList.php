<?php
/* [MODIFY] [InvoicesList.php](file:///d:/LOCALSERVER/laragon/www/ mwangwi-safaris/app/Livewire/Invoices/InvoicesList.php) */

namespace App\Livewire\Admin\Invoices;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Estimation;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Settings\GeneralSetting;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Invoices Management')]
class InvoicesList extends Component
{
    use WithPagination, HasReports, AuthorizesRequests;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal = false;
    public bool $showPaymentModal = false;
    public bool $showCancelModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $cancellingId = null;
    public ?int $payingInvoiceId = null;
    public ?Invoice $viewingInvoice = null;

    // Payment fields
    public string $payment_type = 'full';
    public $payment_amount = 0;
    public $payment_date;
    public $payment_method = '';
    public $payment_reference = '';
    public $payment_notes = '';

    // Form fields (from InvoiceForm)
    public string $target_type = 'client';
    public $client_id;
    public $custom_name;
    public $invoice_number;
    public $created_date;
    public $due_date;
    public $status = 'draft';
    public $notes;
    public $terms;
    public string $invoice_type = 'product'; // product or tour
    public array $items = [];
    public $subtotal = 0;
    public $discount = 0;
    public $tax_total = 0;
    public $total = 0;
    public $balance_due = 0;

    // Multi-Currency
    public $currency_code;
    public $currency_symbol;
    public $exchange_rate = 1.0;
    public $base_grand_total = 0;

    // Settings based selections
    public $tax_rate_index = null;
    public $discount_index = null;

    // Recurring fields
    public $is_recurring = false;
    public $recurring_frequency = 'monthly';
    public $recurring_next_date;
    public $recurring_end_date;

    public function mount()
    {
        $this->authorize('view invoices');
    }

    protected function rules(): array
    {
        return [
            'target_type' => 'required|in:client,custom',
            'client_id' => 'required_if:target_type,client|nullable|exists:clients,id',
            'custom_name' => 'required_if:target_type,custom|nullable|string|max:255',
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . ($this->editingId ?? 'NULL'),
            'created_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:created_date',
            'status' => 'required|in:draft,sent,paid,unpaid,overdue,cancelled,refunded,partially_paid',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax_total' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'invoice_type' => 'required|in:product,tour',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.tour_id' => $this->invoice_type === 'tour' ? 'required|exists:tours,id' : 'nullable|exists:tours,id',
            'items.*.adults' => 'nullable|numeric|min:0',
            'items.*.children' => 'nullable|numeric|min:0',
            'items.*.item_name' => $this->invoice_type === 'product' ? 'required|string' : 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'required|in:rate,value',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,quarterly,yearly',
            'recurring_next_date' => 'nullable|required_if:is_recurring,true|date',
            'recurring_end_date' => 'nullable|date|after:recurring_next_date',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $settings = app(GeneralSetting::class);
        $this->authorize('create invoice');
        $this->reset(['target_type', 'client_id', 'custom_name', 'created_date', 'due_date', 'status', 'notes', 'terms', 'items', 'subtotal', 'discount', 'tax_total', 'total', 'balance_due', 'editingId', 'tax_rate_index', 'discount_index', 'currency_code', 'currency_symbol', 'exchange_rate', 'base_grand_total', 'is_recurring', 'recurring_frequency', 'recurring_next_date', 'recurring_end_date', 'invoice_type']);
        $this->target_type = 'client';

        $this->notes = $settings->invoice_note;
        $this->terms = $settings->invoice_terms;
        $this->recurring_frequency = 'monthly';

        $prefix = rtrim($settings->invoice_prefix, '-');
        $datePart = date('ymd');

        // Robustly find the last number for today, including soft-deleted ones to avoid unique constraint issues
        $lastInvToday = Invoice::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}-{$datePart}%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(invoice_number, "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvToday) {
            $parts = explode('-', $lastInvToday->invoice_number);
            $lastSequence = end($parts);
            // Ensure we only increment if the last part is actually numeric
            $lastNum = is_numeric($lastSequence) ? (int) $lastSequence : 0;
            $nextNumber = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        $this->invoice_number = "{$prefix}-{$datePart}-{$nextNumber}";
        $this->created_date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime("+{$settings->invoice_due_days} days"));
        $this->status = 'draft';

        $this->addItem();

        // Load default currency
        $defaultCurrency = Currency::where('code', $settings->currency_default)->first();
        if ($defaultCurrency) {
            $this->currency_code = $defaultCurrency->code;
            $this->currency_symbol = $defaultCurrency->symbol;
            $this->exchange_rate = $defaultCurrency->exchange_rate;
        }

        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openEdit(int $id): void
    {
        $this->authorize('edit invoice');
        $invoice = Invoice::with('items')->findOrFail($id);
        $this->editingId = $invoice->id;
        $this->client_id = $invoice->client_id;
        $this->invoice_number = $invoice->invoice_number;
        $this->created_date = $invoice->created_date?->format('Y-m-d');
        $this->due_date = $invoice->due_date?->format('Y-m-d');
        $this->status = $invoice->status;
        $this->notes = $invoice->notes;
        $this->terms = $invoice->terms;
        $this->discount = $invoice->discount;
        $this->tax_total = $invoice->tax_total;
        $this->tax_rate_index = $invoice->tax_rate_index;
        $this->discount_index = $invoice->discount_index;

        // Currency
        $this->currency_code = $invoice->currency_code;
        $this->currency_symbol = $invoice->currency_symbol;
        $this->exchange_rate = (float) ($invoice->exchange_rate ?? 1.0);
        $this->base_grand_total = (float) ($invoice->base_grand_total ?? 0.0);

        $this->is_recurring = $invoice->is_recurring;
        $this->recurring_frequency = $invoice->recurring_frequency ?: 'monthly';
        $this->recurring_next_date = $invoice->recurring_next_date ? \Illuminate\Support\Carbon::parse($invoice->recurring_next_date)->format('Y-m-d') : null;
        $this->recurring_end_date = $invoice->recurring_end_date ? \Illuminate\Support\Carbon::parse($invoice->recurring_end_date)->format('Y-m-d') : null;

        $this->invoice_type = 'product';

        $this->items = [];
        foreach ($invoice->items as $item) {
            if ($item->tour_id) {
                $this->invoice_type = 'tour';
            }
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'tour_id' => $item->tour_id,
                'item_name' => $item->item_name,
                'adults' => $item->adults,
                'children' => $item->children,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_value' => $item->discount_value,
                'discount_type' => $item->discount_type ?? 'value',
                'tax_applied' => $item->tax_applied,
                'total' => $item->total,
            ];
        }
        $this->calculateTotals();
        $this->showModal = true;
        $this->resetErrorBag();
    }

    public function openView(int $id): void
    {
        $this->viewingInvoice = Invoice::with(['client', 'items.product'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function updatedInvoiceType()
    {
        $this->items = [];
        $this->addItem();
    }

    public function updatedItems($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'product_id' && !empty($value)) {
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['item_name'] = $product->name;
                    $rate = (float) ($this->exchange_rate ?: 1.0);
                    $this->items[$index]['unit_price'] = round($product->unit_price * $rate, 2);
                }
            }
            if ($field === 'tour_id' && !empty($value)) {
                $tour = \App\Models\Tour::find($value);
                if ($tour) {
                    $this->items[$index]['item_name'] = 'Tour Package: ' . $tour->name;
                    $rate = (float) ($this->exchange_rate ?: 1.0);
                    $adults = (int) ($this->items[$index]['adults'] ?? 1);
                    $children = (int) ($this->items[$index]['children'] ?? 0);
                    $adultPrice = (float) ($tour->adult_price ?? $tour->price ?? 0);
                    $childPrice = (float) ($tour->child_price ?? 0);
                    $this->items[$index]['unit_price'] = round(($adultPrice * $adults + $childPrice * $children) * $rate, 2);
                    $this->items[$index]['quantity'] = 1;
                }
            }
            if (in_array($field, ['adults', 'children'])) {
                $tourId = $this->items[$index]['tour_id'] ?? null;
                if ($tourId) {
                    $tour = \App\Models\Tour::find($tourId);
                    if ($tour) {
                        $rate = (float) ($this->exchange_rate ?: 1.0);
                        $adults = (int) ($this->items[$index]['adults'] ?? 1);
                        $children = (int) ($this->items[$index]['children'] ?? 0);
                        $adultPrice = (float) ($tour->adult_price ?? $tour->price ?? 0);
                        $childPrice = (float) ($tour->child_price ?? 0);
                        $this->items[$index]['unit_price'] = round(($adultPrice * $adults + $childPrice * $children) * $rate, 2);
                    }
                }
            }
            $this->calculateTotals();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'product_id' => '',
            'tour_id' => '',
            'item_name' => '',
            'adults' => 1,
            'children' => 0,
            'quantity' => 1,
            'unit_price' => 0,
            'discount_value' => 0,
            'discount_type' => 'value',
            'tax_applied' => true,
            'total' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }



    public function updatedDiscountIndex()
    {
        $this->calculateTotals();
    }
    public function updatedTaxRateIndex()
    {
        $this->calculateTotals();
    }

    public function updatedCreatedDate($value)
    {
        if ($value) {
            $settings = app(GeneralSetting::class);
            $this->due_date = \Carbon\Carbon::parse($value)->addDays($settings->invoice_due_days)->format('Y-m-d');
        }
    }

    public function updatedCurrencyCode($value)
    {
        $currency = Currency::where('code', $value)->first();
        if ($currency) {
            $this->currency_symbol = $currency->symbol;

            $oldRate = (float) ($this->exchange_rate ?: 1.0);
            $newRate = (float) ($currency->exchange_rate ?: 1.0);

            foreach ($this->items as $i => $item) {
                if (isset($item['unit_price'])) {
                    $basePrice = $item['unit_price'] / $oldRate;
                    $this->items[$i]['unit_price'] = round($basePrice * $newRate, 2);
                }
                if (isset($item['discount_value']) && ($item['discount_type'] ?? 'value') === 'value') {
                    $baseDiscount = (float) $item['discount_value'] / $oldRate;
                    $this->items[$i]['discount_value'] = round($baseDiscount * $newRate, 2);
                }
            }

            $this->exchange_rate = $currency->exchange_rate;
            $this->calculateTotals();
        }
    }

    public function updatedExchangeRate()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $settings = app(GeneralSetting::class);
        $selectedTax = $this->tax_rate_index !== null && isset($settings->taxes[$this->tax_rate_index])
            ? $settings->taxes[$this->tax_rate_index]
            : null;

        $this->subtotal = 0;
        $this->tax_total = 0;
        $this->discount = 0;

        foreach ($this->items as $index => $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $itemDiscountType = $item['discount_type'] ?? 'value';
            $itemDiscountInput = (float) ($item['discount_value'] ?? 0);

            // Dynamic clamping to prevent invalid values
            if ($itemDiscountType === 'rate') {
                if ($itemDiscountInput > 100) {
                    $itemDiscountInput = 100;
                    $this->items[$index]['discount_value'] = 100;
                }
                if ($itemDiscountInput < 0) {
                    $itemDiscountInput = 0;
                    $this->items[$index]['discount_value'] = 0;
                }
                $itemDiscount = ($qty * $price) * ($itemDiscountInput / 100);
            } else {
                if ($itemDiscountInput < 0) {
                    $itemDiscountInput = 0;
                    $this->items[$index]['discount_value'] = 0;
                }
                if ($itemDiscountInput > $price) {
                    $itemDiscountInput = $price;
                    $this->items[$index]['discount_value'] = $price;
                }
                $itemDiscount = $itemDiscountInput * $qty;
            }

            $lineSubtotal = $qty * $price;
            $lineTotalBeforeTax = $lineSubtotal - $itemDiscount;

            $lineTax = 0;
            if (($item['tax_applied'] ?? false) && $selectedTax) {
                $lineTax = $lineTotalBeforeTax * ($selectedTax['rate'] / 100);
            }

            $this->items[$index]['total'] = $lineTotalBeforeTax + $lineTax;

            $this->subtotal += $lineSubtotal;
            $this->tax_total += $lineTax;
            $this->discount += $itemDiscount;
        }

        $this->total = $this->subtotal - $this->discount + $this->tax_total;
        if ($this->total < 0)
            $this->total = 0;
        $this->balance_due = $this->total;

        // Calculate base total (Total / Exchange Rate)
        $defaultCurrency = $settings->currency_default ?? 'USD';
        if ($this->currency_code === $defaultCurrency) {
            $this->exchange_rate = 1.0;
            $rate = 1.0;
        } else {
            $rate = (float) ($this->exchange_rate ?: 1.0);
        }
        $this->base_grand_total = $this->total / $rate;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit invoice' : 'create invoice');
        $this->validate();
        $this->calculateTotals();

        DB::transaction(function () {
            if ($this->target_type === 'custom' && $this->custom_name) {
                $fakeEmail = 'client_' . uniqid() . '@placeholder.com';
                $client = \App\Models\Client::create([
                    'name' => $this->custom_name,
                    'email' => $fakeEmail,
                    'status' => \App\Enums\ClientStatus::ACTIVE,
                ]);
                $this->client_id = $client->id;
                $this->target_type = 'client';
            }

            $data = [
                'client_id' => $this->client_id,
                'invoice_number' => $this->invoice_number,
                'created_date' => $this->created_date,
                'due_date' => $this->due_date,
                'status' => $this->status,
                'notes' => $this->notes,
                'terms' => $this->terms,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount ?: 0,
                'tax_total' => $this->tax_total ?: 0,
                'total' => $this->total,
                'balance_due' => $this->balance_due,
                'tax_rate_index' => $this->tax_rate_index,
                'discount_index' => $this->discount_index,
                'currency_code' => $this->currency_code,
                'currency_symbol' => $this->currency_symbol,
                'exchange_rate' => $this->exchange_rate,
                'base_grand_total' => $this->base_grand_total,
                'is_recurring' => $this->is_recurring,
                'recurring_frequency' => $this->is_recurring ? $this->recurring_frequency : null,
                'recurring_next_date' => $this->is_recurring ? $this->recurring_next_date : null,
                'recurring_end_date' => $this->is_recurring ? $this->recurring_end_date : null,
            ];

            if ($this->editingId) {
                $invoice = Invoice::findOrFail($this->editingId);
                $invoice->update($data);
                $invoice->items()->delete();
            } else {
                $data['user_id'] = auth()->id();
                $invoice = Invoice::create($data);
            }

            foreach ($this->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'tour_id' => ($item['tour_id'] ?? '') ?: null,
                    'adults' => $item['adults'] ?? 1,
                    'children' => $item['children'] ?? 0,
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_value' => $item['discount_value'] ?: 0,
                    'discount_type' => $item['discount_type'] ?? 'value',
                    'tax_applied' => $item['tax_applied'] ?? true,
                    'total' => $item['total'],
                ]);
            }

            ActivityLog::record($this->editingId ? 'invoice.updated' : 'invoice.created', "Invoice {$this->invoice_number} saved.");
        });

        $this->dispatch('notify', message: __('Invoice saved successfully!'), type: 'success');
        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmCancel(int $id): void
    {
        $this->authorize('edit invoice');
        $this->cancellingId = $id;
        $this->showCancelModal = true;
    }

    public function openPaymentModal($id)
    {
        $this->authorize('edit invoice');
        $invoice = Invoice::findOrFail($id);
        $this->payingInvoiceId = $invoice->id;
        $this->payment_type = 'full';
        $this->payment_amount = $invoice->balance_due;
        $this->payment_date = date('Y-m-d');
        $this->payment_method = '';
        $this->payment_reference = '';
        $this->payment_notes = '';
        $this->resetValidation();
        $this->showPaymentModal = true;
    }

    public function updatedPaymentType()
    {
        if ($this->payingInvoiceId) {
            $invoice = Invoice::find($this->payingInvoiceId);
            if ($this->payment_type === 'full') {
                $this->payment_amount = $invoice->balance_due;
            }
        }
    }

    public function recordPayment()
    {
        $this->authorize('edit invoice');

        $this->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
            'payment_notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($this->payingInvoiceId);

        if ($this->payment_amount > $invoice->balance_due) {
            $this->addError('payment_amount', __('Amount cannot exceed balance due.'));
            return;
        }

        DB::transaction(function () use ($invoice) {
            \App\Models\Payment::create([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'amount' => $this->payment_amount,
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'reference_number' => $this->payment_reference,
                'notes' => $this->payment_notes,
            ]);

            $invoice->balance_due -= $this->payment_amount;

            if ($invoice->balance_due <= 0) {
                $invoice->status = 'paid';
                $invoice->paid_at = now();
            } else {
                $invoice->status = 'partially_paid';
            }
            $invoice->save();
        });

        $this->showPaymentModal = false;
        $this->dispatch('notify', message: __('Payment recorded successfully.'), type: 'success');
        $this->resetPage();
    }

    public function cancelInvoice(): void
    {
        $invoice = Invoice::findOrFail($this->cancellingId);
        $number = $invoice->invoice_number;

        $invoice->update(['status' => 'cancelled']);

        ActivityLog::record('invoice.cancelled', "Cancelled invoice: {$number}");
        $this->dispatch('notify', message: __('Invoice cancelled successfully.'), type: 'success');

        $this->showCancelModal = false;
        $this->cancellingId = null;
        $this->resetPage();
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'date' => 'Date',
            'invoice_number' => 'Invoice #',
            'client' => 'Client',
            'subtotal' => 'Subtotal',
            'discount' => 'Discount',
            'tax' => 'Tax',
            'total' => 'Total',
            'balance' => 'Balance',
            'currency' => 'Currency',
            'status' => 'Status',
            'due_date' => 'Due Date',
        ];
    }

    public function openReportModal(): void
    {
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['date', 'invoice_number', 'client', 'total', 'balance', 'status'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        [$start, $end] = $this->getReportDateRange();

        $query = Invoice::with('client')
            ->whereBetween('created_date', [$start, $end])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));

        $headers = collect($this->selectedColumns)
            ->map(fn($col) => $this->availableColumns[$col] ?? $col)
            ->toArray();

        $rowMapper = function ($invoice) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match ($col) {
                    'date' => $invoice->created_date?->format('Y-m-d'),
                    'invoice_number' => $invoice->invoice_number,
                    'client' => $invoice->client?->name ?? 'N/A',
                    'subtotal' => number_format((float) $invoice->subtotal, 2),
                    'discount' => number_format((float) $invoice->discount, 2),
                    'tax' => number_format((float) $invoice->tax_total, 2),
                    'total' => number_format((float) $invoice->total, 2),
                    'balance' => number_format((float) $invoice->balance_due, 2),
                    'currency' => $invoice->currency_code,
                    'status' => ucfirst($invoice->status),
                    'due_date' => $invoice->due_date?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Invoices_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Invoices_Report', $headers, $query, $rowMapper);
    }

    public function sendInvoice($id)
    {
        $this->authorize('send invoice');

        $invoice = Invoice::with('client')->findOrFail($id);

        if (!$invoice->client || !$invoice->client->email) {
            $this->dispatch('notify', message: __('Client does not have an email address!'), type: 'error');
            return;
        }

        try {
            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice));

            // Update status
            $invoice->update(['status' => 'unpaid']);

            ActivityLog::record('invoice.sent', "Sent invoice #{$invoice->invoice_number} to {$invoice->client->name}");

            $this->dispatch('notify', message: __('Invoice sent successfully!'), type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __('Failed to send email:') . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $invoices = Invoice::with('client')
            ->when(
                $this->search,
                fn($q) =>
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
            )
            ->when(
                $this->statusFilter,
                fn($q) =>
                    $q->where('status', $this->statusFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $clients = Client::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $tours = \App\Models\Tour::orderBy('name')->get();

        $settings = app(GeneralSetting::class);
        $tax_rates = $settings->taxes;
        $discounts = $settings->discounts;

        return view('livewire.admin.invoices.invoices-list', compact('invoices', 'clients', 'products', 'tours', 'tax_rates', 'discounts'));
    }
}
