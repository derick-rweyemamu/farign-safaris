<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Invoices') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $invoices->total() }} total invoices</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view invoices')
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create invoice')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('New Invoice') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Invoices Report') }}" :show="$showReportModal">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
            <select wire:model="statusFilter" class="form-input text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="unpaid">{{ __('Unpaid') }}</option>
                <option value="partially_paid">{{ __('Partially Paid') }}</option>
                <option value="paid">{{ __('Paid') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>
    </x-admin.report-modal>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">

        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <!-- Left side - Per page and Status (small widths) -->
            <div class="flex items-center gap-3">
                <!-- Per page -->
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <!-- Status filter -->
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-full sm:w-40">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="sent">{{ __('Sent') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                    <option value="unpaid">{{ __('Unpaid') }}</option>
                    <option value="overdue">{{ __('Overdue') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                    <option value="refunded">{{ __('Refunded') }}</option>
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
                    class="form-input text-xs py-1.5 !pl-8">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />

            </div>
        </div>

    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <button wire:click="sort('invoice_number')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Invoice #
                                @if($sortBy === 'invoice_number') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Client') }}</th>
                        <th>
                            <button wire:click="sort('created_date')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Date
                                @if($sortBy === 'created_date') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Status') }}</th>
                        <th>
                            <button wire:click="sort('total')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Amount
                                @if($sortBy === 'total') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr wire:key="inv-{{ $inv->id }}" class="animate-fade-in">
                            <td class="font-semibold text-indigo-600 dark:text-indigo-400">
                                <div class="flex items-center gap-1.5">
                                    {{ $inv->invoice_number }}
                                    @if($inv->is_recurring)
                                        <x-icon name="arrow-path" class="w-3.5 h-3.5 text-indigo-400" title="Recurring {{ ucfirst($inv->recurring_frequency) }} (Next: {{ $inv->recurring_next_date?->format('M d, Y') }})" />
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $inv->client?->name ?? 'Unknown Client' }}</div>
                                <div class="text-xs text-gray-500">{{ $inv->client?->email ?? '—' }}</div>
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $inv->created_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td>
                                @php $status = $inv->status; @endphp
                                <span
                                    class="badge flex items-center gap-1.5 w-fit
                                                                                                                                                        {{ $status === 'draft' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                                                                                                                                        {{ $status === 'sent' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }}
                                                                                                                                                        {{ $status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : '' }}
                                                                                                                                                        {{ $status === 'partially_paid' ? 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300' : '' }}
                                                                                                                                                        {{ $status === 'unpaid' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : '' }}
                                                                                                                                                        {{ $status === 'overdue' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : '' }}
                                                                                                                                                        {{ $status === 'cancelled' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }}
                                                                                                                                                        {{ $status === 'refunded' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : '' }}">
                                    <span
                                        class="status-dot
                                                                                                                                                            {{ in_array($status, ['draft', 'cancelled', 'overdue']) ? 'offline' : 'online' }}">
                                    </span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="font-medium text-gray-900 dark:text-white">
                                {{ $inv->currency_symbol ?? '$' }}{{ number_format($inv->total, 2) }}
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    <button wire:click="openView({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                        title="{{ __('View invoice') }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    @can('edit invoice')
                                    <button wire:click="openEdit({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit invoice') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @if(in_array($inv->status, ['sent', 'unpaid', 'partially_paid', 'overdue']))
                                        <button wire:click="openPaymentModal({{ $inv->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-900/30 transition-colors"
                                            title="{{ __('Record Payment') }}">
                                            <x-icon name="currency-dollar" class="w-4 h-4" />
                                        </button>
                                    @endif
                                    @if($inv->status === 'paid')
                                        <a href="{{ route('cms.pdf.receipt', $inv->id) }}" target="_blank"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                            title="{{ __('Print Receipt') }}">
                                            <x-icon name="printer" class="w-4 h-4" />
                                        </a>
                                    @endif
                                    @can('edit invoice')
                                    @if($inv->status !== 'cancelled')
                                    <button wire:click="confirmCancel({{ $inv->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Cancel invoice') }}">
                                        <x-icon name="x-circle" class="w-4 h-4" />
                                    </button>
                                    @endif
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <x-icon name="document-text"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No invoices found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Invoice: ' . $invoice_number : 'New Invoice' }}
                    </h3>
                    <div class="flex items-center gap-2">
                        @if($editingId)
                            <a href="{{ route('cms.pdf.invoice', $editingId) }}" target="_blank"
                                class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                title="{{ __('Download PDF') }}">
                                <x-icon name="document-text" class="w-5 h-5" />
                            </a>
                        @endif
                        <button @click="$wire.set('showModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <x-icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <!-- Target Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target *</label>
                                <div class="flex gap-2">
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <input type="radio" wire:model.live="target_type" value="client" class="text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                        <span class="text-[10px] text-gray-500 uppercase font-medium">{{ __('Client') }}</span>
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <input type="radio" wire:model.live="target_type" value="custom" class="text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                        <span class="text-[10px] text-gray-500 uppercase font-medium">{{ __('Custom') }}</span>
                                    </label>
                                </div>
                            </div>
                            
                            @if($target_type === 'client')
                                <select wire:model.live="client_id" class="form-input w-full text-xs">
                                    <option value="">{{ __('Select client...') }}</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            @elseif($target_type === 'custom')
                                <input type="text" wire:model="custom_name" placeholder="{{ __('Enter custom name...') }}" class="form-input w-full text-xs">
                                @error('custom_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <!-- tax rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Tax
                                Rate') }}</label>
                            <select wire:model.live="tax_rate_index" class="form-input w-full text-xs">
                                <option value="">{{ __('No Tax') }}</option>
                                @foreach($tax_rates as $index => $rate)
                                    <option value="{{ $index }}">{{ $rate['name'] }} ({{ $rate['rate'] }}%)</option>
                                @endforeach
                            </select>
                            @error('tax_rate_index') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Created Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date
                                *</label>
                            <input type="date" wire:model.live="created_date" class="form-input w-full text-xs">
                            @error('created_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Due
                                Date') }}</label>
                            <input type="date" wire:model="due_date" class="form-input w-full text-xs">
                        </div>

                        <!-- Currency Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Currency
                                *</label>
                            <select wire:model.live="currency_code" class="form-input w-full text-xs">
                                @foreach(\App\Models\Currency::active()->get() as $curr)
                                    <option value="{{ $curr->code }}">{{ $curr->code }} ({{ $curr->symbol }})</option>
                                @endforeach
                            </select>
                            @error('currency_code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>@enderror
                        </div>

                        <!-- Exchange Rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Exchange
                                Rate') }}</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-bold">×</span>
                                <input type="number" step="0.000001" wire:model.live.debounce.500ms="exchange_rate"
                                    class="form-input w-full text-xs !pl-8">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 italic">Exchange Rate: 1
                                {{ app(\App\Settings\GeneralSetting::class)->currency_base }} = X {{ $currency_code }}
                            </p>
                            @error('exchange_rate') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>@enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status
                                *</label>
                            <select wire:model="status" class="form-input w-full text-xs">
                                <option value="draft">{{ __('Draft') }}</option>
                                <option value="sent">{{ __('Sent') }}</option>
                                <option value="paid">{{ __('Paid') }}</option>
                                <option value="unpaid">{{ __('Unpaid') }}</option>
                                <option value="overdue">{{ __('Overdue') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                                <option value="refunded">{{ __('Refunded') }}</option>
                            </select>
                        </div>

                    </div>

                    <!-- Recurring Toggle Section -->
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-[3px] border border-indigo-100 dark:border-indigo-800/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-100">{{ __('Enable Recurring Invoice') }}</h4>
                                <p class="text-xs text-indigo-600 dark:text-indigo-300 mt-0.5">{{ __('Automatically generate this invoice on a schedule.') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="is_recurring" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        
                        @if($is_recurring)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-indigo-200 dark:border-indigo-800/50">
                                <div>
                                    <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-200 mb-1.5">Frequency *</label>
                                    <select wire:model="recurring_frequency" class="form-input w-full text-xs">
                                        <option value="weekly">{{ __('Weekly') }}</option>
                                        <option value="monthly">{{ __('Monthly') }}</option>
                                        <option value="quarterly">{{ __('Quarterly') }}</option>
                                        <option value="yearly">{{ __('Yearly') }}</option>
                                    </select>
                                    @error('recurring_frequency') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-200 mb-1.5">Next Issue Date *</label>
                                    <input type="date" wire:model="recurring_next_date" class="form-input w-full text-xs">
                                    @error('recurring_next_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-indigo-800 dark:text-indigo-200 mb-1.5">{{ __('End Date (Optional)') }}</label>
                                    <input type="date" wire:model="recurring_end_date" class="form-input w-full text-xs">
                                    @error('recurring_end_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Invoice Type Toggle (Better UI) -->
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 mb-6 mt-4">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">{{ __('What are you invoicing?') }}</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex cursor-pointer rounded-[3px] border bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none {{ $invoice_type === 'product' ? 'border-indigo-600 ring-1 ring-indigo-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="invoice_type" value="product" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ __('Custom Products & Services') }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">{{ __('Build a custom line-item invoice.') }}</span>
                                    </span>
                                </span>
                                <x-icon name="check-circle" class="h-5 w-5 text-indigo-600 {{ $invoice_type === 'product' ? 'block' : 'invisible' }}" />
                            </label>
                            
                            <label class="relative flex cursor-pointer rounded-[3px] border bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none {{ $invoice_type === 'tour' ? 'border-indigo-600 ring-1 ring-indigo-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="invoice_type" value="tour" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ __('Tour Package') }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">{{ __('Invoice based on a standard tour.') }}</span>
                                    </span>
                                </span>
                                <x-icon name="check-circle" class="h-5 w-5 text-indigo-600 {{ $invoice_type === 'tour' ? 'block' : 'invisible' }}" />
                            </label>
                        </div>
                    </div>

                    @if($invoice_type === 'tour')
                        <!-- Tour Items Table -->
                        <div class="border rounded-[3px] overflow-hidden mb-6">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left w-64">{{ __('Tour Package') }}</th>
                                        <th class="px-4 py-2 text-center w-24">{{ __('Adults') }}</th>
                                        <th class="px-4 py-2 text-center w-24">{{ __('Children') }}</th>
                                        <th class="px-4 py-2 text-center w-32">{{ __('Price') }}</th>
                                        <th class="px-4 py-2 text-center w-32">Tax %</th>
                                        <th class="px-4 py-2 text-center w-48">{{ __('Disc') }}</th>
                                        <th class="px-4 py-2 text-right w-32">{{ __('Total') }}</th>
                                        <th class="px-4 py-2 text-right w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($items as $index => $item)
                                        <tr wire:key="modal-tour-item-{{ $index }}">
                                            <td class="px-4 py-2 space-y-1">
                                                <select wire:model.live="items.{{ $index }}.tour_id"
                                                    class="form-input w-full text-xs @error('items.'.$index.'.tour_id') border-red-500 @enderror">
                                                    <option value="">{{ __('Choose a tour...') }}</option>
                                                    @foreach($tours as $tour)
                                                        <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('items.'.$index.'.tour_id')
                                                    <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                                @enderror
                                                <input type="text" wire:model="items.{{ $index }}.item_name"
                                                    placeholder="{{ __('Description') }}" class="form-input w-full text-xs" style="{{ $invoice_type === 'tour' ? 'display: none;' : '' }}">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" min="0" wire:model.live.debounce.500ms="items.{{ $index }}.adults"
                                                    class="form-input text-center w-full text-xs">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" min="0" wire:model.live.debounce.500ms="items.{{ $index }}.children"
                                                    class="form-input text-center w-full text-xs">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" step="0.01"
                                                    wire:model.live.debounce.500ms="items.{{ $index }}.unit_price"
                                                    class="form-input text-right !pr-0.5 w-full text-xs bg-gray-50 dark:bg-gray-800" readonly>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" wire:model.live="items.{{ $index }}.tax_applied"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-4 py-2">
                                                <div class="grid grid-cols-4 gap-1">
                                                    <input type="number" step="0.01"
                                                        wire:model.live.debounce.500ms="items.{{ $index }}.discount_value"
                                                        class="form-input text-right !pr-0.5 col-span-3 text-xs"
                                                        @if(($items[$index]['discount_type'] ?? 'value') === 'rate') max="100" @endif>

                                                    <select wire:model.live="items.{{ $index }}.discount_type"
                                                        class="form-input text-xs !py-1 !pl-2 !pr-4 text-start  col-span-1">
                                                        <option value="value">V</option>
                                                        <option value="rate">%</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                {{ $currency_symbol }}{{ number_format($item['total'] ?? 0, 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="text-red-500 hover:text-red-700" title="{{ __('Delete Item') }}">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50">
                                <button type="button" wire:click="addItem"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <x-icon name="plus" class="w-3 h-3" />{{ __('Add Tour Item') }}</button>
                            </div>
                        </div>
                    @else
                        <!-- Items Table -->
                        <div class="border rounded-[3px] overflow-hidden mb-6">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left w-64">{{ __('Product') }}</th>
                                        <th class="px-4 py-2 text-center w-24">{{ __('Qty') }}</th>
                                        <th class="px-4 py-2 text-right w-32">{{ __('Price') }}</th>
                                        <th class="px-4 py-2 text-center w-24">Tax %</th>
                                        <th class="px-4 py-2 text-right w-48">{{ __('Disc') }}</th>
                                        <th class="px-4 py-2 text-right w-32">{{ __('Total') }}</th>
                                        <th class="px-4 py-2 text-right w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($items as $index => $item)
                                        <tr wire:key="modal-inv-item-{{ $index }}">
                                            <td class="px-4 py-2 space-y-1">
                                                <select wire:model.live="items.{{ $index }}.product_id"
                                                    class="form-input w-full text-xs">
                                                    <option value="">{{ __('Custom Item...') }}</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" wire:model="items.{{ $index }}.item_name"
                                                    placeholder="{{ __('Description') }}" class="form-input w-full text-xs @error('items.'.$index.'.item_name') border-red-500 @enderror">
                                                @error('items.'.$index.'.item_name')
                                                    <span class="text-red-500 text-[10px]">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                                    class="form-input text-center w-full text-xs @error('items.'.$index.'.quantity') border-red-500 @enderror">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" step="0.01"
                                                    wire:model.live.debounce.500ms="items.{{ $index }}.unit_price"
                                                    class="form-input text-right !pr-0.5 w-full text-xs @error('items.'.$index.'.unit_price') border-red-500 @enderror">
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <input type="checkbox" wire:model.live="items.{{ $index }}.tax_applied"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-4 py-2">
                                                <div class="grid grid-cols-4 gap-1">
                                                    <input type="number" step="0.01"
                                                        wire:model.live.debounce.500ms="items.{{ $index }}.discount_value"
                                                        class="form-input text-right !pr-0.5 col-span-3 text-xs"
                                                        @if(($items[$index]['discount_type'] ?? 'value') === 'rate') max="100" @endif>
                                                    <select wire:model.live="items.{{ $index }}.discount_type"
                                                        class="form-input text-xs !py-1 !pl-2 !pr-4 text-start col-span-1">
                                                        <option value="value">V</option>
                                                        <option value="rate">%</option>
                                                    </select>
                                                </div>
                                                @error("items.{$index}.discount_value")
                                                    <span class="text-red-500 text-[10px] block mt-0.5">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                {{ $currency_symbol }}{{ number_format($item['total'], 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="text-red-500 hover:text-red-700" title="{{ __('Delete Invoice') }}">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50">
                                <button type="button" wire:click="addItem"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <x-icon name="plus" class="w-3 h-3" />{{ __('Add Item') }}</button>
                            </div>
                        </div>
                    @endif

                    <!-- Footer Details -->
                    <div class="space-y-6">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-2 text-sm">
                                <div class="flex justify-between text-gray-500">
                                    <span>{{ __('Subtotal') }}</span>
                                    <span>{{ $currency_symbol }}{{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-3">
                                    <span class="text-gray-500">{{ __('Discount') }}</span>
                                    <span
                                        class="font-medium">-{{ $currency_symbol }}{{ number_format($discount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-3">
                                    <span class="text-gray-500">{{ __('Tax') }}</span>
                                    <span
                                        class="font-medium">{{ $currency_symbol }}{{ number_format($tax_total, 2) }}</span>
                                </div>
                                <div class="pt-2 border-t flex justify-between font-bold text-lg text-indigo-600">
                                    <span>{{ __('Grand Total') }}</span>
                                    <span>{{ $currency_symbol }}{{ number_format($total, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-semibold text-red-500">
                                    <span>{{ __('Balance Due') }}</span>
                                    <span>{{ $currency_symbol }}{{ number_format($balance_due, 2) }}</span>
                                </div>
                                <div
                                    class="pt-1 flex justify-between text-[10px] text-gray-400 font-mono uppercase tracking-tight border-t border-gray-100 dark:border-gray-800">
                                    <span>{{ __('Equivalent in Base') }}</span>
                                    <span>{{ app(\App\Settings\GeneralSetting::class)->currency_base }}
                                        {{ number_format($base_grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div>
                                <x-admin.rich-text wire:model="notes" label="{{ __('Notes') }}" height="180" />
                            </div>
                            <div>
                                <x-admin.rich-text wire:model="terms" label="{{ __('Terms') }}" height="180" />
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="save" class="btn-primary" wire:loading.attr="disabled">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ __('Save Invoice') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $viewingInvoice)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showViewModal', false)">
            <div class="modal-box max-w-4xl flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Invoice Details: {{ $viewingInvoice->invoice_number }}
                    </h3>
                    <div class="flex items-center gap-2">
                        @can('print invoice')
                        <a href="{{ route('cms.pdf.invoice', $viewingInvoice->id) }}" target="_blank"
                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                            title="{{ __('Download PDF') }}">
                            <x-icon name="document-text" class="w-5 h-5" />
                        </a>
                        @endcan
                        @can('send invoice')
                            <button wire:click="sendInvoice({{ $viewingInvoice->id }})"
                                class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                title="{{ __('Send to Client') }}" wire:loading.attr="disabled" wire:target="sendInvoice">
                                <x-icon name="paper-airplane" class="w-5 h-5" wire:loading.remove wire:target="sendInvoice" />
                            </button>
                        @endcan
                        <button @click="$wire.set('showViewModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <x-icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="p-8 space-y-8 overflow-y-auto flex-1 bg-white dark:bg-gray-900">
                    <!-- Invoice Header Details -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Bill To') }}</h4>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $viewingInvoice->client?->name ?? 'Unknown Client' }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $viewingInvoice->client?->email ?? '—' }}</p>
                            <p class="text-sm text-gray-500">{{ $viewingInvoice->client?->phone ?? '—' }}</p>
                        </div>
                        <div class="text-right">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Invoice Status') }}</h4>
                            <span
                                class="badge {{ $viewingInvoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ ucfirst($viewingInvoice->status) }}
                            </span>
                            <div class="mt-4 space-y-1">
                                <p class="text-sm text-gray-500">{{ __('Date:') }}<span
                                        class="font-medium text-gray-900 dark:text-white">{{ $viewingInvoice->created_date?->format('M d, Y') }}</span>
                                </p>
                                <p class="text-sm text-gray-500">{{ __('Due:') }}<span
                                        class="font-medium text-gray-900 dark:text-white">{{ $viewingInvoice->due_date?->format('M d, Y') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border rounded-[3px] overflow-hidden border-gray-100 dark:border-gray-800">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left font-bold text-gray-600 dark:text-gray-400">{{ __('Item
                                        Description') }}</th>
                                    <th class="px-6 py-4 text-center w-24 font-bold text-gray-600 dark:text-gray-400">{{ __('Qty') }}</th>
                                    <th class="px-6 py-4 text-right w-32 font-bold text-gray-600 dark:text-gray-400">{{ __('Price') }}</th>
                                    <th class="px-6 py-4 text-right w-32 font-bold text-gray-600 dark:text-gray-400">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($viewingInvoice->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $item->item_name }}</div>
                                            @if($item->product)<div class="text-xs text-gray-400">{{ $item->product->sku }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                            {{ $viewingInvoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                            {{ $viewingInvoice->currency_symbol }}{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="flex justify-end">
                        <div class="w-72 space-y-3">
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>{{ __('Subtotal') }}</span>
                                <span
                                    class="text-gray-900 dark:text-white font-medium">{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->subtotal, 2) }}</span>
                            </div>
                            @if($viewingInvoice->tax_total > 0)
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>{{ __('Tax') }}</span>
                                    <span
                                        class="text-gray-900 dark:text-white font-medium">{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->tax_total, 2) }}</span>
                                </div>
                            @endif
                            @if($viewingInvoice->discount > 0)
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>{{ __('Discount') }}</span>
                                    <span
                                        class="text-red-500 font-medium">-{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Grand Total') }}</span>
                                <span
                                    class="text-lg font-bold text-indigo-600">{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->total, 2) }}</span>
                            </div>
                            @php $amountPaid = $viewingInvoice->total - $viewingInvoice->balance_due; @endphp
                            @if($amountPaid > 0)
                                <div class="flex justify-between text-sm text-emerald-600 font-bold">
                                    <span>{{ __('Amount Paid') }}</span>
                                    <span>-{{ $viewingInvoice->currency_symbol }}{{ number_format($amountPaid, 2) }}</span>
                                </div>
                            @endif
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                <span class="text-lg font-bold text-red-600">{{ __('Balance Due') }}</span>
                                <span
                                    class="text-lg font-bold text-red-600">{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->balance_due, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($viewingInvoice->notes || $viewingInvoice->terms)<div class="grid grid-cols-2 gap-8 pt-8 border-t border-gray-100 dark:border-gray-800">
                            @if($viewingInvoice->notes)<div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Notes') }}</h4>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none">{!! $viewingInvoice->notes !!}</div>
                                </div>
                            @endif
                            @if($viewingInvoice->terms)<div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Terms & Conditions') }}</h4>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none">{!! $viewingInvoice->terms !!}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showViewModal', false)" class="btn-secondary">{{ __('Close View') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Cancel Modal -->
    @if($showCancelModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showCancelModal', false)">
            <div class="modal-box max-w-sm">
                <div class="p-5 text-center">
                    <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="x-circle" class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Cancel Invoice?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to cancel this invoice? This will mark it as cancelled.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showCancelModal', false)" class="btn-secondary">{{ __('Keep Invoice') }}</button>
                        <button wire:click="cancelInvoice" class="btn-danger" title="{{ __('Cancel Invoice') }}">
                            <x-icon name="x-circle" class="w-4 h-4" />{{ __('Yes, Cancel it') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Payment Modal -->
    <x-admin.modal wire:model="showPaymentModal" title="{{ __('Record Payment') }}">
        <form wire:submit="recordPayment">
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Payment Type') }} <span class="text-red-500">*</span></label>
                    <select wire:model.live="payment_type" class="form-input w-full">
                        <option value="full">{{ __('Full Payment') }}</option>
                        <option value="deposit">{{ __('Initial Deposit') }}</option>
                        <option value="partial">{{ __('Partial Payment') }}</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Amount') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">{{ $currency_symbol ?? '$' }}</span>
                        </div>
                        <input type="number" step="0.01" wire:model="payment_amount" class="form-input w-full !pl-8" required 
                            {{ $payment_type === 'full' ? 'readonly' : '' }}>
                    </div>
                    @error('payment_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Payment Date') }} <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="payment_date" class="form-input w-full" required>
                        @error('payment_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Payment Method') }} <span class="text-red-500">*</span></label>
                        <select wire:model="payment_method" class="form-input w-full" required>
                            <option value="">{{ __('Select Method') }}</option>
                            <option value="Cash">{{ __('Cash') }}</option>
                            <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                            <option value="Credit Card">{{ __('Credit Card') }}</option>
                            <option value="Check">{{ __('Check') }}</option>
                            <option value="Other">{{ __('Other') }}</option>
                        </select>
                        @error('payment_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Reference Number / ID') }}</label>
                    <input type="text" wire:model="payment_reference" class="form-input w-full">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Notes') }}</label>
                    <textarea wire:model="payment_notes" class="form-input w-full" rows="2"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                <button type="button" wire:click="$set('showPaymentModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-primary">
                    <x-icon name="check" class="w-4 h-4" />{{ __('Save Payment') }}
                </button>
            </div>
        </form>
    </x-admin.modal>
</div>
