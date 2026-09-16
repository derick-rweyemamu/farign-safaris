<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Estimations') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $estimations->total() }} total estimates</p>
        </div>
        <div class="flex items-center gap-3">
            @can('view estimate reports')
                <button wire:click="openReportModal" class="btn-secondary">
                    <x-icon name="document-chart-bar" class="w-4 h-4" />{{ __('Reports') }}</button>
            @endcan
            @can('create estimate')
                <button wire:click="openCreate" class="btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('New Estimate') }}</button>
            @endcan
        </div>
    </div>

    <!-- Reports Modal -->
    <x-admin.report-modal title="{{ __('Estimations Report') }}" :show="$showReportModal">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
            <select wire:model="statusFilter" class="form-input text-sm">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="draft">{{ __('Draft') }}</option>
                <option value="sent">{{ __('Sent') }}</option>
                <option value="accepted">{{ __('Accepted') }}</option>
                <option value="declined">{{ __('Declined') }}</option>
                <option value="expired">{{ __('Expired') }}</option>
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
                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="sent">{{ __('Sent') }}</option>
                    <option value="accepted">{{ __('Accepted') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
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
                            <button wire:click="sort('estimate_number')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Estimate #
                                @if($sortBy === 'estimate_number') <x-icon
                                    name="{{ $sortDir === 'asc' ? 'trending-up' : 'trending-down' }}" class="w-3 h-3" />
                                @endif
                            </button>
                        </th>
                        <th>{{ __('Client') }}</th>
                        <th>
                            <button wire:click="sort('date')"
                                class="flex items-center gap-1 uppercase text-xs font-semibold tracking-wider">
                                Date
                                @if($sortBy === 'date') <x-icon
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
                    @forelse($estimations as $est)
                        <tr wire:key="est-{{ $est->id }}" class="animate-fade-in">
                            <td class="font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $est->estimate_number }}
                            </td>
                            <td>
                                @if($est->client)<div class="font-medium text-gray-900 dark:text-gray-100">{{ $est->client->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $est->client->email }}</div>
                                @elseif($est->lead)<div class="font-medium text-gray-900 dark:text-gray-100">{{ $est->lead->name }} <span class="badge bg-amber-100 text-amber-700 ml-1 text-[10px]">{{ __('Lead') }}</span></div>
                                    <div class="text-xs text-gray-500">{{ $est->lead->email }}</div>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $est->date instanceof \DateTimeInterface ? $est->date->format('M d, Y') : ($est->date ? \Illuminate\Support\Carbon::parse($est->date)->format('M d, Y') : '—') }}
                            </td>
                            <td>
                                @php $status = $est->status; @endphp
                                <span
                                    class="badge flex items-center gap-1.5 w-fit {{ $status === 'draft' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' : '' }} {{ $status === 'sent' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : '' }} {{ $status === 'accepted' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : '' }} {{ $status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : '' }}">
                                    <span
                                        class="status-dot{{ $status === 'draft' ? 'offline' : '' }} {{ $status === 'sent' ? 'online' : '' }} {{ $status === 'accepted' ? 'online' : '' }} {{ $status === 'rejected' ? 'offline' : '' }}">
                                    </span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="font-medium text-gray-900 dark:text-white">
                                {{ $est->currency_symbol ?? '$' }}{{ number_format($est->total, 2) }}
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    <button wire:click="openView({{ $est->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors"
                                        title="{{ __('View estimation') }}">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    @can('send estimate')
                                        <button wire:click="sendEstimate({{ $est->id }})"
                                            class="p-2 rounded-[3px] transition-colors {{ $est->invoice ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30' }}"
                                            title="{{ $est->invoice ? 'Converted to invoice' : 'Send estimate' }}"
                                            {{ $est->invoice ? 'disabled' : '' }}>
                                            <x-icon name="paper-airplane" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('convert to invoice')
                                        <button wire:click="convertToInvoice({{ $est->id }})"
                                            class="p-2 rounded-[3px] transition-colors {{ $est->invoice ? 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 cursor-not-allowed' : 'text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30' }}"
                                            title="{{ $est->invoice ? 'Already converted' : 'Convert to Invoice' }}"
                                            {{ $est->invoice ? 'disabled' : '' }}>
                                            <x-icon name="{{ $est->invoice ? 'check-circle' : 'arrow-path' }}" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @if($est->status === 'accepted' && $est->tour_id && !$est->itinerary_id)
                                        <button wire:click="convertToItinerary({{ $est->id }})"
                                            class="p-2 rounded-[3px] transition-colors text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                                            title="{{ __('Convert to Itinerary') }}">
                                            <x-icon name="map" class="w-4 h-4" />
                                        </button>
                                    @endif
                                    @can('edit estimate')
                                        <button wire:click="openEdit({{ $est->id }})"
                                            class="p-2 rounded-[3px] transition-colors {{ $est->invoice ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30' }}"
                                            title="{{ $est->invoice ? 'Converted to invoice' : 'Edit estimation' }}"
                                            {{ $est->invoice ? 'disabled' : '' }}>
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete estimate')
                                        <button wire:click="confirmDelete({{ $est->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="{{ __('Delete estimation') }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <x-icon name="document-text"
                                    class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No estimations found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($estimations->hasPages())<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $estimations->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $editingId ? 'Edit Estimation: ' . $estimate_number : 'New Estimation' }}
                    </h3>
                    <div class="flex items-center gap-2">
                        @if($editingId)
                            @can('print estimate')
                                <a href="{{ route('cms.pdf.estimate', $editingId) }}" target="_blank"
                                    class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                    title="{{ __('Download PDF') }}">
                                    <x-icon name="document-text" class="w-5 h-5" />
                                </a>
                            @endcan
                        @endif
                        <button @click="$wire.set('showModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <x-icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div class="p-5 space-y-4 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                        <input type="radio" wire:model.live="target_type" value="lead" class="text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                        <span class="text-[10px] text-gray-500 uppercase font-medium">{{ __('Lead') }}</span>
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <input type="radio" wire:model.live="target_type" value="custom" class="text-indigo-600 focus:ring-indigo-500 h-3 w-3">
                                        <span class="text-[10px] text-gray-500 uppercase font-medium">{{ __('Custom') }}</span>
                                    </label>
                                </div>
                            </div>
                            
                            @if($target_type === 'client')
                                <select wire:model.live="client_id" class="form-input w-full">
                                    <option value="">{{ __('Select client...') }}</option>
                                    @foreach($clients as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @elseif($target_type === 'lead')
                                <select wire:model.live="lead_id" class="form-input w-full">
                                    <option value="">{{ __('Select lead...') }}</option>
                                    @foreach($leads as $l)
                                        <option value="{{ $l->id }}">{{ $l->name }}</option>
                                    @endforeach
                                </select>
                                @error('lead_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @elseif($target_type === 'custom')
                                <input type="text" wire:model="custom_name" placeholder="{{ __('Enter custom name...') }}" class="form-input w-full">
                                @error('custom_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            @endif
                        </div>



                        <!-- tax rate -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tax Rate
                                *</label>
                            <select wire:model.live="tax_rate_index" class="form-input w-full">
                                <option value="">{{ __('No Tax') }}</option>
                                @foreach($tax_rates as $index => $rate)
                                    <option value="{{ $index }}">{{ $rate['name'] }} ({{ $rate['rate'] }}%)</option>
                                @endforeach
                            </select>
                            @error('tax_rate_index') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>@enderror
                        </div>


                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Date') }}</label>
                            <input type="date" wire:model.live="date" class="form-input w-full">
                            @error('date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- due_date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Due
                                Date') }}</label>
                            <input type="date" wire:model="due_date" class="form-input w-full">
                            @error('due_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Currency Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Currency
                                *</label>
                            <select wire:model.live="currency_code" class="form-input w-full">
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
                                    class="form-input w-full !pl-8">
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
                            <select wire:model="status" class="form-input w-full">
                                <option value="draft">{{ __('Draft') }}</option>
                                <option value="sent">{{ __('Sent') }}</option>
                                <option value="accepted">{{ __('Accepted') }}</option>
                                <option value="rejected">{{ __('Rejected') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Estimate Type Toggle (Better UI) -->
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 mb-6">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">{{ __('What are you estimating?') }}</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex cursor-pointer rounded-[3px] border bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none {{ $estimate_type === 'product' ? 'border-indigo-600 ring-1 ring-indigo-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="estimate_type" value="product" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ __('Custom Products & Services') }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">{{ __('Build a custom line-item quote.') }}</span>
                                    </span>
                                </span>
                                <x-icon name="check-circle" class="h-5 w-5 text-indigo-600 {{ $estimate_type === 'product' ? 'block' : 'invisible' }}" />
                            </label>
                            
                            <label class="relative flex cursor-pointer rounded-[3px] border bg-white dark:bg-gray-900 p-4 shadow-sm focus:outline-none {{ $estimate_type === 'tour' ? 'border-indigo-600 ring-1 ring-indigo-600' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                                <input type="radio" wire:model.live="estimate_type" value="tour" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ __('Tour Package') }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">{{ __('Quote based on a standard tour.') }}</span>
                                    </span>
                                </span>
                                <x-icon name="check-circle" class="h-5 w-5 text-indigo-600 {{ $estimate_type === 'tour' ? 'block' : 'invisible' }}" />
                            </label>
                        </div>
                    </div>

                    @if($estimate_type === 'tour')
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
                                                    placeholder="{{ __('Description') }}" class="form-input w-full text-xs" style="{{ $estimate_type === 'tour' ? 'display: none;' : '' }}">
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
                                                    class="text-red-500 hover:text-red-700" title="{{ __('Delete Estimation') }}">
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
                                        <th class="px-4 py-2 text-center w-32">{{ __('Price') }}</th>
                                        <th class="px-4 py-2 text-center w-32">Tax %</th>
                                        <th class="px-4 py-2 text-center w-48">{{ __('Disc') }}</th>
                                        <th class="px-4 py-2 text-right w-32">{{ __('Total') }}</th>
                                        <th class="px-4 py-2 text-right w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($items as $index => $item)
                                        <tr wire:key="modal-item-{{ $index }}">
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
                                                        class="form-input text-xs !py-1 !pl-2 !pr-4 text-start  col-span-1">
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
                                                    class="text-red-500 hover:text-red-700" title="{{ __('Delete Estimation') }}">
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
                        <!-- Totals -->
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
                                    <span class="font-medium">{{ $currency_symbol }}{{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="pt-2 border-t flex justify-between font-bold text-lg text-indigo-600">
                                    <span>{{ __('Total') }}</span>
                                    <span>{{ $currency_symbol }}{{ number_format($total, 2) }}</span>
                                </div>
                                <div
                                    class="pt-1 flex justify-between text-[10px] text-gray-400 font-mono uppercase tracking-tight">
                                    <span>{{ __('Equivalent in Base') }}</span>
                                    <span>{{ app(\App\Settings\GeneralSetting::class)->currency_base }}
                                        {{ number_format($base_grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes and Terms -->
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
                            <span wire:loading.remove wire:target="save">{{ __('Save Changes') }}</span>
                            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </div>
            </div>
    @endif

        <!-- Delete Modal -->
        @if($showDeleteModal)
            <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
                <div class="modal-box max-w-sm">
                    <div class="p-5 text-center">
                        <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-icon name="trash" class="w-8 h-8 text-white" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Estimation?') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this estimation? This cannot be undone.') }}</p>
                        <div class="flex items-center justify-center gap-3 mt-6">
                            <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                            <button wire:click="deleteEstimation" class="btn-danger" title="{{ __('Delete Estimation') }}">
                                <x-icon name="trash" class="w-4 h-4" />{{ __('Delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- View Modal -->
        @if($showViewModal && $viewingEstimation)
            <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showViewModal', false)">
                <div class="modal-box max-w-4xl flex flex-col overflow-hidden">
                    <div
                        class="flex-shrink-0 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            Estimation Details: {{ $viewingEstimation->estimate_number }}
                        </h3>
                        <div class="flex items-center gap-2">
                            @can('print estimate')
                                <a href="{{ route('cms.pdf.estimate', $viewingEstimation->id) }}" target="_blank"
                                    class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                    title="{{ __('Download PDF') }}">
                                    <x-icon name="document-text" class="w-5 h-5" />
                                </a>
                            @endcan
                            @can('send estimate')
                                <button wire:click="sendEstimate({{ $viewingEstimation->id }})"
                                    class="p-2 rounded-[3px] transition-colors {{ $viewingEstimation->invoice ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30' }}"
                                    title="{{ $viewingEstimation->invoice ? 'Converted to invoice' : 'Send to Client' }}"
                                    {{ $viewingEstimation->invoice ? 'disabled' : '' }} wire:loading.attr="disabled" wire:target="sendEstimate">
                                    <x-icon name="paper-airplane" class="w-5 h-5" wire:loading.remove
                                        wire:target="sendEstimate" />
                                </button>
                            @endcan
                            <button @click="$wire.set('showViewModal', false)"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <x-icon name="x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <div class="p-8 space-y-8 overflow-y-auto flex-1 bg-white dark:bg-gray-900">
                        <!-- Header Details -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('To') }}</h4>
                                @if($viewingEstimation->client)<p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $viewingEstimation->client->name }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $viewingEstimation->client->email }}</p>
                                    <p class="text-sm text-gray-500">{{ $viewingEstimation->client->phone }}</p>
                                @elseif($viewingEstimation->lead)<p class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        {{ $viewingEstimation->lead->name }} <span class="badge bg-amber-100 text-amber-700 text-[10px]">{{ __('Lead') }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $viewingEstimation->lead->email }}</p>
                                    <p class="text-sm text-gray-500">{{ $viewingEstimation->lead->phone }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('Status') }}</h4>
                                <span
                                    class="badge {{ $viewingEstimation->status === 'accepted' ? 'bg-emerald-100 text-emerald-700' : ($viewingEstimation->status === 'draft' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($viewingEstimation->status) }}
                                </span>
                                <div class="mt-4 space-y-1">
                                    <p class="text-sm text-gray-500">{{ __('Date:') }}<span
                                            class="font-medium text-gray-900 dark:text-white">{{ $viewingEstimation->date ? \Illuminate\Support\Carbon::parse($viewingEstimation->date)->format('M d, Y') : '-' }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">{{ __('Expiry:') }}<span
                                            class="font-medium text-gray-900 dark:text-white">{{ $viewingEstimation->expiry_date ? \Illuminate\Support\Carbon::parse($viewingEstimation->expiry_date)->format('M d, Y') : '-' }}</span>
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
                                    @foreach($viewingEstimation->items as $item)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $item->item_name }}
                                                </div>
                                                @if($item->product)<div class="text-xs text-gray-400">{{ $item->product->sku }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                                {{ $viewingEstimation->currency_symbol }}{{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                                {{ $viewingEstimation->currency_symbol }}{{ number_format($item->total, 2) }}
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
                                        class="text-gray-900 dark:text-white font-medium">{{ $viewingEstimation->currency_symbol }}{{ number_format($viewingEstimation->subtotal, 2) }}</span>
                                </div>
                                @if($viewingEstimation->tax > 0)
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>{{ __('Tax') }}</span>
                                        <span
                                            class="text-gray-900 dark:text-white font-medium">{{ $viewingEstimation->currency_symbol }}{{ number_format($viewingEstimation->tax, 2) }}</span>
                                    </div>
                                @endif
                                @if($viewingEstimation->discount > 0)
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>{{ __('Discount') }}</span>
                                        <span
                                            class="text-red-500 font-medium">-{{ $viewingEstimation->currency_symbol }}{{ number_format($viewingEstimation->discount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Total Estimate') }}</span>
                                    <span
                                        class="text-lg font-bold text-indigo-600">{{ $viewingEstimation->currency_symbol }}{{ number_format($viewingEstimation->total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($viewingEstimation->notes || $viewingEstimation->terms)<div class="grid grid-cols-2 gap-8 pt-8 border-t border-gray-100 dark:border-gray-800">
                                @if($viewingEstimation->notes)<div>
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Notes') }}</h4>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none">{!! $viewingEstimation->notes !!}</div>
                                    </div>
                                @endif
                                @if($viewingEstimation->terms)<div>
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Terms & Conditions') }}</h4>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 prose dark:prose-invert max-w-none">{!! $viewingEstimation->terms !!}</div>
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

    </div>
