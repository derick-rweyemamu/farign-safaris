<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Payments') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $payments->total() }} total payments</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view payments')
            <button wire:click="openReportModal" class="btn-secondary">
                <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create payment')
            <button wire:click="openCreate" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('New Payment') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Payments Report') }}" :show="$showReportModal" />

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
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="paid">{{ __('Paid') }}</option>
                    <option value="unpaid">{{ __('Unpaid') }}</option>
                </select>
            </div>

            <!-- Right side - Search with medium width -->
            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by...') }}"
                    class="form-input text-xs py-1.5 !pl-8 w-full">
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
                            <button wire:click="sort('payment_date')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Date
                                @if($sortBy === 'payment_date') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>Invoice #</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Method') }}</th>
                        <th>
                            <button wire:click="sort('amount')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Amount
                                @if($sortBy === 'amount') <x-icon
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
                    @forelse($payments as $pay)
                        <tr wire:key="pay-{{ $pay->id }}" class="animate-fade-in">
                            <td class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $pay->payment_date?->format('M d, Y') ?? '—' }}
                            </td>
                            <td class="font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $pay->invoice?->invoice_number ?? '—' }}
                            </td>
                            <td>
                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $pay->client?->name ?? 'Unknown Client' }}</div>
                            </td>
                            <td class="text-sm text-gray-500 capitalize">
                                {{ str_replace('_', ' ', $pay->payment_method) }}
                                @if($pay->status === 'cancelled')
                                    <span class="inline-flex items-center px-1.5 py-0.5 ml-2 rounded text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                        {{ __('Cancelled') }}
                                    </span>
                                @endif
                            </td>
                            <td class="font-bold {{ $pay->status === 'cancelled' ? 'text-gray-400 line-through' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $pay->invoice?->currency_symbol ?? '$' }}{{ number_format($pay->amount, 2) }}
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @if($pay->invoice && $pay->amount == $pay->invoice->total)
                                    <a href="{{ route('cms.pdf.receipt', $pay->invoice_id) }}" target="_blank"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                        title="{{ __('Print Receipt') }}">
                                        <x-icon name="printer" class="w-4 h-4" />
                                    </a>
                                    @endif
                                    @can('edit payment')
                                    <button wire:click="openEdit({{ $pay->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                        title="{{ __('Edit payment') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete payment')
                                    @if($pay->status !== 'cancelled')
                                    <button wire:click="confirmCancel({{ $pay->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                        title="{{ __('Cancel Payment') }}">
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
                                <x-icon name="currency-dollar"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No payments found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-lg max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Payment' : 'New Payment' }}
                    </h3>
                    <button @click="$wire.set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <!-- Invoice Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Invoice *</label>
                        <select wire:model.live="invoice_id" class="form-input w-full text-xs">
                            <option value="">{{ __('Select invoice...') }}</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->invoice_number }} - {{ $inv->client?->name ?? 'Unknown Client' }} (Due:
                                    {{ $inv->currency_symbol ?? '$' }}{{ number_format($inv->balance_due, 2) }})</option>
                            @endforeach
                        </select>
                        @error('invoice_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Client (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Client') }}</label>
                        <select wire:model="client_id" class="form-input w-full text-xs bg-gray-50 dark:bg-gray-800"
                            disabled>
                            <option value="">{{ __('Select client...') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Amount ({{ $currency_symbol }})
                                *</label>
                            <input type="number" step="0.01" wire:model="amount"
                                class="form-input w-full text-xs font-bold text-emerald-600 {{ $editingId ? 'bg-gray-50 dark:bg-gray-800' : '' }}" placeholder="0.00" {{ $editingId ? 'disabled' : '' }}>
                            @error('amount') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date *</label>
                            <input type="date" wire:model="payment_date" class="form-input w-full text-xs {{ $editingId ? 'bg-gray-50 dark:bg-gray-800' : '' }}" {{ $editingId ? 'disabled' : '' }}>
                            @error('payment_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Method *</label>
                        <select wire:model="payment_method" class="form-input w-full text-xs">
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                            <option value="cash">{{ __('Cash') }}</option>
                            <option value="credit_card">{{ __('Credit Card') }}</option>
                            <option value="cheque">{{ __('Cheque') }}</option>
                            <option value="mobile_money">{{ __('Mobile Money') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>

                    <!-- Reference -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Reference / TXN
                            ID') }}</label>
                        <input type="text" wire:model="reference_number" class="form-input w-full text-xs"
                            placeholder="{{ __('e.g. REF123') }}">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Notes') }}</label>
                        <textarea wire:model="notes" rows="2" class="form-input w-full text-xs resize-none"
                            placeholder="{{ __('Details...') }}"></textarea>
                    </div>
                </div>

                <div
                    class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    <button @click="$wire.set('showModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="save" class="btn-primary" wire:loading.attr="disabled">
                        <x-icon name="check" class="w-4 h-4" wire:loading.remove wire:target="save" />
                        <span wire:loading.remove wire:target="save">{{ __('Save Payment') }}</span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </button>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Cancel Payment?') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to cancel this payment? This will update the invoice balance.') }}</p>
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <button wire:click="$set('showCancelModal', false)" class="btn-secondary">{{ __('Keep Payment') }}</button>
                        <button wire:click="cancelPayment" class="btn-danger" title="{{ __('Cancel Payment') }}">
                            <x-icon name="x-circle" class="w-4 h-4" />{{ __('Yes, Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
