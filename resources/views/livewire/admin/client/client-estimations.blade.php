<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('My Estimations') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('View and review your tour proposals and price quotes.') }}</p>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-card p-4 rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="relative w-full sm:w-80">
            <x-icon name="magnifying-glass" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search estimation number...') }}"
                class="w-full pl-9 pr-4 py-2 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-gray-50 dark:bg-gray-900/50 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
        </div>

        <div class="w-full sm:w-auto flex gap-3">
            <select wire:model.live="statusFilter"
                class="w-full sm:w-48 border border-gray-200 dark:border-gray-700 rounded-[3px] bg-gray-50 dark:bg-gray-900/50 py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="draft">{{ __('Draft') }}</option>
                <option value="sent">{{ __('Sent') }}</option>
                <option value="accepted">{{ __('Accepted') }}</option>
                <option value="rejected">{{ __('Rejected') }}</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-[3px] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('estimate_number')">
                            <span class="flex items-center gap-1">
                                Estimate #
                                @if($sortBy === 'estimate_number')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('date')">
                            <span class="flex items-center gap-1">
                                Date
                                @if($sortBy === 'date')
                                    <x-icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-amber-500" />
                                @endif
                            </span>
                        </th>
                        <th class="px-5 py-3 font-semibold cursor-pointer" wire:click="sort('expiry_date')">
                            <span class="flex items-center gap-1">
                                Expiry Date
                                @if($sortBy === 'expiry_date')
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
                        <th class="px-5 py-3 font-semibold text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($estimations as $estimate)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $estimate->estimate_number }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $estimate->date ? \Illuminate\Support\Carbon::parse($estimate->date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $estimate->expiry_date ? \Illuminate\Support\Carbon::parse($estimate->expiry_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <span class="badge {{
                                    $estimate->status === 'accepted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' :
                                    ($estimate->status === 'rejected' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' :
                                    ($estimate->status === 'sent' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' :
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'))
                                }}">
                                    {{ ucfirst($estimate->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white text-right">
                                {{ $estimate->currency_symbol }}{{ number_format($estimate->total, 2) }}
                            </td>
                            <td class="px-5 py-4 text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(in_array($estimate->status, ['sent', 'draft']))
                                        <button wire:click="updateStatus({{ $estimate->id }}, 'accepted')"
                                            class="inline-flex items-center justify-center p-1.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 rounded transition-colors"
                                            title="{{ __('Accept Estimate') }}">
                                            <x-icon name="check" class="w-4 h-4" />
                                        </button>
                                        <button wire:click="updateStatus({{ $estimate->id }}, 'rejected')"
                                            class="inline-flex items-center justify-center p-1.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 rounded transition-colors"
                                            title="{{ __('Reject Estimate') }}">
                                            <x-icon name="x-mark" class="w-4 h-4" />
                                        </button>
                                    @endif
                                    <a href="{{ route('cms.pdf.estimate', $estimate->id) }}" target="_blank"
                                        class="inline-flex items-center justify-center p-1.5 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded transition-colors"
                                        title="{{ __('Download PDF') }}">
                                        <x-icon name="document-arrow-down" class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">{{ __('No estimations found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($estimations->hasPages())<div class="px-5 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $estimations->links() }}
            </div>
        @endif
    </div>
</div>

