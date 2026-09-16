<?php
/* [MODIFY] [EstimationsList.php](file:///d:/LOCALSERVER/laragon/www/ mwangwi-safaris/app/Livewire/Estimations/EstimationsList.php) */

namespace App\Livewire\Admin\Estimations;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Estimation;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Settings\GeneralSetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstimateMail;

use App\Traits\HasReports;

#[Layout('admin::layouts.app')]
#[Title('Estimations Management')]
class EstimationsList extends Component
{
    use WithPagination, HasReports;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showViewModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?Estimation $viewingEstimation = null;

    // Form fields (from EstimationForm)
    public string $target_type = 'client';
    public $client_id;
    public $lead_id;
    public $custom_name;
    public string $estimate_type = 'product'; // product or tour
    public $estimate_number;
    public $date;
    public $expiry_date;
    public $status = 'draft';
    public $notes;
    public $terms;
    public array $items = [];
    public $subtotal = 0;
    public $discount = 0;
    public $tax = 0;
    public $total = 0;

    // Multi-Currency
    public $currency_code;
    public $currency_symbol;
    public $exchange_rate = 1.0;
    public $base_grand_total = 0;

    // Settings based selections
    public $tax_rate_index = null;
    public $discount_index = null;
    public $due_date;

    protected function rules(): array
    {
        return [
            'target_type' => 'required|in:client,lead,custom',
            'client_id' => 'required_if:target_type,client|nullable|exists:clients,id',
            'lead_id' => 'required_if:target_type,lead|nullable|exists:leads,id',
            'custom_name' => 'required_if:target_type,custom|nullable|string|max:255',
            'date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:draft,sent,accepted,rejected',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'estimate_type' => 'required|in:product,tour',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.tour_id' => $this->estimate_type === 'tour' ? 'required|exists:tours,id' : 'nullable|exists:tours,id',
            'items.*.adults' => 'nullable|numeric|min:0',
            'items.*.children' => 'nullable|numeric|min:0',
            'items.*.item_name' => $this->estimate_type === 'product' ? 'required|string' : 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_value' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'required|in:rate,value',
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
        $this->authorize('create estimate');
        $this->reset(['target_type', 'client_id', 'lead_id', 'custom_name', 'estimate_type', 'date', 'expiry_date', 'status', 'notes', 'terms', 'items', 'subtotal', 'discount', 'tax', 'total', 'editingId', 'tax_rate_index', 'discount_index', 'due_date', 'currency_code', 'currency_symbol', 'exchange_rate', 'base_grand_total']);
        $this->target_type = 'client';
        $this->estimate_type = 'product';

        $this->notes = $settings->estimate_note;
        $this->terms = $settings->estimate_terms;

        $prefix = rtrim($settings->estimate_prefix, '-');
        $datePart = date('ymd');

        // Robustly find the last number for today, including soft-deleted ones to avoid unique constraint issues
        $lastEstToday = Estimation::withTrashed()
            ->where('estimate_number', 'like', "{$prefix}-{$datePart}%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(estimate_number, "-", -1) AS UNSIGNED) DESC')
            ->first();

        if ($lastEstToday) {
            $parts = explode('-', $lastEstToday->estimate_number);
            $lastSequence = end($parts);
            // Ensure we only increment if the last part is actually numeric
            $lastNum = is_numeric($lastSequence) ? (int) $lastSequence : 0;
            $nextNumber = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        $this->estimate_number = "{$prefix}-{$datePart}-{$nextNumber}";
        $this->date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime("+{$settings->estimate_due_days} days"));
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
        $this->authorize('edit estimate');
        $estimation = Estimation::with(['items', 'invoice'])->findOrFail($id);

        if ($estimation->invoice) {
            $this->dispatch('notify', message: __('This estimate has already been converted to an invoice and cannot be edited.'), type: 'warning');
            return;
        }

        $this->editingId = $estimation->id;
        $this->target_type = $estimation->lead_id ? 'lead' : 'client';
        $this->client_id = $estimation->client_id;
        $this->lead_id = $estimation->lead_id;
        $this->estimate_type = $estimation->items->whereNotNull('tour_id')->count() > 0 ? 'tour' : 'product';
        $this->estimate_number = $estimation->estimate_number;
        $this->date = $estimation->date ? \Illuminate\Support\Carbon::parse($estimation->date)->format('Y-m-d') : null;
        $this->expiry_date = $estimation->expiry_date ? \Illuminate\Support\Carbon::parse($estimation->expiry_date)->format('Y-m-d') : null;
        $this->status = $estimation->status;
        $this->notes = $estimation->notes;
        $this->terms = $estimation->terms;
        $this->discount = $estimation->discount;
        $this->tax = $estimation->tax;
        $this->tax_rate_index = $estimation->tax_rate_index;
        $this->discount_index = $estimation->discount_index;
        $this->due_date = $estimation->due_date ? \Illuminate\Support\Carbon::parse($estimation->due_date)->format('Y-m-d') : null;

        // Currency
        $this->currency_code = $estimation->currency_code;
        $this->currency_symbol = $estimation->currency_symbol;
        $this->exchange_rate = (float) ($estimation->exchange_rate ?? 1.0);
        $this->base_grand_total = (float) ($estimation->base_grand_total ?? 0.0);

        $this->items = [];
        foreach ($estimation->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'tour_id' => $item->tour_id,
                'adults' => $item->adults ?? 1,
                'children' => $item->children ?? 0,
                'item_name' => $item->item_name,
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
        $this->viewingEstimation = Estimation::with(['client', 'items.product'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'tour_id' => '',
            'adults' => 1,
            'children' => 0,
            'item_name' => '',
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

    public function updatedEstimateType()
    {
        $this->items = [];
        $this->addItem();
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

    public function updatedDate($value)
    {
        if ($value) {
            $settings = app(GeneralSetting::class);
            $this->due_date = \Carbon\Carbon::parse($value)->addDays($settings->estimate_due_days)->format('Y-m-d');
        }
    }

    public function updatedCurrencyCode($value)
    {
        $currency = Currency::where('code', $value)->first();
        if ($currency) {
            $this->currency_symbol = $currency->symbol;

            $oldRate = (float) ($this->exchange_rate ?: 1.0);
            $newRate = (float) ($currency->exchange_rate ?: 1.0);

            \Log::info('Currency changed to ' . $value, [
                'oldRate' => $oldRate,
                'newRate' => $newRate,
                'items_before' => $this->items,
            ]);

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

            \Log::info('Items after conversion:', ['items_after' => $this->items]);
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
        $this->tax = 0;
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
            $this->tax += $lineTax;
            $this->discount += $itemDiscount;
        }

        $this->total = $this->subtotal - $this->discount + $this->tax;
        if ($this->total < 0)
            $this->total = 0;

        // Calculate base total (Total / Exchange Rate)
        $rate = (float) ($this->exchange_rate ?: 1.0);
        $this->base_grand_total = $this->total / $rate;
    }

    public function save(): void
    {
        $this->authorize($this->editingId ? 'edit estimate' : 'create estimate');

        foreach ($this->items as $index => $item) {
            if (trim($this->items[$index]['product_id'] ?? '') === '') {
                $this->items[$index]['product_id'] = null;
            }
            if (trim($this->items[$index]['tour_id'] ?? '') === '') {
                $this->items[$index]['tour_id'] = null;
            }
            if ($this->estimate_type === 'tour') {
                if (empty($this->items[$index]['item_name']) && !empty($this->items[$index]['tour_id'])) {
                    $tour = \App\Models\Tour::find($this->items[$index]['tour_id']);
                    $this->items[$index]['item_name'] = 'Tour Package: ' . ($tour ? $tour->name : '');
                }
            }
        }

        $this->validate();
        $this->calculateTotals();

        DB::transaction(function () {
            if ($this->target_type === 'custom' && $this->custom_name) {
                $lead = \App\Models\Lead::create([
                    'name' => $this->custom_name,
                    'status' => \App\Enums\LeadStatus::NEW ,
                ]);
                $this->lead_id = $lead->id;
                $this->target_type = 'lead';
            }

            $data = [
                'client_id' => $this->target_type === 'client' ? $this->client_id : null,
                'lead_id' => $this->target_type === 'lead' ? $this->lead_id : null,
                'estimate_number' => $this->estimate_number,
                'date' => $this->date,
                'expiry_date' => $this->expiry_date,
                'status' => $this->status,
                'notes' => $this->notes,
                'terms' => $this->terms,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount ?: 0,
                'tax' => $this->tax ?: 0,
                'total' => $this->total,
                'tax_rate_index' => $this->tax_rate_index,
                'discount_index' => $this->discount_index,
                'due_date' => $this->due_date,
                'currency_code' => $this->currency_code,
                'currency_symbol' => $this->currency_symbol,
                'exchange_rate' => $this->exchange_rate,
                'base_grand_total' => $this->base_grand_total,
            ];

            if ($this->editingId) {
                $estimation = Estimation::findOrFail($this->editingId);
                $estimation->update($data);
                $estimation->items()->delete();
            } else {
                $data['created_by'] = auth()->id();
                $estimation = Estimation::create($data);
            }

            foreach ($this->items as $item) {
                $estimation->items()->create([
                    'product_id' => !empty($item['product_id']) ? $item['product_id'] : null,
                    'tour_id' => !empty($item['tour_id']) ? $item['tour_id'] : null,
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

            ActivityLog::record($this->editingId ? 'estimation.updated' : 'estimation.created', "Estimation {$this->estimate_number} saved.");
        });

        $this->dispatch('notify', message: __('Estimation saved successfully!'), type: 'success');
        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEstimation(): void
    {
        $this->authorize('delete estimate');
        $estimation = Estimation::findOrFail($this->deletingId);
        $number = $estimation->estimate_number;
        $estimation->delete();

        ActivityLog::record('estimation.deleted', "Deleted estimation: {$number}");
        $this->dispatch('notify', message: __('Estimation deleted successfully.'), type: 'success');

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function convertToInvoice(int $id): void
    {
        $this->authorize('convert to invoice');

        $estimation = Estimation::with(['items', 'invoice'])->findOrFail($id);

        if ($estimation->invoice) {
            $this->dispatch('notify', message: __('This estimate has already been converted to an invoice!'), type: 'warning');
            return;
        }

        $settings = app(GeneralSetting::class);

        DB::transaction(function () use ($estimation, $settings) {
            // Generate Invoice Number
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
            $invoiceNumber = "{$prefix}-{$datePart}-{$nextNumber}";

            $clientId = $estimation->client_id;

            if (!$clientId && $estimation->lead_id) {
                $lead = \App\Models\Lead::find($estimation->lead_id);
                if ($lead) {
                    if ($lead->client_id) {
                        $clientId = $lead->client_id;
                    } else {
                        $fakeEmail = 'client_' . uniqid() . '@placeholder.com';
                        $client = \App\Models\Client::create([
                            'name' => $lead->name,
                            'email' => $lead->email ?: $fakeEmail,
                            'phone' => $lead->phone,
                            'status' => \App\Enums\ClientStatus::ACTIVE,
                            'notes' => "Converted from Lead #" . $lead->id . "\nCompany: " . $lead->company,
                        ]);

                        $lead->update([
                            'status' => \App\Enums\LeadStatus::WON,
                            'client_id' => $client->id,
                        ]);

                        $clientId = $client->id;
                    }

                    // Link estimation to the newly created/found client
                    $estimation->update([
                        'client_id' => $clientId,
                        'lead_id' => null
                    ]);
                }
            }

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_id' => $clientId,
                'user_id' => auth()->id(),
                'estimate_id' => $estimation->id,
                'created_date' => now(),
                'due_date' => now()->addDays($settings->invoice_due_days),
                'subtotal' => $estimation->subtotal,
                'tax_total' => $estimation->tax,
                'discount' => $estimation->discount,
                'total' => $estimation->total,
                'balance_due' => $estimation->total,
                'notes' => $estimation->notes,
                'status' => 'draft',
                'tax_rate_index' => $estimation->tax_rate_index,
                'discount_index' => $estimation->discount_index,
                'currency_code' => $estimation->currency_code,
                'currency_symbol' => $estimation->currency_symbol,
                'exchange_rate' => $estimation->exchange_rate,
                'base_grand_total' => $estimation->base_grand_total,
            ]);

            // Create Invoice Items
            foreach ($estimation->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'tour_id' => $item->tour_id,
                    'adults' => $item->adults,
                    'children' => $item->children,
                    'item_name' => $item->item_name ?? 'Service/Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax' => 0, // Calculated in total
                    'discount_value' => $item->discount_value,
                    'discount_type' => $item->discount_type ?? 'value',
                    'tax_applied' => $item->tax_applied,
                    'total' => $item->total,
                ]);
            }

            // Update Estimation Status
            $estimation->update(['status' => 'accepted']);

            ActivityLog::record('invoice.created', "Invoice {$invoiceNumber} created from Estimation {$estimation->estimate_number}");
        });

        $this->dispatch('notify', message: __('Estimation converted to Invoice successfully!'), type: 'success');
        $this->resetPage();
    }

    public function convertToItinerary($id)
    {
        $this->authorize('create itineraries');
        $estimation = Estimation::with('items')->findOrFail($id);

        $tourItems = $estimation->items->whereNotNull('tour_id');

        if ($estimation->status !== 'accepted' || $tourItems->isEmpty() || $estimation->itinerary_id) {
            $this->dispatch('notify', message: __('Cannot convert this estimate.'), type: 'error');
            return;
        }

        // Just take the first tour for the main itinerary attributes
        $firstTourItem = $tourItems->first();
        $tour = \App\Models\Tour::with('days.activities', 'days.transports')->findOrFail($firstTourItem->tour_id);

        // Sum up total duration days from all tours
        $totalDurationDays = 0;
        $totalDurationNights = 0;
        $adults = 0;
        $children = 0;
        $price = 0;

        foreach ($tourItems as $tourItem) {
            $t = \App\Models\Tour::find($tourItem->tour_id);
            if ($t) {
                $totalDurationDays += $t->duration_days;
                $totalDurationNights += $t->duration_nights;
                if ($tourItem->adults > $adults)
                    $adults = $tourItem->adults;
                if ($tourItem->children > $children)
                    $children = $tourItem->children;
                $price += $t->price;
            }
        }

        $itinerary = \App\Models\Itinerary::create([
            'client_id' => $estimation->client_id,
            'lead_id' => $estimation->lead_id,
            'tour_id' => $tour->id,
            'reference_number' => 'ITN-' . date('Ymd') . '-' . rand(1000, 9999),
            'title' => 'Multi-Tour Proposal',
            'duration_days' => $totalDurationDays,
            'duration_nights' => $totalDurationNights,
            'start_date' => $estimation->date,
            'end_date' => $estimation->date ? \Illuminate\Support\Carbon::parse($estimation->date)->addDays($totalDurationDays) : null,
            'adults' => $adults > 0 ? $adults : 1,
            'children' => $children,
            'status' => 'draft',
            'currency' => $estimation->currency_code,
            'price_per_adult' => $price,
        ]);

        $dayOffset = 0;
        foreach ($tourItems as $tourItem) {
            $t = \App\Models\Tour::with('itineraries.pivotActivities', 'itineraries.transports')->find($tourItem->tour_id);
            if (!$t)
                continue;

            foreach ($t->itineraries as $tourDay) {
                $dayNum = $tourDay->day_number + $dayOffset;
                $cleanTitle = preg_replace('/^Day\s+\d+\s*[-–:]\s*/iu', '', $tourDay->title ?? '');

                $itineraryDay = $itinerary->days()->create([
                    'day' => "Day {$dayNum}",
                    'description' => $tourDay->description,
                    'location' => $cleanTitle,
                    'destination_id' => $tourDay->destination_id ?? null,
                    'accommodation_id' => $tourDay->accommodation_id ?? null,
                    'accommodation' => $tourDay->accommodation ?? null,
                    'meal_plan_id' => $tourDay->meal_plan_id ?? null,
                ]);

                // Sync activities
                if ($tourDay->activities->count() > 0) {
                    $itineraryDay->activities()->sync($tourDay->activities->pluck('id')->toArray());
                }

                // Sync transports
                if ($tourDay->transports->count() > 0) {
                    $itineraryDay->transports()->sync($tourDay->transports->pluck('id')->toArray());
                }
            }
            $dayOffset += $t->duration_days;
        }

        $estimation->update(['itinerary_id' => $itinerary->id]);

        $this->dispatch('notify', message: __('Tour estimates converted to Itinerary!'), type: 'success');
        return redirect()->route('itineraries.edit', $itinerary->id);
    }

    public function sendEstimate($id)
    {
        $this->authorize('send estimate');

        $estimate = Estimation::with(['client', 'lead', 'invoice'])->findOrFail($id);

        if ($estimate->invoice) {
            $this->dispatch('notify', message: __('This estimate has been converted to an invoice. Please send the invoice instead.'), type: 'error');
            return;
        }

        $targetEmail = null;
        $targetName = null;

        if ($estimate->client && $estimate->client->email) {
            $targetEmail = $estimate->client->email;
            $targetName = $estimate->client->name;
        } elseif ($estimate->lead && $estimate->lead->email) {
            $targetEmail = $estimate->lead->email;
            $targetName = $estimate->lead->name;
        }

        if (!$targetEmail) {
            $this->dispatch('notify', message: __('Client or Lead does not have an email address!'), type: 'error');
            return;
        }

        try {
            Mail::to($targetEmail)->send(new EstimateMail($estimate));

            // Update status
            $estimate->update(['status' => 'sent']);

            ActivityLog::record('estimate.sent', "Sent estimate #{$estimate->estimate_number} to {$targetName}");

            $this->dispatch('notify', message: __('Estimate sent successfully!'), type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __('Failed to send email:') . $e->getMessage(), type: 'error');
        }
    }

    public function getAvailableColumnsProperty()
    {
        return [
            'date' => 'Date',
            'estimate_number' => 'Estimate #',
            'client' => 'Client',
            'subtotal' => 'Subtotal',
            'discount' => 'Discount',
            'tax' => 'Tax',
            'total' => 'Total',
            'currency' => 'Currency',
            'status' => 'Status',
            'due_date' => 'Due Date',
            'expiry_date' => 'Expiry Date',
        ];
    }

    public function openReportModal(): void
    {
        $this->authorize('view estimate reports');
        $this->showReportModal = true;
        $this->reportStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->reportEndDate = now()->format('Y-m-d');
        $this->selectedColumns = ['date', 'estimate_number', 'client', 'total', 'status'];
        $this->resetErrorBag();
    }

    public function generateReport()
    {
        $this->authorize('view estimate reports');
        [$start, $end] = $this->getReportDateRange();

        $query = Estimation::with('client')
            ->whereBetween('date', [$start, $end])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter));

        $headers = collect($this->selectedColumns)
            ->map(fn($col) => $this->availableColumns[$col] ?? $col)
            ->toArray();

        $rowMapper = function ($est) {
            $row = [];
            foreach ($this->selectedColumns as $col) {
                $row[] = match ($col) {
                    'date' => $est->date?->format('Y-m-d'),
                    'estimate_number' => $est->estimate_number,
                    'client' => $est->client ? $est->client->name : ($est->lead ? $est->lead->name : 'N/A'),
                    'subtotal' => number_format((float) $est->subtotal, 2),
                    'discount' => number_format((float) $est->discount, 2),
                    'tax' => number_format((float) $est->tax, 2),
                    'total' => number_format((float) $est->total, 2),
                    'currency' => $est->currency_code,
                    'status' => ucfirst($est->status),
                    'due_date' => $est->due_date?->format('Y-m-d'),
                    'expiry_date' => $est->expiry_date?->format('Y-m-d'),
                    default => ''
                };
            }
            return $row;
        };

        if ($this->reportFormat === 'pdf') {
            return $this->exportToPdf('Estimations_Report', $headers, $query, $rowMapper);
        }

        return $this->exportToCsv('Estimations_Report', $headers, $query, $rowMapper);
    }



    public function render()
    {
        $estimations = Estimation::with(['client', 'lead', 'invoice'])
            ->when(
                $this->search,
                fn($q) =>
                    $q->where('estimate_number', 'like', "%{$this->search}%")
                        ->orWhereHas('client', fn($cq) => $cq->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('lead', fn($lq) => $lq->where('name', 'like', "%{$this->search}%"))
            )
            ->when(
                $this->statusFilter,
                fn($q) =>
                    $q->where('status', $this->statusFilter)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        $clients = Client::orderBy('name')->get();
        $leads = \App\Models\Lead::whereNotIn('status', ['converted', 'lost'])->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $tours = \App\Models\Tour::orderBy('name')->get();

        $settings = app(GeneralSetting::class);
        $tax_rates = $settings->taxes;
        $discounts = $settings->discounts;

        return view('livewire.admin.estimations.estimations-list', compact('estimations', 'clients', 'leads', 'products', 'tours', 'tax_rates', 'discounts'));
    }
}
