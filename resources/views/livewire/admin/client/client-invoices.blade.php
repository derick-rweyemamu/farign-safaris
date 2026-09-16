<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('My Invoices') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('Review your invoices, payment status, and balances due.') }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-card p-4 rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="relative w-full sm:w-80">
            <x-icon name="magnifying-glass" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search invoice number...') }}"
                class="w-full pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-gray-50 dark:bg-gray-900/50 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>

        <div class="w-full sm:w-auto flex gap-3">
            <select wire:model.live="statusFilter"
                class="w-full sm:w-48 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-gray-50 dark:bg-gray-900/50 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="unpaid">{{ __('Unpaid') }}</option>
                <option value="paid">{{ __('Paid') }}</option>
                <option value="overdue">{{ __('Overdue') }}</option>
                <option value="cancelled">{{ __('Cancelled') }}</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('invoice_number')">
                            <span class="flex items-center gap-1">
                                Invoice #
                                @if($sortBy === 'invoice_number')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('created_date')">
                            <span class="flex items-center gap-1">
                                Invoice Date
                                @if($sortBy === 'created_date')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('due_date')">
                            <span class="flex items-center gap-1">
                                Due Date
                                @if($sortBy === 'due_date')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('status')">
                            <span class="flex items-center gap-1">
                                Status
                                @if($sortBy === 'status')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold text-right cursor-pointer" wire:click="sort('total')">
                            <span class="flex items-center justify-end gap-1">
                                Total
                                @if($sortBy === 'total')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold text-right">{{ __('Balance Due') }}</th>
                        <th class="px-5 py-3 font-semibold text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $invoice->created_date ? \Illuminate\Support\Carbon::parse($invoice->created_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $invoice->due_date ? \Illuminate\Support\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <span class="badge {{
                                    $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                    ($invoice->status === 'unpaid' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' :
                                    ($invoice->status === 'overdue' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' :
                                    'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300'))
                                }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white text-right">
                                {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-amber-600 dark:text-amber-400 text-right">
                                {{ $invoice->currency_symbol }}{{ number_format($invoice->balance_due, 2) }}
                            </td>
                            <td class="px-5 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- View --}}
                                    <button wire:click="viewInvoice({{ $invoice->id }})"
                                        class="inline-flex items-center justify-center p-1.5 bg-sky-50 text-sky-600 dark:bg-sky-950/40 dark:text-sky-400 hover:bg-sky-100 dark:hover:bg-sky-900/40 rounded transition-colors"
                                        title="{{ __('View Invoice') }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    {{-- Print/Download PDF --}}
                                    <a href="{{ route('cms.pdf.invoice', $invoice->id) }}" target="_blank"
                                        class="inline-flex items-center justify-center p-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded transition-colors"
                                        title="{{ __('Download / Print PDF') }}">
                                        <x-icon name="printer" class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-400 text-sm">{{ __('No invoices found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($invoices->hasPages())<div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    {{-- View Invoice Modal --}}
    @if($showViewModal && $viewingInvoice)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="closeView">
    <div class="bg-white dark:bg-gray-900 rounded-[3px] shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto mx-4">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice {{ $viewingInvoice->invoice_number }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $viewingInvoice->created_date?->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cms.pdf.invoice', $viewingInvoice->id) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 hover:bg-indigo-100 rounded text-sm font-medium transition-colors"
                    title="{{ __('Print / Download PDF') }}">
                    <x-icon name="printer" class="w-4 h-4" />{{ __('Print') }}</a>
                <button wire:click="closeView" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Invoice Details --}}
        <div class="px-6 py-4 space-y-6">
            {{-- Status & Dates --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('Status') }}</p>
                    <span class="badge mt-1 inline-block {{
                        $viewingInvoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                        ($viewingInvoice->status === 'unpaid' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' :
                        ($viewingInvoice->status === 'overdue' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' :
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'))
                    }}">{{ ucfirst($viewingInvoice->status) }}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('Invoice Date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $viewingInvoice->created_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('Due Date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $viewingInvoice->due_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">{{ __('Currency') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $viewingInvoice->currency_code }}</p>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="border border-gray-100 dark:border-gray-800 rounded-[3px] overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Item') }}</th>
                            <th class="px-4 py-3 text-center">{{ __('Qty') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Unit Price') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($viewingInvoice->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $item->item_name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-500 dark:text-gray-400">{{ $viewingInvoice->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ $viewingInvoice->currency_symbol }}{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>{{ __('Subtotal') }}</span>
                        <span>{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->subtotal, 2) }}</span>
                    </div>
                    @if($viewingInvoice->discount > 0)
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>{{ __('Discount') }}</span>
                        <span class="text-rose-500">-{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->discount, 2) }}</span>
                    </div>
                    @endif
                    @if($viewingInvoice->tax_total > 0)
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>{{ __('Tax') }}</span>
                        <span>{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->tax_total, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span>{{ __('Total') }}</span>
                        <span>{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-semibold text-amber-600 dark:text-amber-400">
                        <span>{{ __('Balance Due') }}</span>
                        <span>{{ $viewingInvoice->currency_symbol }}{{ number_format($viewingInvoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($viewingInvoice->notes)<div class="bg-gray-50 dark:bg-gray-800/50 rounded-[3px] p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ __('Notes') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $viewingInvoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>
    </div>
    @endif
</div>

