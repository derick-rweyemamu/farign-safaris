<div class="space-y-4 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Itineraries & Quotes') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Manage Safari Packages and Client Proposals') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create itineraries')
            <button wire:click="create" class="btn-primary">
                <x-icon name="plus" class="w-4 h-4" />{{ __('Build Itinerary') }}</button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card rounded-[3px] p-3 shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <select wire:model.live="perPage" class="form-input text-xs py-1.5 px-2 w-24">
                    <option value="10">10 / {{ __('page') }}</option>
                    <option value="25">25 / {{ __('page') }}</option>
                    <option value="50">50 / {{ __('page') }}</option>
                </select>

                <select wire:model.live="statusFilter" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="sent">{{ __('Sent') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>
            </div>

            <div class="sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by title, ref, client...') }}"
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
                        <th>Ref #</th>
                        <th>{{ __('Title / Client') }}</th>
                        <th>{{ __('Dates') }}</th>
                        <th>{{ __('Pax') }}</th>
                        <th>{{ __('Total Price') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itineraries as $itinerary)
                        <tr wire:key="itinerary-{{ $itinerary->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors animate-fade-in">
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700/60">
                                    #{{ $itinerary->reference_number }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 leading-tight">{{ $itinerary->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-450 mt-1 flex items-center gap-1">
                                    <x-icon name="user" class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ $itinerary->client_name ?: 'Generic Package' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5 text-xs font-medium text-gray-600 dark:text-gray-400">
                                    <x-icon name="calendar-days" class="w-4 h-4 text-gray-400" />
                                    <span>
                                        @if($itinerary->start_date && $itinerary->end_date)
                                            {{ $itinerary->start_date->format('M d') }} - {{ $itinerary->end_date->format('M d, Y') }}
                                        @else
                                            TBD
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 text-xs font-semibold border border-indigo-100 dark:border-indigo-900/30">
                                        {{ $itinerary->adults }}A
                                    </span>
                                    @if($itinerary->children)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-400 text-xs font-semibold border border-sky-100 dark:border-sky-900/30">
                                        {{ $itinerary->children }}C
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="font-bold text-xs text-gray-900 dark:text-white bg-gray-100/80 dark:bg-gray-800/80 px-2 py-1 rounded border border-gray-200/50 dark:border-gray-700/50">
                                    {{ $itinerary->currency }} {{ number_format($itinerary->total_price, 2) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
                                        'sent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                    $color = $statusColors[$itinerary->status] ?? $statusColors['draft'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $color }}">
                                    {{ $itinerary->status }}
                                </span>
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('view itineraries')
                                        <button wire:click="viewPriceBreakdown({{ $itinerary->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                            title="{{ __('Price Breakdown') }}">
                                            <x-icon name="banknotes" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('create itineraries')
                                        <button wire:click="duplicate({{ $itinerary->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors"
                                            title="{{ __('Duplicate Itinerary') }}">
                                            <x-icon name="document-duplicate" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('view itineraries')
                                        <a href="{{ route('cms.pdf.itinerary', $itinerary) }}" target="_blank"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-colors"
                                            title="{{ __('Download PDF') }}">
                                            <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                        </a>
                                    @endcan
                                    @can('create cost sheets')
                                        <button wire:click="generateCostSheet({{ $itinerary->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="generateCostSheet({{ $itinerary->id }})"
                                            class="p-2 rounded-[3px] transition-colors {{ $itinerary->costSheet ? 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30' }}"
                                            title="{{ $itinerary->costSheet ? 'Update Cost Sheet (#' . $itinerary->costSheet->reference_number . ')' : 'Generate Cost Sheet' }}">
                                            <x-icon name="calculator" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    <button wire:click="sendEmail({{ $itinerary->id }})"
                                        class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                        title="{{ __('Send via Email') }}">
                                        <x-icon name="paper-airplane" class="w-4 h-4" />
                                    </button>
                                    @can('edit itineraries')
                                        <button wire:click="edit({{ $itinerary->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit Builder') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete itineraries')
                                        <button wire:click="confirmDelete({{ $itinerary->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                            title="{{ __('Delete') }}">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </x-admin.action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <x-icon name="document-text" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No itineraries found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $itineraries->links() }}
        </div>
    </div>

        <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop bg-black/60 backdrop-blur-md flex items-center justify-center p-4" x-data @keydown.escape.window="$wire.set('showModal', false)">
            <div class="modal-box max-w-4xl max-h-[90vh] flex flex-col overflow-hidden rounded-[3px] shadow-premium border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 transition-all duration-300 animate-fade-in-up">
                
                <!-- Modal Header -->
                <div class="flex-shrink-0 flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-800/80 bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-[3px] bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <x-icon name="document-text" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-none">
                                {{ $isEdit ? 'Edit Proposal Settings' : 'Create New Proposal' }}
                            </h3>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ __('Configure client details, base tours, pricing, and media uploads') }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-850 transition-all">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden" x-data="{
                    activeTab: 'overview',
                    selectedCategory: @entangle('selectedCategory'),
                    seasonalMonths: @entangle('seasonal_months')
                }">
                    <!-- Tabs Navigation -->
                    <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-800/80 bg-gray-50/40 dark:bg-gray-900/20">
                        <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-950/60 p-1.5 rounded-[3px] w-fit overflow-x-auto max-w-full">
                            <button type="button" @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="user-circle" class="w-4 h-4" />{{ __('Proposal Info') }}</button>
                            <button type="button" @click="activeTab = 'details'"
                                :class="activeTab === 'details' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="adjustments-horizontal" class="w-4 h-4" />{{ __('Tour Details') }}</button>
                            <button type="button" @click="activeTab = 'features'"
                                :class="activeTab === 'features' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="check-circle" class="w-4 h-4" />{{ __('Include/Exclude') }}</button>

                            <button type="button" @click="activeTab = 'media'"
                                :class="activeTab === 'media' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="photo" class="w-4 h-4" />{{ __('Media') }}</button>
                            <button type="button" @click="activeTab = 'itinerary'"
                                :class="activeTab === 'itinerary' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="calendar-days" class="w-4 h-4" />{{ __('Day-by-Day') }}</button>
                        </div>
                    </div>

                    <!-- Scrollable Modal Body -->
                    <div class="p-6 overflow-y-auto flex-1 bg-white dark:bg-gray-900">

                        {{-- ============================================================
                             TAB 1: PROPOSAL INFO (client + base tour auto-fill)
                             ============================================================ --}}
                        <div x-show="activeTab === 'overview'" class="space-y-6 animate-fade-in">

                            {{-- Auto-fill from Tour --}}
                            <div class="bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100 dark:border-indigo-900/60 rounded-[3px] p-5 md:p-6 space-y-4 shadow-sm relative overflow-hidden group">
                                <div class="absolute -right-3 -top-3 w-16 h-16 text-indigo-100 dark:text-indigo-950/20 group-hover:scale-110 transition-transform duration-300 pointer-events-none">
                                    <x-icon name="sparkles" class="w-full h-full" />
                                </div>
                                <label class="block text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider mb-2 flex items-center gap-2 select-none">
                                    <x-icon name="sparkles" class="w-4.5 h-4.5 text-indigo-500 animate-pulse" />{{ __('Auto-Fill from Base Tour') }}<span class="font-normal text-[10px] text-indigo-400 dark:text-indigo-500 lowercase">(copies everything from selection)</span>
                                </label>
                                <select wire:model.live="base_tour_id" class="form-input border-indigo-200 dark:border-indigo-900/60 focus:border-indigo-500 bg-white dark:bg-gray-900" @disabled($isEdit)>
                                    <option value="">-- Start from Scratch --</option>
                                    @foreach($tours as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                @if($isEdit)
                                    <p class="text-[10px] text-amber-500 font-semibold mt-1.5 flex items-center gap-1">
                                        <span>⚠</span>{{ __('Base tour cannot be changed while editing. Use the Itinerary Builder to modify days.') }}</p>
                                @else
                                    <p class="text-[10px] text-indigo-405 dark:text-indigo-500 font-semibold mt-1.5 flex items-center gap-1.5">
                                        <span>✦</span>{{ __('Selecting a tour auto-fills: title, description, gallery, categories, destinations, transports, accommodations and all day-by-day itinerary.') }}</p>
                                @endif
                            </div>

                            <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-6">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="information-circle" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Proposal General Info') }}</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Proposal Title') }}<span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="title" class="form-input" placeholder="{{ __('e.g. 7 Days Serengeti Safari for John Doe') }}">
                                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    
                                    <div class="md:col-span-2 space-y-4 mt-2">
                                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                                            <x-icon name="user" class="w-4 h-4 text-indigo-500" />
                                            <span class="text-xs font-bold text-gray-750 dark:text-gray-300 uppercase tracking-wider">{{ __('Client Information') }}</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Client Name') }}</label>
                                                <input type="text" wire:model="client_name" class="form-input" placeholder="{{ __('e.g. John Doe') }}">
                                                @error('client_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Client Email') }}</label>
                                                <input type="email" wire:model="client_email" class="form-input" placeholder="john@example.com">
                                                @error('client_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Trip Start Date') }}</label>
                                                <input type="date" wire:model="start_date" class="form-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Trip End Date') }}</label>
                                                <input type="date" wire:model="end_date" class="form-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Adults') }}<span class="text-red-500">*</span></label>
                                                <input type="number" wire:model="adults" min="1" class="form-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Children') }}</label>
                                                <input type="number" wire:model="children" min="0" class="form-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Currency') }}<span class="text-red-500">*</span></label>
                                                <select wire:model="currency" class="form-input" required>
                                                    @foreach($currencies as $cur)
                                                        <option value="{{ $cur->code }}">{{ $cur->code }} ({{ $cur->symbol }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Link to Estimate (optional)') }}</label>
                                                <select wire:model="estimation_id" class="form-input">
                                                    <option value="">-- No linked estimate --</option>
                                                    @foreach($estimations as $est)
                                                        <option value="{{ $est->id }}">#{{ $est->estimate_number ?? $est->id }} – {{ $est->client?->name ?? 'Unknown' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 pt-2">
                                        <x-admin.rich-text wire:model="description" label="{{ __('Tour Description') }}" />
                                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Tour Categories -->
                            <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 space-y-4">
                                <div class="flex items-center gap-2 pb-2.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="tag" class="w-4.5 h-4.5 text-indigo-500" />
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('Tour Category') }}</span>
                                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500 normal-case">(select one)</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($categories as $cat)
                                        <label
                                            @click.prevent="selectedCategory = (selectedCategory == {{ $cat->id }}) ? null : {{ $cat->id }}"
                                            :class="selectedCategory == {{ $cat->id }} ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'"
                                            class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-[3px] border transition-all duration-200 select-none">
                                            <span class="text-xs font-bold">{{ $cat->name }}</span>
                                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all duration-200 shrink-0"
                                                :class="selectedCategory == {{ $cat->id }} ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900'">
                                                <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedCategory == {{ $cat->id }}"></div>
                                            </div>
                                        </label>
                                    @endforeach
                                    @if($categories->isEmpty())<p class="text-xs text-gray-400 col-span-3">{{ __('No categories configured.') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 2: TOUR DETAILS
                             ============================================================ --}}
                        <div x-show="activeTab === 'details'" class="space-y-6 animate-fade-in" style="display: none;">
                            <!-- Adventure Details Card -->
                            <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="adjustments-horizontal" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Adventure Details') }}</h4>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Duration (Days)') }}</label>
                                        <input type="number" wire:model="duration_days" class="form-input" placeholder="e.g. 5">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Duration (Nights)') }}</label>
                                        <input type="number" wire:model="duration_nights" class="form-input" placeholder="e.g. 4">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Difficulty') }}</label>
                                        <select wire:model="difficulty" class="form-input">
                                            <option value="">{{ __('Select Difficulty') }}</option>
                                            <option value="Easy">{{ __('Easy') }}</option>
                                            <option value="Medium">{{ __('Medium') }}</option>
                                            <option value="Hard">{{ __('Hard') }}</option>
                                            <option value="Challenging">{{ __('Challenging') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Adult Price') }}</label>
                                        <input type="number" step="0.01" wire:model.live="price_per_adult" class="form-input" placeholder="e.g. 500.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Child Price') }}<span class="text-indigo-400 font-normal normal-case">(= 50% of adult)</span></label>
                                        <input type="number" step="0.01" wire:model="price_per_child" class="form-input" placeholder="e.g. 250.00">
                                    </div>
                                    
                                    <div class="col-span-full space-y-2">
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider">{{ __('Seasonal Months') }}</label>
                                        <div class="bg-gray-50/20 dark:bg-gray-950/20 border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 flex flex-wrap gap-2.5">
                                            @foreach([
                                                'January' => 'Jan', 'February' => 'Feb', 'March' => 'Mar', 'April' => 'Apr',
                                                'May' => 'May', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Aug',
                                                'September' => 'Sep', 'October' => 'Oct', 'November' => 'Nov', 'December' => 'Dec'
                                            ] as $fullName => $shortName)
                                                <label class="flex items-center justify-center cursor-pointer px-4 py-2.5 rounded-[3px] border text-xs font-bold transition-all duration-200 select-none min-w-[72px]"
                                                    :class="(seasonalMonths || []).includes('{{ $fullName }}') ? 'bg-amber-50/70 dark:bg-amber-950/30 border-amber-305 dark:border-amber-900 text-amber-700 dark:text-amber-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-605 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'">
                                                    <input type="checkbox" x-model="seasonalMonths" value="{{ $fullName }}" class="sr-only">
                                                    <span>{{ $shortName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Policy & Limits Card -->
                            <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="check-circle" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Policy & Limits') }}</h4>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">Min Guests *</label>
                                        <input type="number" wire:model="min_guests" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Max Guests') }}</label>
                                        <input type="number" wire:model="max_guests" class="form-input" placeholder="{{ __('No limit') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">Min Age *</label>
                                        <input type="number" wire:model="min_age" class="form-input">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Max Age') }}</label>
                                        <input type="number" wire:model="max_age" class="form-input" placeholder="{{ __('No limit') }}">
                                    </div>
                                    
                                    <div class="col-span-full flex flex-wrap items-center justify-between gap-5 mt-2 p-4 bg-gray-50/20 dark:bg-gray-950/20 rounded-[3px] border border-gray-200/60 dark:border-gray-800">
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" wire:model="accepts_solo_travelers" class="text-indigo-600 rounded border-gray-305 dark:bg-gray-800 dark:border-gray-700 w-4 h-4">
                                            <span class="text-xs font-bold text-gray-705 dark:text-gray-300">{{ __('Accepts Solo Travelers') }}</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" wire:model="accepts_children" class="text-indigo-600 rounded border-gray-305 dark:bg-gray-800 dark:border-gray-700 w-4 h-4">
                                            <span class="text-xs font-bold text-gray-705 dark:text-gray-300">{{ __('Accepts Children') }}</span>
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <label class="text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Free Under Age *</label>
                                            <input type="number" wire:model="children_free_under_age" class="form-input w-20 text-center !py-1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 3: INCLUDE / EXCLUDE
                             ============================================================ --}}
                        <div x-show="activeTab === 'features'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Included Features Card -->
                                <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-4">
                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Included Features') }}</h4>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="new_included_feature" class="form-input" placeholder="{{ __('e.g. Airport Transfer') }}" wire:keydown.enter.prevent="addIncludedFeature">
                                        <button type="button" wire:click="addIncludedFeature" class="btn-primary py-2 px-4 shadow-sm">{{ __('Add') }}</button>
                                    </div>
                                    <ul class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                        @foreach($included_features as $index => $feature)
                                            <li class="flex items-center justify-between text-xs p-3 bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800/60 rounded-[3px] transition-all hover:bg-gray-50/50 dark:hover:bg-gray-850/30">
                                                <span class="flex items-center font-semibold text-gray-700 dark:text-gray-300">
                                                    <x-icon name="check" class="w-4 h-4 text-emerald-500 mr-2 shrink-0"/>
                                                    {{ $feature }}
                                                </span>
                                                <button type="button" wire:click="removeIncludedFeature({{ $index }})" class="p-1 rounded-[3px] text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                                    <x-icon name="x-mark" class="w-4 h-4"/>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Excluded Features Card -->
                                <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-4">
                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Excluded Features') }}</h4>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="new_excluded_feature" class="form-input" placeholder="{{ __('e.g. Flight Tickets') }}" wire:keydown.enter.prevent="addExcludedFeature">
                                        <button type="button" wire:click="addExcludedFeature" class="btn-primary py-2 px-4 shadow-sm">{{ __('Add') }}</button>
                                    </div>
                                    <ul class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                        @foreach($excluded_features as $index => $feature)
                                            <li class="flex items-center justify-between text-xs p-3 bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800/60 rounded-[3px] transition-all hover:bg-gray-50/50 dark:hover:bg-gray-850/30">
                                                <span class="flex items-center font-semibold text-gray-700 dark:text-gray-300">
                                                    <x-icon name="x-mark" class="w-4 h-4 text-red-500 mr-2 shrink-0"/>
                                                    {{ $feature }}
                                                </span>
                                                <button type="button" wire:click="removeExcludedFeature({{ $index }})" class="p-1 rounded-[3px] text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                                    <x-icon name="x-mark" class="w-4 h-4"/>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 5: MEDIA
                             ============================================================ --}}
                        <div x-show="activeTab === 'media'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-6">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="photo" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Media Assets') }}</h4>
                                </div>
                                
                                <div class="space-y-6">
                                    <!-- Featured Image Zone -->
                                    <x-admin.image-upload 
                                        :image="$featured_image" 
                                        :existing="$existingImage" 
                                        label="{{ __('Featured Image') }}" 
                                        id="featured_image_input" 
                                        model="featured_image" 
                                    />
                                                                    <!-- Gallery Upload Zone -->
                                    <x-admin.gallery-upload 
                                        :gallery="$gallery" 
                                        :existing="$existingGallery" 
                                        label="{{ __('Gallery Images') }}" 
                                        id="itinerary_gallery_input" 
                                        model="gallery" 
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================
                             TAB 6: DAY-BY-DAY ITINERARY
                             ============================================================ --}}
                        <div x-show="activeTab === 'itinerary'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div class="flex flex-col items-center justify-center p-12 text-center border border-gray-150 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-[3px]">
                                <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-950/40 rounded-full flex items-center justify-center mb-5 text-indigo-650 dark:text-indigo-400">
                                    <x-icon name="calendar-days" class="w-8 h-8" />
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">{{ __('Day-by-Day Itinerary Builder') }}</h3>
                                @if($isEdit)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mb-6 leading-relaxed">{{ __('Build a comprehensive day-to-day itinerary including daily destinations, accommodations, meals, transports and wildlife.') }}</p>
                                    <a href="{{ route('itineraries.builder', $itinerary_id) }}" class="btn-primary py-2.5 px-5 shadow-md shadow-indigo-600/10">{{ __('Open Itinerary Builder') }}<x-icon name="arrow-right" class="w-4 h-4 ml-2" />
                                    </a>
                                @else
                                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mb-6 leading-relaxed">{{ __('First save this proposal, then you can start building the day-by-day itinerary.') }}</p>
                                    <button type="submit" class="btn-primary py-2.5 px-5 shadow-md" @click="activeTab = 'overview'">{{ __('Save Proposal & Continue') }}</button>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800/80 rounded-b-2xl">
                        <button type="button" wire:click="$set('showModal', false)" class="btn-secondary py-2 px-4 shadow-sm">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary py-2 px-4 shadow-sm" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Proposal' : 'Create Proposal' }}</span>
                            <span wire:loading>{{ __('Processing...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal-backdrop" x-data @keydown.escape.window="$wire.set('showDeleteModal', false)">
            <div class="modal-box max-w-sm p-5 text-center">
                <div class="w-16 h-16 gradient-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-8 h-8 text-white" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Itinerary?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Are you sure you want to delete this itinerary? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteItinerary" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Price Breakdown Modal -->
    @if($showPriceBreakdownModal && $breakdownItinerary)
        <div class="modal-backdrop fixed inset-0 bg-black/50 flex items-center justify-center z-50" x-data @keydown.escape.window="$wire.set('showPriceBreakdownModal', false)">
            <div class="modal-box bg-white dark:bg-gray-800 rounded-[3px] max-w-3xl w-full mx-4 max-h-[90vh] flex flex-col shadow-xl">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900 rounded-t-lg">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-icon name="banknotes" class="w-5 h-5 text-indigo-500" />
                        Price Breakdown: {{ $breakdownItinerary->title }}
                    </h3>
                    <button wire:click="closePriceBreakdown" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto">
                    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        <p>{{ __('Total Calculated Price (Rack):') }}<strong class="text-gray-900 dark:text-white">{{ $breakdownItinerary->currency }} {{ number_format($breakdownItinerary->total_price, 2) }}</strong></p>
                    </div>

                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400 border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="p-2 font-medium">{{ __('Day') }}</th>
                                <th class="p-2 font-medium">{{ __('Item') }}</th>
                                <th class="p-2 font-medium text-right">{{ __('Cost') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @php $totalCost = 0; @endphp
                            @foreach($breakdownItinerary->days as $day)
                                @if($day->accommodation_amount || $day->meal_plan_amount || $day->activities->count() || $day->transports->count())<tr class="bg-indigo-50/30 dark:bg-indigo-900/10">
                                        <td colspan="3" class="p-2 font-semibold text-indigo-700 dark:text-indigo-400">{{ $day->day }}</td>
                                    </tr>
                                    
                                    @if($day->accommodation_amount)
                                        @php $totalCost += $day->accommodation_amount; @endphp
                                        <tr>
                                            <td class="p-2"></td>
                                            <td class="p-2"><x-icon name="home" class="w-3 h-3 inline mr-1 text-gray-400"/> Accommodation: {{ $day->accommodation->name ?? 'Custom' }}</td>
                                            <td class="p-2 text-right font-medium">{{ number_format($day->accommodation_amount, 2) }}</td>
                                        </tr>
                                    @endif

                                    @if($day->meal_plan_amount)
                                        @php $totalCost += $day->meal_plan_amount; @endphp
                                        <tr>
                                            <td class="p-2"></td>
                                            <td class="p-2"><x-icon name="cake" class="w-3 h-3 inline mr-1 text-gray-400"/> Meal Plan: {{ $day->mealPlan->name ?? 'Custom' }}</td>
                                            <td class="p-2 text-right font-medium">{{ number_format($day->meal_plan_amount, 2) }}</td>
                                        </tr>
                                    @endif

                                    @foreach($day->activities as $act)
                                        @if($act->pivot->amount)
                                            @php $totalCost += $act->pivot->amount; @endphp
                                            <tr>
                                                <td class="p-2"></td>
                                                <td class="p-2"><x-icon name="ticket" class="w-3 h-3 inline mr-1 text-gray-400"/> Activity: {{ $act->name }}</td>
                                                <td class="p-2 text-right font-medium">{{ number_format($act->pivot->amount, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    @foreach($day->transports as $trans)
                                        @if($trans->pivot->amount)
                                            @php $totalCost += $trans->pivot->amount; @endphp
                                            <tr>
                                                <td class="p-2"></td>
                                                <td class="p-2"><x-icon name="truck" class="w-3 h-3 inline mr-1 text-gray-400"/> Transport: {{ $trans->name }}</td>
                                                <td class="p-2 text-right font-medium">{{ number_format($trans->pivot->amount, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                                <td colspan="2" class="p-3 text-right font-bold text-gray-900 dark:text-white">{{ __('Total Itemized Costs:') }}</td>
                                <td class="p-3 text-right font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($totalCost, 2) }}</td>
                            </tr>
                            <tr class="bg-indigo-50 dark:bg-indigo-900/20">
                                <td colspan="2" class="p-3 text-right font-bold text-gray-900 dark:text-white">{{ __('Estimated Profit Margin:') }}</td>
                                <td class="p-3 text-right font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($breakdownItinerary->total_price - $totalCost, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

