<div class="flex flex-col lg:flex-row gap-8" id="destinations-listing">

    {{-- ═══════════════════════════════════════════════════════════
    LEFT SIDEBAR — Filters
    ════════════════════════════════════════════════════════════ --}}
    <aside class="lg:w-72 flex-shrink-0" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

        {{-- Mobile Filter Toggle --}}
        <div class="lg:hidden mb-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="w-full flex items-center justify-between bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-[3px] px-5 py-4 font-bold text-gray-800 dark:text-gray-200 shadow-sm hover:shadow-md transition-all">
                <span class="flex items-center gap-2">
                    <x-icon name="adjustments-horizontal"
                        class="w-5 h-5 text-brand-primary" />{{ __('Filter Destinations') }}</span>
                <span x-text="sidebarOpen ? '▲' : '▼'" class="text-xs text-gray-400 dark:text-gray-500"></span>
            </button>
        </div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-5">

            {{-- Search --}}
            <div
                class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h3
                    class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3 flex items-center gap-2">
                    <x-icon name="magnifying-glass"
                        class="w-3.5 h-3.5 text-brand-primary" />{{ __('Search Destinations') }}
                </h3>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="{{ __('e.g. Serengeti, Zanzibar...') }}"
                        class="w-full border border-gray-200 dark:border-gray-800 rounded-[3px] py-2.5 pl-9 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-transparent transition-all placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-550" />
                </div>
            </div>

            {{-- Country Filter --}}
            @if($countries->count() > 0)
                <div
                    class="bg-white dark:bg-gray-900 rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3
                            class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <x-icon name="map" class="w-3.5 h-3.5 text-brand-primary" />{{ __('Filter by Country') }}
                        </h3>
                    </div>

                    <div class="space-y-2">
                        @foreach($countries as $country)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative shrink-0 flex items-center">
                                    <input type="checkbox" wire:click="toggleCountry({{ $country->id }})"
                                        @checked(in_array($country->id, $selectedCountries))
                                        class="w-4 h-4 text-brand-primary dark:bg-gray-950 border-gray-300 dark:border-gray-800 focus:ring-brand-primary cursor-pointer rounded-[2px]">
                                </div>
                                <span
                                    class="!text-sm text-gray-700 dark:text-gray-300 group-hover:text-brand-primary transition-colors font-medium">
                                    {{ $country->name }}
                                </span>
                                <span
                                    class="ml-auto !text-xs text-gray-400 dark:text-gray-550 bg-gray-50 dark:bg-gray-950 px-2 py-0.5 rounded-full">
                                    {{ $country->destination_count }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reset Button --}}
            <button wire:click="clearFilters"
                class="w-full py-3 px-5 rounded-[3px] border-2 border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 font-bold text-sm hover:border-brand-primary hover:text-brand-primary transition-all duration-200 flex items-center justify-center gap-2">
                <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Reset All Filters') }}</button>
        </div>

    </aside>

    <div class="flex-1 min-w-0">

        {{-- Toolbar / Result Count --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-gray-550 dark:text-gray-400 text-sm">{{ __('Showing') }}<span
                        class="px-2 font-bold text-gray-900 dark:text-white">{{ $destinations->total() }}</span>
                    {{ __('destination')}}
                </p>
            </div>
        </div>

        {{-- Active filter tags --}}
        @if(!empty($selectedCountries) || !empty(trim($search)))
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach($countries->whereIn('id', $selectedCountries) as $activeCountry)
                    <span
                        class="inline-flex items-center gap-1.5 bg-brand-light text-brand-primary text-xs font-bold px-3 py-1.5 rounded-full border border-brand-primary/20">
                        <x-icon name="map" class="w-3 h-3" />
                        <span>{{ $activeCountry->name }}</span>
                        <button wire:click="toggleCountry({{ $activeCountry->id }})"
                            class="ml-1 hover:text-brand-primary-hover">✕</button>
                    </span>
                @endforeach
                @if(!empty(trim($search)))
                    <span
                        class="inline-flex items-center gap-1.5 bg-brand-light text-brand-primary text-xs font-bold px-3 py-1.5 rounded-full border border-brand-primary/20">
                        <x-icon name="magnifying-glass" class="w-3 h-3" />
                        <span>Search: "{{ $search }}"</span>
                        <button wire:click="$set('search', '')" class="ml-1 hover:text-brand-primary-hover">✕</button>
                    </span>
                @endif
            </div>
        @endif

        {{-- Loading Overlay --}}
        <div wire:loading.class.remove="hidden"
            class="hidden absolute inset-0 bg-white/40 rounded z-10 flex items-center justify-center">
            <div class="w-6 h-6 border-2 border-[#fbb158] border-t-transparent rounded-full animate-spin"></div>
        </div>

        @if($destinations->count() > 0)
            {{-- Card list --}}
            <div class="space-y-5" wire:loading.class="opacity-60 transition-opacity">
                @foreach($destinations as $destination)
                    <article
                        class="group bg-white dark:bg-gray-900 rounded-[6px] shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-xl hover:border-brand-primary/40 transition-all duration-300 overflow-hidden flex flex-col sm:flex-row items-stretch">


                        {{-- Feature Image (Left) --}}
                        <div class="relative shrink-0 w-full h-[220px] sm:w-[300px] sm:h-auto">

                            <a href="{{ route('frontend.destinations.show', $destination->slug) }}"
                                class="absolute inset-0 overflow-hidden">

                                @if(!empty($destination->featured_image))
                                    <img src="{{ Storage::url($destination->featured_image) }}" alt="{{ $destination->name }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
                                        loading="lazy">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-[#2E1F12] to-[#5a3e28] flex items-center justify-center">
                                        <svg class="w-12 h-12 text-[#fbb158]/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Country badge --}}
                                @if($destination->city?->country)
                                    <div class="absolute top-3 right-3">
                                        <span
                                            class="inline-block px-2.5 py-1 bg-white/92 backdrop-blur-sm text-[#2E1F12] text-[10px] font-bold tracking-[0.12em] uppercase rounded-[3px] shadow-sm">
                                            {{ $destination->city->country->name }}
                                        </span>
                                    </div>
                                @endif

                            </a>

                        </div>


                        {{-- Content (Right) --}}
                        <div class="flex flex-col justify-between p-6 sm:p-7 flex-1 min-w-0">

                            <div>
                                {{-- Tagline --}}
                                @if(!empty($destination->tagline))
                                    <p class="text-brand-primary text-[11px] font-semibold tracking-[0.15em] uppercase mb-2">
                                        {{ $destination->tagline }}
                                    </p>
                                @elseif($destination->city?->country)
                                    <p class="text-brand-primary text-[11px] font-semibold tracking-[0.15em] uppercase mb-2">
                                        {{ $destination->city->country->name }}
                                    </p>
                                @endif


                                {{-- Destination Name --}}
                                <h2 class="text-gray-900 dark:text-white text-xl md:text-[22px] font-bold leading-tight mb-3"
                                    style="font-family: 'Playfair Display', Georgia, serif;">
                                    {{ strtoupper($destination->name) }}
                                </h2>


                                {{-- Description --}}
                                @if(!empty($destination->description))
                                    <p class="text-gray-600 dark:text-gray-400 text-[14px] leading-[1.75] line-clamp-3">
                                        {{ Str::limit(strip_tags($destination->description), 220) }}
                                    </p>
                                @endif
                            </div>


                            {{-- CTA Button --}}
                            <div class="mt-5">
                                <a href="{{ route('frontend.destinations.show', $destination->slug) }}"
                                    class="inline-flex items-center gap-1.5 bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:text-brand-primary px-4 py-2.5 rounded-[3px] font-bold text-sm transition-all duration-300 group/btn">{{ __('View Destination') }}<x-icon
                                        name="arrow-right"
                                        class="w-3.5 h-3.5 transform group-hover/btn:translate-x-1 transition-transform" />
                                </a>
                            </div>

                        </div>

                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($destinations->hasPages())
                <div class="mt-8">
                    {{ $destinations->links('vendor.livewire.frontend-tailwind') }}
                </div>
            @endif

        @else
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-brand-light rounded-full flex items-center justify-center">
                    <x-icon name="map" class="w-12 h-12 text-brand-primary" />
                </div>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-3 font-brand">
                    {{ __('No destinations found') }}
                </h3>
                <p class="text-gray-400 dark:text-gray-500 mb-6">{{ __('Try adjusting your filters or search term to see
                                                                                        destinations.') }}</p>
                <button wire:click="clearFilters"
                    class="inline-flex items-center gap-2 bg-brand-primary hover:bg-brand-primary-hover text-white px-6 py-3 rounded-[3px] font-bold transition-all shadow-lg shadow-brand-primary/20">
                    <x-icon name="arrow-path" class="w-4 h-4" />{{ __('Clear All Filters') }}</button>
            </div>
        @endif

    </div>

</div>