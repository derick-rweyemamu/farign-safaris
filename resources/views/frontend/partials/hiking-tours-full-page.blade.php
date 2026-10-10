@php
    $currency_symbol = \App\Models\Currency::where('code', app(\App\Settings\GeneralSetting::class)->currency_default)->first()?->symbol ?? '$';
@endphp
{{-- MAIN CONTENT --}}
<div x-data="toursFilter()"
    class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if(!empty($customTitle) || !empty($customSubtitle))
            <div class="text-center max-w-3xl mx-auto mb-10 md:mb-12">
                @if(!empty($customTitle))
                    <h2 class="text-3xl md:text-5xl font-bold font-brand text-gray-900 dark:text-white mb-4 leading-tight">
                        {{ $customTitle }}
                    </h2>
                @endif
                @if(!empty($customSubtitle))
                    <p class="text-gray-600 dark:text-gray-300 text-base md:text-lg leading-relaxed">
                        {{ $customSubtitle }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- FILTER SIDEBAR --}}
            <aside class="lg:w-72 flex-shrink-0">

                {{-- Mobile Filter Toggle --}}
                <div class="lg:hidden mb-4">
                    <button x-on:click="sidebarOpen = !sidebarOpen"
                        class="w-full flex items-center justify-between bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-[3px] px-5 py-4 font-bold text-gray-800 dark:text-gray-200 shadow-sm hover:shadow-md transition-all">
                        <span class="flex items-center gap-2">
                            <x-icon name="adjustments-horizontal" class="w-5 h-5 text-brand-primary" />{{ __('Filter Tours') }}</span>
                        <span x-text="sidebarOpen ? '▲' : '▼'" class="text-xs text-gray-400 dark:text-gray-500"></span>
                    </button>
                </div>

                <div x-show="sidebarOpen || isDesktop" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">

                    {{-- Search --}}
                    <div
                        class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <x-icon name="magnifying-glass" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Search Tours') }}</h3>
                        <div class="relative">
                            <input id="tour-search" type="text" x-model="filters.search"
                                @input.debounce.350ms="applyFilters()" placeholder="{{ __('e.g. 4days Marangu route...') }}"
                                class="w-full border border-gray-200 dark:border-gray-800 rounded-[3px] py-2.5 pl-9 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-all placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
                            <x-icon name="magnifying-glass"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-550" />
                        </div>
                    </div>

                    {{-- Tour Category --}}
                    @if(count($routes) > 1)
                    <div
                        class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <x-icon name="map" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Climbing Routes') }}</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category" value="" x-model="filters.category"
                                    x-on:change="applyFilters()"
                                    class="w-4 h-4 text-brand-primary dark:bg-gray-950 border-gray-300 dark:border-gray-800 focus:ring-brand-primary cursor-pointer">
                                <span
                                    class="!text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-primary transition-colors font-medium">{{ __('All
                                    Routes') }}</span>
                            </label>
                            @foreach($routes as $route)
                                                    <label class="flex items-center gap-3 cursor-pointer group">
                                                        <input type="radio" name="category" value="{{ $route->slug }}" x-model="filters.category"
                                                            x-on:change="applyFilters()"
                                                            class="w-4 h-4 text-brand-primary dark:bg-gray-950 border-gray-300 dark:border-gray-800 focus:ring-brand-primary cursor-pointer">
                                                        <span
                                                            class="!text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-primary transition-colors font-medium">{{
                                $route->name }}</span>
                                                        <span
                                                            class="ml-auto !text-xs text-gray-400 dark:text-gray-550 bg-gray-50 dark:bg-gray-950 px-2 py-0.5 rounded-md">{{
                                $route->tours_count }}</span>
                                                    </label>
                            @endforeach
                        </div>
                    </div>
                    @endif



                    {{-- Advanced Filters Accordion --}}
                    <div x-data="{ showAdvanced: false }">
                        <button x-on:click="showAdvanced = !showAdvanced" type="button"
                            class="w-full flex items-center justify-between bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800/80 border border-gray-100 dark:border-gray-800 shadow-sm rounded-[3px] p-4 transition-colors">
                            <span class="flex items-center gap-2 text-gray-500 dark:text-gray-400"><x-icon name="adjustments-horizontal" class="w-4 h-4 text-brand-primary" />{{ __('More Filters') }}</span>
                            <x-icon name="chevron-down" class="w-4 h-4 transition-transform duration-200" x-bind:class="showAdvanced ? 'rotate-180' : ''" />
                        </button>
                        
                        <div x-show="showAdvanced" x-collapse x-cloak class="space-y-4 mt-4">
                            {{-- Duration Slider --}}
                            <div
                        class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2">
                            <x-icon name="clock" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Duration') }}</h3>
                        <!-- Value display -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="!text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('Min') }}</span>
                            <span
                                class="!text-sm text-brand-primary bg-brand-light border border-brand-primary/20 px-3 py-1 rounded-md">
                                <span x-text="filters.durationMin"></span> - <span
                                    x-text="filters.durationMax === 30 ? '30+' : filters.durationMax"></span>{{ __('days') }}</span>
                            <span class="!text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('Max') }}</span>
                        </div>

                        <!-- Dual range track -->
                        <div class="relative h-6 flex items-center">
                            <div class="relative w-full h-1.5 rounded-md bg-gray-200 dark:bg-gray-800">
                                <!-- Filled track -->
                                <div class="absolute h-full rounded-md bg-brand-primary"
                                    :style="'left:' + (filters.durationMin / 30 * 100) + '%; right:' + ((1 - filters.durationMax / 30) * 100) + '%'">
                                </div>
                            </div>
                            <!-- Min thumb -->
                            <input type="range" min="1" max="30" step="1" x-model.number="filters.durationMin"
                                x-on:input="if(filters.durationMin &gt; filters.durationMax) filters.durationMax = filters.durationMin; applyFilters()"
                                class="range-thumb absolute w-full">
                            <!-- Max thumb -->
                            <input type="range" min="1" max="30" step="1" x-model.number="filters.durationMax"
                                x-on:input="if(filters.durationMax &lt; filters.durationMin) filters.durationMin = filters.durationMax; applyFilters()"
                                class="range-thumb absolute w-full">
                        </div>
                        <div
                            class="flex justify-between text-[10px] text-gray-400 dark:text-gray-550 font-semibold mt-1">
                            <span class="!text-sm">{{ __('1 day') }}</span><span class="!text-sm">{{ __('30+ days') }}</span>
                        </div>
                    </div>

                    {{-- Price Range Slider --}}
                    <div
                        class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2">
                            <x-icon name="currency-dollar" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Price Range') }}</h3>
                        <!-- Value display -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="!text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('Min') }}</span>
                            <span
                                class="!text-sm text-brand-primary bg-brand-light border border-brand-primary/20 px-3 py-1 rounded-md">
                                {{ $currency_symbol }}<span x-text="Number(filters.priceMin).toLocaleString()"></span> -
                                {{ $currency_symbol }}<span
                                    x-text="filters.priceMax >= 20000 ? '20,000+' : Number(filters.priceMax).toLocaleString()"></span>
                            </span>
                            <span class="!text-xs font-semibold text-gray-500 dark:text-gray-400">{{ __('Max') }}</span>
                        </div>
                        <!-- Dual range track -->
                        <div class="relative h-6 flex items-center">
                            <div class="relative w-full h-1.5 rounded-md bg-gray-200 dark:bg-gray-800">
                                <div class="absolute h-full rounded-md bg-brand-primary"
                                    :style="'left:' + (filters.priceMin / 20000 * 100) + '%; right:' + ((1 - filters.priceMax / 20000) * 100) + '%'">
                                </div>
                            </div>
                            <!-- Min thumb -->
                            <input type="range" min="0" max="20000" step="100" x-model.number="filters.priceMin"
                                x-on:input="if(filters.priceMin &gt; filters.priceMax) filters.priceMax = filters.priceMin; applyFilters()"
                                class="range-thumb absolute w-full">
                            <!-- Max thumb -->
                            <input type="range" min="0" max="20000" step="100" x-model.number="filters.priceMax"
                                x-on:input="if(filters.priceMax &lt; filters.priceMin) filters.priceMin = filters.priceMax; applyFilters()"
                                class="range-thumb absolute w-full">
                        </div>
                        <div class="flex justify-between  text-gray-400 dark:text-gray-550 font-semibold mt-1">
                            <span class="!text-sm">{{ $currency_symbol }} {{ __('0') }} </span><span
                                class="!text-sm">{{ $currency_symbol }} {{ __('20,000+') }} </span>
                        </div>
                    </div>

                    {{-- Difficulty --}}
                    <div
                        class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                            <x-icon name="trending-up" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Difficulty') }}</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="difficulty" value="" x-model="filters.difficulty"
                                    x-on:change="applyFilters()"
                                    class="w-4 h-4 text-brand-primary dark:bg-gray-950 border-gray-300 dark:border-gray-800 focus:ring-brand-primary cursor-pointer">
                                <span
                                    class="!text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-primary transition-colors font-medium">{{ __('Any
                                    Level') }}</span>
                            </label>
                            @foreach(['Easy', 'Moderate', 'Challenging', 'Strenuous'] as $lvl)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="difficulty" value="{{ $lvl }}" x-model="filters.difficulty"
                                        x-on:change="applyFilters()"
                                        class="w-4 h-4 text-brand-primary dark:bg-gray-950 border-gray-300 dark:border-gray-800 focus:ring-brand-primary cursor-pointer">
                                    <span
                                        class="!text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-primary transition-colors font-medium flex items-center gap-2">
                                        <span
                                            class="w-2 h-2 rounded-md {{ $lvl === 'Easy' ? 'bg-emerald-400' : ($lvl === 'Moderate' ? 'bg-yellow-400' : ($lvl === 'Challenging' ? 'bg-orange-400' : 'bg-red-500')) }}"></span>
                                        {{ $lvl }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                        </div>
                    </div>

                    {{-- Reset Button --}}
                    <button x-on:click="resetFilters()"
                        class="w-full py-3 px-5 rounded-[3px] border-2 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 font-bold text-sm hover:border-brand-primary hover:text-brand-primary transition-all duration-200 flex items-center justify-center gap-2">
                        <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Reset All Filters') }}</button>
                </div>
            </aside>

            {{-- TOURS GRID --}}
            <main class="flex-1 min-w-0">

                {{-- Toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-gray-550 dark:text-gray-400 text-sm" id="results-count">{{ __('Showing') }}<span class="px-1 font-bold text-gray-900 dark:text-white" x-text="startRecord"></span>{{ __('to') }}<span class="px-1 font-bold text-gray-900 dark:text-white" x-text="endRecord"></span>{{ __('of') }}<span class="px-1 font-bold text-gray-900 dark:text-white" x-text="visibleCount"></span>{{ __('tours') }}</p>
                    </div>
                    <div class="flex items-center gap-3">   
                        {{-- Sort --}}
                        <select id="sort-select" x-model="filters.sort" x-on:change="applyFilters()"
                            class="border border-gray-200 dark:border-gray-800 rounded-[3px] py-2 px-3 text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-primary bg-white dark:bg-gray-900 shadow-sm">
                            <option value="featured">{{ __('Sort: Featured') }}</option>
                            <option value="price_asc">{{ __('Price: Low to High') }}</option>
                            <option value="price_desc">{{ __('Price: High to Low') }}</option>
                            <option value="duration_asc">{{ __('Duration: Shortest First') }}</option>
                            <option value="duration_desc">{{ __('Duration: Longest First') }}</option>
                            <option value="name_asc">{{ __('Name: A-Z') }}</option>
                        </select>
                        {{-- View toggle removed as requested --}}
                    </div>
                </div>

                {{-- Active Filter Chips --}}
                <div x-show="hasActiveFilters()" class="flex flex-wrap gap-2 mb-5">
                    <template x-if="filters.search">
                        <span
                            class="inline-flex items-center gap-1.5 bg-brand-light text-brand-primary text-xs font-bold px-3 py-1.5 rounded-md border border-brand-primary/20">
                            <x-icon name="magnifying-glass" class="w-3 h-3" />
                            <span x-text="'Search: ' + filters.search"></span>
                            <button x-on:click="filters.search=''; applyFilters()"
                                class="ml-1 hover:text-brand-primary-hover">✕</button>
                        </span>
                    </template>
                    <template x-if="filters.route">
                        <span
                            class="inline-flex items-center gap-1.5 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 text-xs font-bold px-3 py-1.5 rounded-md border border-blue-200 dark:border-blue-800">{{ __('Route:') }}<span x-text="filters.route"></span>
                            <button x-on:click="filters.route=''; applyFilters()"
                                class="ml-1 hover:text-blue-600 dark:hover:text-blue-400">✕</button>
                        </span>
                    </template>
                    <template x-if="filters.durationMin &gt; 1 || filters.durationMax &lt; 30">
                        <span
                            class="inline-flex items-center gap-1.5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-xs font-bold px-3 py-1.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                            <span
                                x-text="filters.durationMin + '-' + (filters.durationMax &gt;= 30 ? '30+' : filters.durationMax) + ' days'"></span>
                            <button x-on:click="filters.durationMin=1; filters.durationMax=30; applyFilters()"
                                class="ml-1 hover:text-emerald-600 dark:hover:text-emerald-400">✕</button>
                        </span>
                    </template>
                    <template x-if="filters.difficulty">
                        <span
                            class="inline-flex items-center gap-1.5 bg-orange-100 dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 text-xs font-bold px-3 py-1.5 rounded-md border border-orange-200 dark:border-gray-800">
                            <span x-text="filters.difficulty"></span>
                            <button x-on:click="filters.difficulty=''; applyFilters()"
                                class="ml-1 hover:text-orange-600 dark:hover:text-orange-400">✕</button>
                        </span>
                    </template>

                </div>

                {{-- Loading Spinner --}}
                <div x-show="loading" class="flex items-center justify-center py-20">
                    <div class="flex flex-col items-center gap-4">
                        <x-icon name="diamond" type="solid" class="w-12 h-12 text-brand-primary animate-spin" />
                        <p class="text-gray-550 dark:text-gray-400 text-sm font-medium">{{ __('Finding the best tours for
                            you...') }}</p>
                    </div>
                </div>

                {{-- Grid View --}}
                <div x-show="!loading" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" id="tours-grid"
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($tours as $tour)
                                    <div class="tour-card group bg-white dark:bg-gray-900 rounded-[3px] shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full"
                                        data-name="{{ strtolower($tour->name) }}"
                                        data-description="{{ strtolower($tour->description ?? '') }}"
                                        data-route="{{ $tour->tourRoute->slug ?? '' }}" data-duration="{{ $tour->duration_days }}"
                                        data-difficulty="{{ $tour->difficulty ?? '' }}" data-price="{{ $tour->adult_price }}">

                                        {{-- Image --}}
                                        <div class="relative h-56 overflow-hidden">
                                            <a href="{{ route('tours.show', $tour->slug) }}" class="block w-full h-full">
                                                @if($tour->featured_image)
                                                    <img src="{{ Storage::url($tour->featured_image) }}" alt="{{ $tour->name }}"
                                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                                @elseif($companySetting?->tour_fallback_image)
                                                    <img src="{{ Storage::url($companySetting->tour_fallback_image) }}"
                                                        alt="{{ $tour->name }}"
                                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                                        <x-icon name="photograph" class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                                                    </div>
                                                @endif
                                            </a>





                                            {{-- Duration overlay --}}
                                            <div class="absolute bottom-3 left-3 z-10">
                                                <span
                                                    class="flex items-center gap-1.5 bg-gray-950/75 dark:bg-gray-900/75 backdrop-blur-sm text-white !text-xs  px-3 py-1.5 rounded-md">
                                                    <x-icon name="clock" class="w-3.5 h-3.5 text-brand-primary" />
                                                    {{ $tour->duration_days }} Days
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Card body --}}
                                        <div class="p-5 flex flex-col flex-grow">
                                            <a href="{{ route('tours.show', $tour->slug) }}" class="block mb-2">
                                                <h3
                                                    class="text-md font-bold text-gray-900 dark:text-white text-brand-secondary leading-snug group-hover:text-brand-primary transition-colors line-clamp-2">
                                                    {{ $tour->name }}
                                                </h3>
                                            </a>
                                            @if($tour->description)<p class="text-gray-550 dark:text-gray-400 text-sm line-clamp-2 mb-4 flex-grow">
                                                    {{ Str::limit(strip_tags($tour->description), 100) }}
                                                </p>
                                            @endif

                                            {{-- Price (Single Line) --}}
                                            <div class="flex items-baseline gap-1 text-gray-900 dark:text-white mb-4">
                                                <span class="!text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">{{ __('From') }}</span>
                                                <span class="text-brand-primary font-bold !text-xs leading-none">{{ $currency_symbol }}</span>
                                                <span class="text-lg font-bold text-gray-900 dark:text-white font-brand leading-none">{{ number_format($tour->adult_price, 0) }}</span>
                                                <span class="!text-xs text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider">{{ __('per person') }}</span>
                                            </div>

                                            {{-- Max People + CTA --}}
                                            <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                                @if($tour->max_guests)
                                                    <span class="flex items-center gap-1.5 text-sm font-medium text-gray-600 dark:text-gray-300">
                                                        <x-icon name="users" class="w-4 h-4 text-brand-primary" />
                                                        <span>{{ __('Max') }} {{ $tour->max_guests }}</span>
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                                <a href="{{ route('tours.show', $tour->slug) }}"
                                                    class="inline-flex items-center gap-1.5 bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:text-brand-primary px-4 py-2.5 rounded-[3px] font-bold text-sm transition-all duration-300 group/btn">{{ __('View Tour') }}<x-icon name="arrow-right"
                                                        class="w-3.5 h-3.5 transform group-hover/btn:translate-x-1 transition-transform" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div x-show="!loading && totalPages > 1" class="mt-12 pt-8 border-t border-gray-150 dark:border-gray-800">
                    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        {{-- Page status info (very small, no dot) --}}
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 order-2 sm:order-1">
                            <span>{{ __('Page') }} <strong class="text-gray-900 dark:text-white" x-text="currentPage"></strong> {{ __('of') }} <strong class="text-gray-900 dark:text-white" x-text="totalPages"></strong></span>
                        </div>

                        {{-- Pagination Controls --}}
                        <div class="flex items-center gap-1.5 order-1 sm:order-2">
                            {{-- Previous Page Button --}}
                            <button type="button"
                                x-on:click="if(currentPage > 1) { currentPage--; applyFilters(false); window.scrollTo({top: document.getElementById('tours-grid').offsetTop - 100, behavior: 'smooth'}); }"
                                :disabled="currentPage === 1"
                                :class="{'opacity-40 cursor-not-allowed bg-gray-50 dark:bg-gray-800/40 text-gray-400 border-gray-200 dark:border-gray-800': currentPage === 1, 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-brand-primary hover:border-brand-primary hover:text-white dark:hover:text-white shadow-2xs': currentPage > 1}"
                                class="group inline-flex items-center gap-1 px-2.5 h-7 rounded-[3px] border font-bold text-[10px] uppercase tracking-wider transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                aria-label="{{ __('Previous') }}">
                                <x-icon name="arrow-left" class="w-3 h-3 transform group-hover:-translate-x-0.5 transition-transform" />
                                <span class="hidden xs:inline">{{ __('Prev') }}</span>
                            </button>

                            {{-- Numbers Segmented Capsule --}}
                            <div class="flex items-center gap-1 p-0.5 bg-gray-50 dark:bg-gray-900/70 rounded-[3px] border border-gray-200/80 dark:border-gray-800">
                                <template x-for="page in totalPages" :key="page">
                                    <div>
                                        {{-- Current Page --}}
                                        <span x-show="currentPage === page"
                                            class="min-w-[28px] h-6 px-1.5 flex items-center justify-center text-[10px] font-black tracking-wider text-white bg-brand-primary rounded-[2px] shadow-2xs ring-1 ring-brand-primary/25 cursor-default select-none"
                                            x-text="page < 10 ? '0' + page : page">
                                        </span>

                                        {{-- Inactive Page --}}
                                        <button x-show="currentPage !== page" type="button"
                                            x-on:click="currentPage = page; applyFilters(false); window.scrollTo({top: document.getElementById('tours-grid').offsetTop - 100, behavior: 'smooth'});"
                                            class="min-w-[28px] h-6 px-1.5 flex items-center justify-center text-[10px] font-bold tracking-wider text-gray-600 dark:text-gray-300 bg-transparent hover:bg-white dark:hover:bg-gray-800 hover:text-brand-primary dark:hover:text-brand-primary rounded-[2px] transition-all duration-200 focus:outline-none"
                                            x-text="page < 10 ? '0' + page : page">
                                        </button>
                                    </div>
                                </template>
                            </div>

                            {{-- Next Page Button --}}
                            <button type="button"
                                x-on:click="if(currentPage < totalPages) { currentPage++; applyFilters(false); window.scrollTo({top: document.getElementById('tours-grid').offsetTop - 100, behavior: 'smooth'}); }"
                                :disabled="currentPage === totalPages"
                                :class="{'opacity-40 cursor-not-allowed bg-gray-50 dark:bg-gray-800/40 text-gray-400 border-gray-200 dark:border-gray-800': currentPage === totalPages, 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-brand-primary hover:border-brand-primary hover:text-white dark:hover:text-white shadow-2xs': currentPage < totalPages}"
                                class="group inline-flex items-center gap-1 px-2.5 h-7 rounded-[3px] border font-bold text-[10px] uppercase tracking-wider transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                aria-label="{{ __('Next') }}">
                                <span class="hidden xs:inline">{{ __('Next') }}</span>
                                <x-icon name="arrow-right" class="w-3 h-3 transform group-hover:translate-x-0.5 transition-transform" />
                            </button>

                            {{-- Optional Go To Page Input when there are > 3 pages --}}
                            <div x-show="totalPages > 3" class="hidden md:flex items-center gap-1.5 pl-2.5 border-l border-gray-200 dark:border-gray-800">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('Go to') }}</span>
                                <div class="relative flex items-center">
                                    <input type="number" min="1" :max="totalPages" x-data="{ pageVal: currentPage }"
                                        x-effect="pageVal = currentPage" x-model="pageVal"
                                        x-on:keydown.enter="if(pageVal >= 1 && pageVal <= totalPages) { currentPage = parseInt(pageVal); applyFilters(false); window.scrollTo({top: document.getElementById('tours-grid').offsetTop - 100, behavior: 'smooth'}); }"
                                        class="w-12 h-6 pl-1.5 pr-5 text-[10px] font-bold text-center text-gray-900 dark:text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-[2px] shadow-2xs focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                        placeholder="#" />
                                    <button type="button"
                                        x-on:click="let inputPage = parseInt($el.previousElementSibling.value); if(inputPage >= 1 && inputPage <= totalPages) { currentPage = inputPage; applyFilters(false); window.scrollTo({top: document.getElementById('tours-grid').offsetTop - 100, behavior: 'smooth'}); }"
                                        class="absolute right-0.5 w-4 h-4 flex items-center justify-center text-gray-400 hover:text-brand-primary dark:hover:text-brand-primary rounded transition-colors"
                                        title="{{ __('Go') }}">
                                        <x-icon name="arrow-right" class="w-2.5 h-2.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>



                {{-- No Results State --}}
                <div x-show="!loading && visibleCount === 0" class="py-24 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 bg-brand-light rounded-md flex items-center justify-center">
                        <x-icon name="face-smile" class="w-12 h-12 text-brand-primary" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-3 font-brand">{{ __('No tours match your
                        filters') }}</h3>
                    <p class="text-gray-400 dark:text-gray-500 mb-6">{{ __('Try adjusting the filters or reset them to see all
                        available tours.') }}</p>
                    <button x-on:click="resetFilters()"
                        class="inline-flex items-center gap-2 bg-brand-primary hover:bg-brand-primary-hover text-white px-6 py-3 rounded-[3px] font-bold transition-all shadow-lg shadow-brand-primary/20">
                        <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Clear All Filters') }}</button>
                </div>

            </main>
        </div>
    </div>
</div>



{{-- Slider CSS --}}
<!-- Styles moved to app.css for easier management -->

{{-- Alpine.js Filter Logic --}}
<script>
    function toursFilter() {
        return {
            sidebarOpen: false,
            loading: false,
            visibleCount: {{ $tours->count() }},
            currentPage: 1,
            perPage: {{ $limit ?? 12 }},
            get totalPages() {
                return Math.ceil(this.visibleCount / this.perPage) || 1;
            },
            get startRecord() {
                return this.visibleCount === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
            },
            get endRecord() {
                return Math.min(this.currentPage * this.perPage, this.visibleCount);
            },
            filters: {
                search: '',
                route: '{{ request("route", "") }}',
                durationMin: 1,
                durationMax: 30,
                difficulty: '',
                priceMin: 0,
                priceMax: 20000,
                sort: 'featured',
            },

            get isDesktop() {
                return window.innerWidth >= 1024;
            },

            init() {
                this.sidebarOpen = this.isDesktop;
                window.addEventListener('resize', () => {
                    this.sidebarOpen = this.isDesktop;
                });
                this.applyFilters(false);
            },

            hasActiveFilters() {
                return this.filters.search || this.filters.route
                    || this.filters.durationMin > 1 || this.filters.durationMax < 30
                    || this.filters.difficulty
                    || this.filters.priceMin > 0 || this.filters.priceMax < 20000;
            },

            applyFilters(resetPage = true) {
                if (resetPage) this.currentPage = 1;
                this.loading = true;
                setTimeout(() => {
                    const container = document.getElementById('tours-grid');
                    if (!container) return;

                    let cards = Array.from(container.querySelectorAll('.tour-card'));
                    const f = this.filters;

                    // Sort cards first
                    cards = this.sortCards(cards);

                    // Append sorted cards back to DOM so they are physically ordered
                    cards.forEach(card => container.appendChild(card));

                    let visible = 0;

                    cards.forEach(card => {
                        const name = card.dataset.name || '';
                        const desc = card.dataset.description || '';
                        const route = card.dataset.route || '';
                        const duration = parseInt(card.dataset.duration) || 0;
                        const difficulty = card.dataset.difficulty || '';
                        const price = parseFloat(card.dataset.price) || 0;

                        let show = true;

                        if (f.search) {
                            const q = f.search.toLowerCase();
                            if (!name.includes(q) && !desc.includes(q)) show = false;
                        }
                        if (f.route && route !== f.route) show = false;
                        const dMax = f.durationMax >= 30 ? 9999 : f.durationMax;
                        if (duration < f.durationMin || duration > dMax) show = false;
                        if (f.difficulty && difficulty.toLowerCase() !== f.difficulty.toLowerCase()) show = false;
                        const pMax = f.priceMax >= 20000 ? Infinity : f.priceMax;
                        if (price < f.priceMin || price > pMax) show = false;

                        if (show) {
                            if (visible >= (this.currentPage - 1) * this.perPage && visible < this.currentPage * this.perPage) {
                                card.style.display = '';
                            } else {
                                card.style.display = 'none';
                            }
                            visible++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    this.visibleCount = visible;

                    // Correct current page if it's out of bounds after filtering
                    if (this.currentPage > this.totalPages && this.totalPages > 0) {
                        this.currentPage = this.totalPages;
                        this.applyFilters(false);
                        return;
                    }

                    this.loading = false;
                }, 200);
            },

            sortCards(cards) {
                const sortBy = this.filters.sort;
                cards.sort((a, b) => {
                    const priceA = parseFloat(a.dataset.price) || 0;
                    const priceB = parseFloat(b.dataset.price) || 0;
                    const durA = parseInt(a.dataset.duration) || 0;
                    const durB = parseInt(b.dataset.duration) || 0;
                    const nameA = a.dataset.name || '';
                    const nameB = b.dataset.name || '';

                    switch (sortBy) {
                        case 'price_asc': return priceA - priceB;
                        case 'price_desc': return priceB - priceA;
                        case 'duration_asc': return durA - durB;
                        case 'duration_desc': return durB - durA;
                        case 'name_asc': return nameA.localeCompare(nameB);
                        default: return 0;
                    }
                });
                return cards;
            },

            updateCount() {
                this.visibleCount = document.querySelectorAll('.tour-card').length;
            },

            resetFilters() {
                this.filters = {
                    search: '',
                    route: '',
                    durationMin: 1,
                    durationMax: 30,
                    difficulty: '',
                    priceMin: 0,
                    priceMax: 20000,
                    soloFriendly: false,
                    sort: 'featured',
                };
                this.applyFilters();
            },
        };
    }
</script>