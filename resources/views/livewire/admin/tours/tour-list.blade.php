<div class="space-y-4 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tours') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $tours->total() }} total tours</p>
        </div>
        <div class="flex items-center gap-3">
            @can('create product')
                <button wire:click="create" class="btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />{{ __('Add Tour') }}</button>
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
                    <option value="100">100 / {{ __('page') }}</option>
                </select>

                <select wire:model.live="category" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="tourType" class="form-input text-xs py-1.5 px-2 w-36">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($tourTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class=" sm:w-90 relative">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name or slug...') }}" class="form-input text-xs py-1.5 !pl-8 w-full">
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
                        <th class="w-16">{{ __('Image') }}</th>
                        <th>{{ __('Tour Details') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Tags') }}</th>
                        <th>{{ __('Duration') }}</th>
                        <th>{{ __('Guests') }}</th>
                        <th class="text-right">
                            <div class="flex justify-end">{{ __('Actions') }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                        <tr wire:key="tour-{{ $tour->id }}"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-all duration-200 animate-fade-in">
                            <td>
                                <div
                                    class="w-10 h-10 rounded-[3px] overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200/60 dark:border-gray-700/60 shadow-sm relative group cursor-zoom-in">
                                    @if($tour->featured_image)
                                        <img src="{{ asset('storage/' . $tour->featured_image) }}" alt=""
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    @else
                                        <x-icon name="photo" class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-bold text-gray-900 dark:text-gray-150 leading-tight">{{ $tour->name }}</span>
                                        @if($tour->difficulty)<span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50">
                                                {{ $tour->difficulty }}
                                            </span>
                                        @endif
                                    </div>
                                    <div
                                        class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-450">
                                        <span>Slug: {{ $tour->slug }}</span>
                                    </div>
                                    @if(!empty($tour->seasonal_months))
                                        <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                            @foreach($tour->seasonal_months as $month)
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                                                    {{ substr($month, 0, 3) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php $displayType = $tour->tourType->name ?? $tour->type ?? '-'; @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $displayType === 'Safari' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50' : 'bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 border border-orange-100 dark:border-orange-900/50' }}">
                                    {{ $displayType }}
                                </span>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @if($tour->category)<span
                                            class="text-xs px-2.5 py-0.5 rounded-full font-medium bg-gray-100 dark:bg-gray-800 text-gray-650 dark:text-gray-350 border border-gray-200/50 dark:border-gray-700/50">
                                            {{ $tour->category->name }}
                                        </span>
                                    @else
                                        <span
                                            class="text-xs px-2 py-0.5 rounded bg-gray-105 dark:bg-gray-800 text-gray-400 dark:text-gray-600 italic">{{ __('Uncategorized') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($tour->tags as $t)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">
                                            {{ $t->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-450 dark:text-gray-500 italic">{{ __('None') }}</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-gray-800 dark:text-gray-250">
                                        <x-icon name="clock" class="w-3.5 h-3.5 text-gray-400" />
                                        @if($tour->duration_days)
                                            {{ $tour->duration_days }} {{ Str::plural('Day', $tour->duration_days) }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-gray-450 dark:text-gray-500 pl-5">
                                        @if($tour->duration_nights)
                                            {{ $tour->duration_nights }} {{ Str::plural('Night', $tour->duration_nights) }}
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1 text-xs font-medium">
                                    <span
                                        class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/20">
                                        Min: {{ $tour->min_guests }}
                                    </span>
                                    @if($tour->max_guests)
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100/50 dark:border-blue-900/20">
                                            Max: {{ $tour->max_guests }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <x-admin.action-dropdown>
                                    @can('edit product')
                                        <button wire:click="edit({{ $tour->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                                            title="{{ __('Edit') }}">
                                            <x-icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <a href="{{ route('admin.tours.builder', $tour->id) }}"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors"
                                            title="{{ __('Itinerary Builder') }}" wire:navigate>
                                            <x-icon name="calendar-days" class="w-4 h-4" />
                                        </a>
                                    @endcan
                                    @can('create product')
                                        <button wire:click="duplicate({{ $tour->id }})"
                                            class="p-2 rounded-[3px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                            title="{{ __('Duplicate') }}">
                                            <x-icon name="document-duplicate" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                    @can('delete product')
                                        <button wire:click="confirmDelete({{ $tour->id }})"
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
                            <td colspan="8" class="py-16 text-center">
                                <x-icon name="map" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3" />
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No tours found') }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $tours->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-backdrop bg-black/60 backdrop-blur-md flex items-center justify-center p-4" x-data
            @keydown.escape.window="$wire.set('showModal', false)">
            <div
                class="modal-box max-w-4xl max-h-[90vh] flex flex-col overflow-hidden rounded-[3px] shadow-premium border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 transition-all duration-300 animate-fade-in-up">

                <!-- Modal Header -->
                <div
                    class="flex-shrink-0 flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-800/80 bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-[3px] bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                            <x-icon name="map" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white leading-none">
                                {{ $isEdit ? 'Edit Tour Settings' : 'Create New Tour' }}
                            </h3>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                                {{ __('Configure global pricing, categories, seasonal months, and description') }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('showModal', false)"
                        class="p-2 rounded-[3px] text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-850 transition-all">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex-1 flex flex-col overflow-hidden"
                    x-data="{ activeTab: 'overview', seasonalMonths: @entangle('seasonal_months'), selectedTags: @entangle('selectedTags') }">

                    <!-- Tabs Navigation -->
                    <div
                        class="px-6 py-3 border-b border-gray-100 dark:border-gray-800/80 bg-gray-50/40 dark:bg-gray-900/20">
                        <div
                            class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-950/60 p-1.5 rounded-[3px] w-fit overflow-x-auto max-w-full">
                            <button type="button" @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="information-circle" class="w-4 h-4" />{{ __('Overview') }}</button>
                            <button type="button" @click="activeTab = 'details'"
                                :class="activeTab === 'details' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="adjustments-horizontal"
                                    class="w-4 h-4" />{{ __('Adventure Details') }}</button>
                            <button type="button" @click="activeTab = 'features'"
                                :class="activeTab === 'features' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="check-circle" class="w-4 h-4" />{{ __('Include/Exclude') }}</button>
                            <button type="button" @click="activeTab = 'rates'"
                                :class="activeTab === 'rates' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="currency-dollar" class="w-4 h-4" />{{ __('Rates') }}</button>
                            <button type="button" @click="activeTab = 'media'"
                                :class="activeTab === 'media' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="photo" class="w-4 h-4" />{{ __('Media') }}</button>
                            <button type="button" @click="activeTab = 'itinerary'"
                                :class="activeTab === 'itinerary' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="calendar-days" class="w-4 h-4" />{{ __('Day-by-Day Itinerary') }}</button>
                            <button type="button" @click="activeTab = 'seo'"
                                :class="activeTab === 'seo' ? 'bg-white dark:bg-gray-805 text-indigo-600 dark:text-indigo-400 border border-gray-200/40 dark:border-gray-700/40 shadow-sm' : 'text-gray-505 hover:text-gray-900 dark:hover:text-gray-300 hover:bg-white/40 dark:hover:bg-gray-850/40'"
                                class="flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-[3px] transition-all duration-200 whitespace-nowrap">
                                <x-icon name="magnifying-glass" class="w-4 h-4" />{{ __('SEO') }}</button>
                        </div>
                    </div>

                    <!-- Scrollable Modal Body -->
                    <div class="p-6 overflow-y-auto flex-1 bg-white dark:bg-gray-900">

                        <!-- Tab: Overview -->
                        <div x-show="activeTab === 'overview'" class="space-y-6 animate-fade-in">
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-6">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="information-circle" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ __('Basic Information') }}</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">Tour
                                            Name *</label>
                                        <input type="text" wire:model.live="name" class="form-input"
                                            placeholder="{{ __('e.g. 5 Days Kilimanjaro Climb - Marangu Route') }}">
                                        @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider">Slug
                                                *</label>
                                            @if($isEdit)
                                                <label class="flex items-center gap-2 cursor-pointer group">
                                                    <input type="checkbox" wire:model.live="shouldUpdateSlug"
                                                        class="w-4 h-4 text-indigo-650 rounded border-gray-300 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-700">
                                                    <span
                                                        class="text-xs text-gray-400 dark:text-gray-500 group-hover:text-gray-600 transition-colors">{{ __('Update from name') }}</span>
                                                </label>
                                            @endif
                                        </div>
                                        <input type="text" wire:model.live="slug" class="form-input"
                                            placeholder="{{ __('e.g. 5-days-kilimanjaro-climb-marangu-route') }}">
                                        @error('slug')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div
                                        class="md:col-span-2 border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 bg-gray-50/20 dark:bg-gray-950/20">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">


                                            <!-- Tour Type -->
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                                                    Tour Type *
                                                </label>
                                                <select wire:model="tour_type_id" class="form-input w-full">
                                                    <option value="">{{ __('Select Type') }}</option>
                                                    @foreach($tourTypes as $tType)
                                                        <option value="{{ $tType->id }}">{{ $tType->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('tour_type_id')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Tour Category -->
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Tour Category') }}</label>
                                                <select wire:model="tour_category_id" class="form-input w-full">
                                                    <option value="">{{ __('Select Category (Optional)') }}</option>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tour_category_id')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Tour Route -->
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Tour Route (Hiking)') }}</label>
                                                <select wire:model="tour_route_id" class="form-input w-full">
                                                    <option value="">{{ __('Select Route (Optional)') }}</option>
                                                    @foreach($tourRoutes as $route)
                                                        <option value="{{ $route->id }}">
                                                            {{ $route->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tour_route_id')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 space-y-2.5">
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider">{{ __('Tour Tags') }}</label>
                                        <div
                                            class="border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 bg-gray-50/20 dark:bg-gray-950/20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            @forelse($allTags as $tag)
                                                <label
                                                    class="flex items-center justify-between gap-3 cursor-pointer p-3 rounded-[3px] border transition-all duration-200 select-none"
                                                    :class="(selectedTags || []).some(t => t == {{ $tag->id }}) ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-900/50 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-605 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'">
                                                    <input type="checkbox" x-model="selectedTags" value="{{ $tag->id }}"
                                                        class="sr-only">
                                                    <span class="text-xs font-bold">{{ $tag->name }}</span>
                                                    <div class="w-4.5 h-4.5 rounded-full border flex items-center justify-center transition-all duration-200"
                                                        :class="(selectedTags || []).some(t => t == {{ $tag->id }}) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'border-gray-305 dark:border-gray-705 bg-white dark:bg-gray-900'">
                                                        <x-icon name="check" class="w-2.5 h-2.5 stroke-3"
                                                            x-show="(selectedTags || []).some(t => t == {{ $tag->id }})" />
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="col-span-full py-4 text-center text-xs text-gray-400 italic">
                                                    {{ __('No active tags available. Create tags first in Tour Tags CRUD.') }}
                                                </div>
                                            @endforelse
                                        </div>
                                        @error('selectedTags')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <x-admin.rich-text wire:model="description" label="{{ __('Description *') }}" />
                                        @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Details -->
                        <div x-show="activeTab === 'details'" class="space-y-6 animate-fade-in" style="display: none;">
                            <!-- Adventure Details Card -->
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="adjustments-horizontal" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ __('Adventure Details') }}</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Duration (Days)') }}</label>
                                        <input type="number" wire:model.live="duration_days" class="form-input"
                                            placeholder="e.g. 5">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Duration (Nights)') }}</label>
                                        <input type="number" wire:model="duration_nights" class="form-input"
                                            placeholder="e.g. 4">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Difficulty') }}</label>
                                        <select wire:model="difficulty" class="form-input">
                                            <option value="">{{ __('Select Difficulty') }}</option>
                                            <option value="Easy">{{ __('Easy') }}</option>
                                            <option value="Medium">{{ __('Medium') }}</option>
                                            <option value="Hard">{{ __('Hard') }}</option>
                                            <option value="Challenging">{{ __('Challenging') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-span-full space-y-2">
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider">{{ __('Seasonal Months') }}</label>
                                        <div
                                            class="bg-gray-50/20 dark:bg-gray-950/20 border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-4 flex flex-wrap gap-2.5">
                                            @foreach([
                                                    'January' => 'Jan',
                                                    'February' => 'Feb',
                                                    'March' => 'Mar',
                                                    'April' => 'Apr',
                                                    'May' => 'May',
                                                    'June' => 'Jun',
                                                    'July' => 'Jul',
                                                    'August' => 'Aug',
                                                    'September' => 'Sep',
                                                    'October' => 'Oct',
                                                    'November' => 'Nov',
                                                    'December' => 'Dec'
                                                ] as $fullName => $shortName)
                                                <label
                                                    class="flex items-center justify-center cursor-pointer px-4 py-2.5 rounded-[3px] border text-xs font-bold transition-all duration-200 select-none min-w-[72px]"
                                                    :class="(seasonalMonths || []).includes('{{ $fullName }}') ? 'bg-amber-50/70 dark:bg-amber-950/30 border-amber-305 dark:border-amber-900 text-amber-700 dark:text-amber-300 shadow-sm' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-605 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-850/50'">
                                                    <input type="checkbox" x-model="seasonalMonths" value="{{ $fullName }}"
                                                        class="sr-only">
                                                    <span>{{ $shortName }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Policy & Limits Card -->
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="check-circle" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ __('Policy & Limits') }}</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">Min
                                            Guests *</label>
                                        <input type="number" wire:model="min_guests" class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Max Guests') }}</label>
                                        <input type="number" wire:model="max_guests" class="form-input"
                                            placeholder="{{ __('No limit') }}">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">Min
                                            Age *</label>
                                        <input type="number" wire:model="min_age" class="form-input">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Max Age') }}</label>
                                        <input type="number" wire:model="max_age" class="form-input"
                                            placeholder="{{ __('No limit') }}">
                                    </div>

                                    <div
                                        class="col-span-full flex flex-wrap items-center justify-between gap-5 mt-2 p-4 bg-gray-50/20 dark:bg-gray-950/20 rounded-[3px] border border-gray-200/60 dark:border-gray-800">
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" wire:model="accepts_solo_travelers"
                                                class="text-indigo-600 rounded border-gray-305 dark:bg-gray-800 dark:border-gray-700 w-4 h-4">
                                            <span
                                                class="text-xs font-bold text-gray-705 dark:text-gray-300">{{ __('Accepts Solo Travelers') }}</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                            <input type="checkbox" wire:model="accepts_children"
                                                class="text-indigo-600 rounded border-gray-305 dark:bg-gray-800 dark:border-gray-700 w-4 h-4">
                                            <span
                                                class="text-xs font-bold text-gray-705 dark:text-gray-300">{{ __('Accepts Children') }}</span>
                                        </label>
                                        <div class="flex items-center gap-3">
                                            <label
                                                class="text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">Free
                                                Under Age *</label>
                                            <input type="number" wire:model="children_free_under_age"
                                                class="form-input w-20 text-center !py-1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Rates -->
                        <div x-show="activeTab === 'rates'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="currency-dollar" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Tour Rates') }}
                                    </h4>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">1
                                            Pax (Solo) Price</label>
                                        <input type="number" step="0.01" wire:model="adult_price" class="form-input"
                                            placeholder="e.g. 500.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">2
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_2_pax" class="form-input"
                                            placeholder="e.g. 450.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">3
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_3_pax" class="form-input"
                                            placeholder="e.g. 400.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">4
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_4_pax" class="form-input"
                                            placeholder="e.g. 380.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">5
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_5_pax" class="form-input"
                                            placeholder="e.g. 350.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">6
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_6_pax" class="form-input"
                                            placeholder="e.g. 330.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">7+
                                            Pax Price</label>
                                        <input type="number" step="0.01" wire:model="price_7_plus_pax" class="form-input"
                                            placeholder="e.g. 300.00">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 tracking-wider mb-2">{{ __('Child Price') }}</label>
                                        <input type="number" step="0.01" wire:model="child_price" class="form-input"
                                            placeholder="e.g. 250.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Features -->
                        <div x-show="activeTab === 'features'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Included Features Card -->
                                <div
                                    class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-4">
                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ __('Included Features') }}</h4>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="new_included_feature" class="form-input"
                                            placeholder="{{ __('e.g. Airport Transfer') }}"
                                            wire:keydown.enter.prevent="addIncludedFeature">
                                        <button type="button" wire:click="addIncludedFeature"
                                            class="btn-primary py-2 px-4 shadow-sm">{{ __('Add') }}</button>
                                    </div>
                                    <ul class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                        @foreach($included_features as $index => $feature)
                                            <li
                                                class="flex items-center justify-between text-xs p-3 bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800/60 rounded-[3px] transition-all hover:bg-gray-50/50 dark:hover:bg-gray-850/30">
                                                <span class="flex items-center font-semibold text-gray-700 dark:text-gray-300">
                                                    <x-icon name="check" class="w-4 h-4 text-emerald-500 mr-2 shrink-0" />
                                                    {{ $feature }}
                                                </span>
                                                <button type="button" wire:click="removeIncludedFeature({{ $index }})"
                                                    class="p-1 rounded-[3px] text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                                    <x-icon name="x-mark" class="w-4 h-4" />
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Excluded Features Card -->
                                <div
                                    class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-4">
                                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                            {{ __('Excluded Features') }}</h4>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="new_excluded_feature" class="form-input"
                                            placeholder="{{ __('e.g. Flight Tickets') }}"
                                            wire:keydown.enter.prevent="addExcludedFeature">
                                        <button type="button" wire:click="addExcludedFeature"
                                            class="btn-primary py-2 px-4 shadow-sm">{{ __('Add') }}</button>
                                    </div>
                                    <ul class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                        @foreach($excluded_features as $index => $feature)
                                            <li
                                                class="flex items-center justify-between text-xs p-3 bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800/60 rounded-[3px] transition-all hover:bg-gray-50/50 dark:hover:bg-gray-850/30">
                                                <span class="flex items-center font-semibold text-gray-700 dark:text-gray-300">
                                                    <x-icon name="x-mark" class="w-4 h-4 text-red-500 mr-2 shrink-0" />
                                                    {{ $feature }}
                                                </span>
                                                <button type="button" wire:click="removeExcludedFeature({{ $index }})"
                                                    class="p-1 rounded-[3px] text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">
                                                    <x-icon name="x-mark" class="w-4 h-4" />
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Media -->
                        <div x-show="activeTab === 'media'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-6">
                                <div class="flex items-center gap-2 pb-3.5 border-b border-gray-100 dark:border-gray-800">
                                    <x-icon name="photo" class="w-5 h-5 text-indigo-500" />
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ __('Media Assets') }}
                                    </h4>
                                </div>

                                <div class="space-y-6">
                                    <!-- Featured Image Zone -->
                                    <x-admin.image-upload :image="$featured_image" :existing="$existingImage"
                                        label="{{ __('Featured Image') }}" id="featured_image_input"
                                        model="featured_image" />

                                    <!-- Gallery Upload Zone -->
                                    <x-admin.gallery-upload :gallery="$gallery" :existing="$existingGallery"
                                        label="{{ __('Gallery Images') }}" id="tour_gallery_input" model="gallery" />
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Itinerary -->
                        <div x-show="activeTab === 'itinerary'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div
                                class="flex flex-col items-center justify-center p-12 text-center border border-gray-150 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-[3px]">
                                <div
                                    class="w-16 h-16 bg-indigo-50 dark:bg-indigo-950/40 rounded-full flex items-center justify-center mb-5 text-indigo-650 dark:text-indigo-400">
                                    <x-icon name="calendar-days" class="w-8 h-8" />
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                                    {{ __('Day-by-Day Itinerary Builder') }}</h3>
                                @if($isEdit)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mb-6 leading-relaxed">
                                        {{ __('Build a comprehensive day-to-day itinerary for this tour, including selecting daily destinations, accommodations, meals, and animals.') }}
                                    </p>
                                    <a href="{{ route('admin.tours.builder', $tour_id) }}"
                                        class="btn-primary py-2.5 px-5 shadow-md shadow-indigo-600/10">{{ __('Open Itinerary Builder') }}<x-icon
                                            name="arrow-right" class="w-4 h-4 ml-2" />
                                    </a>
                                @else
                                    <p class="text-xs text-gray-400 dark:text-gray-500 max-w-sm mb-6 leading-relaxed">
                                        {{ __('Please save the tour first before you can start building the day-by-day itinerary.') }}
                                    </p>
                                    <button type="submit" class="btn-primary py-2.5 px-5 shadow-md"
                                        @click="activeTab = 'overview'">{{ __('Save Tour & Continue') }}</button>
                                @endif
                            </div>
                        </div>

                        <!-- Tab: SEO -->
                        <div x-show="activeTab === 'seo'" class="space-y-6 animate-fade-in" style="display: none;">
                            <div
                                class="bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/80 rounded-[3px] p-5 md:p-6 space-y-5">
                                <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-800">
                                    <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                        {{ __('Search Engine Optimization') }}</h4>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Configure how this tour package appears in Google Search results and when shared on social media platforms like Facebook or Twitter.') }}
                                </p>

                                <div class="grid grid-cols-1 gap-5 mt-4">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Title') }}<span
                                                class="text-gray-400 font-normal normal-case">(Max 60 chars)</span></label>
                                        <input type="text" wire:model="seo_title" class="form-input"
                                            placeholder="{{ __('e.g. 5-Day Serengeti Safari -  ') }}" maxlength="60">
                                        <p class="mt-1.5 text-[11px] text-gray-400">
                                            {{ __('Leave blank to automatically use the tour name.') }}</p>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-705 dark:text-gray-300 uppercase tracking-wider mb-2">{{ __('Meta Description') }}<span
                                                class="text-gray-400 font-normal normal-case">(Max 160 chars)</span></label>
                                        <textarea wire:model="seo_description" rows="3"
                                            class="form-textarea w-full resize-none"
                                            placeholder="{{ __('A compelling summary of the tour that encourages people to click...') }}"
                                            maxlength="160"></textarea>
                                        <p class="mt-1.5 text-[11px] text-gray-400">
                                            {{ __('Leave blank to automatically use the first paragraph of the description.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex-shrink-0 flex items-center justify-end gap-3 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800/80 rounded-b-2xl">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn-secondary py-2 px-4 shadow-sm">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn-primary py-2 px-4 shadow-sm" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" wire:loading.remove />
                            <x-icon name="arrow-path" class="w-4 h-4 animate-spin" wire:loading />
                            <span wire:loading.remove>{{ $isEdit ? 'Update Tour' : 'Create Tour' }}</span>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Tour?') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ __('Are you sure you want to delete this tour? This action cannot be undone.') }}</p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="deleteTour" class="btn-danger">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>