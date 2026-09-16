<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('My Payments') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('Check your billing transactions and payment history.') }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-card p-4 rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="relative w-full sm:w-80">
            <x-icon name="magnifying-glass" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search reference or invoice...') }}"
                class="w-full pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-gray-50 dark:bg-gray-900/50 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('reference_number')">
                            <span class="flex items-center gap-1">
                                Reference #
                                @if($sortBy === 'reference_number')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold">Invoice #</th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('payment_date')">
                            <span class="flex items-center gap-1">
                                Payment Date
                                @if($sortBy === 'payment_date')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold">{{ __('Method') }}</th>
                        <th class="px-5 py-3 font-semibold text-right" wire:click="sort('amount')">
                            <span class="flex items-center justify-end gap-1">
                                Amount
                                @if($sortBy === 'amount')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $payment->reference_number ?: 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $payment->invoice?->invoice_number ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $payment->payment_date ? \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-emerald-600 dark:text-emerald-400 text-right">
                                {{ $payment->invoice?->currency_symbol ?? '$' }}{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $payment->notes }}">
                                {{ $payment->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">{{ __('No payment history found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payments->hasPages())<div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

