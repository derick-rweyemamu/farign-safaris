@extends('layouts.frontend')

@section('title', $tour->name . ' | ' . ($companySetting?->company_name ?? config('app.name')))

@section('content')

    <div x-data="{ 
        activeTab: 'overview',
        isSticky: false,
        checkSticky() {
            const overlay = document.getElementById('tour-tab-overlay');
            if (overlay) {
                const rect = overlay.getBoundingClientRect();
                this.isSticky = rect.top <= 0;
            }
        }
    }" x-init="checkSticky()" @scroll.window="checkSticky()" class="flex flex-col">

        {{-- ── 1. TOUR HERO HEADER BANNER ──────────────────────────────────────── --}}
        <div id="tour-hero"
            class="relative w-full min-h-[580px] md:min-h-[640px] bg-gray-900 bg-cover bg-center flex flex-col justify-between"
            style="background-image: url('{{ $tour->featured_image ? Storage::url($tour->featured_image) : asset('images/default-tour.jpg') }}')">

            {{-- Dark overlays for deep cinematic contrast --}}
            <div class="absolute inset-0 bg-gray-950/45"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950/70 to-transparent"></div>

            {{-- 1. Breadcrumbs at Top of the Header --}}
            <div class="relative z-20 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                <nav
                    class="flex items-center gap-2 !text-xs text-gray-300 font-medium bg-black/40 w-fit px-4 py-2 rounded-sm backdrop-blur-sm border border-white/10">
                    <a href="{{ route('home') }}" class="!text-xs hover:text-white transition-colors">{{ __('Home') }}</a>
                    <span class="!text-xs opacity-50">›</span>
                    <a href="{{ route('tours.index') }}"
                        class="!text-xs hover:text-white transition-colors">{{ __('Tours') }}</a>
                    <span class="!text-xs opacity-50">›</span>
                    <span class="!text-xs text-gray-400">{{ $tour->name }}</span>
                </nav>
            </div>

            {{-- 2. Title & Info in the MIDDLE of the Header (Vertically Centered) --}}
            <div class="relative z-10 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 my-auto text-white">
                <div class="max-w-4xl space-y-4">
                    {{-- Category Badge --}}
                    <div
                        class="inline-flex items-center px-3 py-1 rounded-[3px] bg-brand-primary/20 text-brand-primary border border-brand-primary/30 uppercase tracking-widest text-xs font-bold">
                        {{ $tour->category->name ?? 'Safari Adventure' }}
                    </div>

                    {{-- Page Title Stay in the Middle --}}
                    <h1
                        class="text-2xl sm:text-2xl md:text-3xl lg:text-[38px] font-bold font-brand leading-tight tracking-wide text-white">
                        {{ $tour->name }}
                    </h1>

                    {{-- Meta Badges --}}
                    <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-gray-200 pt-2">
                        <span
                            class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm border border-white/10">
                            <x-icon name="clock" class="w-4 h-4 mr-2 text-brand-primary" />
                            {{ $tour->duration_days }} Days / {{ $tour->duration_nights }} Nights
                        </span>
                        @if($tour->destinations && $tour->destinations->count())
                            <span
                                class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm border border-white/10">
                                <x-icon name="map-pin" class="w-4 h-4 mr-2 text-brand-primary" />
                                {{ $tour->destinations->count() }}
                                Destination{{ $tour->destinations->count() !== 1 ? 's' : '' }}
                            </span>
                        @endif
                        @if($tour->difficulty)
                            <span
                                class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm border border-white/10">
                                <x-icon name="trending-up" class="w-4 h-4 mr-2 text-brand-primary" />
                                {{ $tour->difficulty }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. THE TAB BAR ON TOP OF THE FEATURED IMAGE (OVERLAID AT BOTTOM, NOT FULL WIDTH) --}}
            <div id="tour-tab-overlay" class="relative z-20 w-full transition-opacity duration-150"
                :class="isSticky ? 'opacity-0 pointer-events-none' : 'opacity-100'">
                <div class="max-w-7xl lg:max-w-7xl mx-auto px-4 sm:px-6">
                    <div
                        class="bg-black/55 backdrop-blur-md border border-white/20 rounded-[3px] shadow-2xl overflow-hidden">
                        <nav class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-white/20">

                            {{-- Tab 1: Overview --}}
                            <button type="button" @click="activeTab = 'overview'"
                                class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                                :class="activeTab === 'overview' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                                <span
                                    class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                                    :class="activeTab === 'overview' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                                    {{ __('OVERVIEW') }}
                                </span>
                                <span
                                    class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                                    {{ __('Journey Summary') }}
                                </span>
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                                    :class="activeTab === 'overview' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                            </button>

                            {{-- Tab 2: Day by Day Itinerary --}}
                            <button type="button" @click="activeTab = 'itinerary'"
                                class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                                :class="activeTab === 'itinerary' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                                <span
                                    class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                                    :class="activeTab === 'itinerary' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                                    {{ __('ITINERARY') }}
                                </span>
                                <span
                                    class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                                    {{ __('Day by Day Experience') }}
                                </span>
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                                    :class="activeTab === 'itinerary' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                            </button>

                            {{-- Tab 3: Rates --}}
                            <button type="button" @click="activeTab = 'rates'"
                                class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                                :class="activeTab === 'rates' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                                <span
                                    class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                                    :class="activeTab === 'rates' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                                    {{ __('RATES') }}
                                </span>
                                <span
                                    class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                                    {{ __('Pricing & Group Costs') }}
                                </span>
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                                    :class="activeTab === 'rates' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                            </button>

                            {{-- Tab 4: Included / Excluded --}}
                            <button type="button" @click="activeTab = 'details'"
                                class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                                :class="activeTab === 'details' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                                <span
                                    class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                                    :class="activeTab === 'details' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                                    {{ __('INCLUSIONS') }}
                                </span>
                                <span
                                    class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                                    {{ __('What’s Included & Excluded') }}
                                </span>
                                <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                                    :class="activeTab === 'details' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                            </button>

                        </nav>
                    </div>
                </div>
            </div>

        </div><!-- /hero -->


        {{-- ── 2. STICKY TAB BAR ON SCROLL (FLUSH AT TOP: 0, ZERO GAP, FULL-WIDTH BACKGROUND) ── --}}
        <div x-cloak x-show="isSticky"
            class="fixed top-0 left-0 right-0 z-50 w-full bg-[#162016]/98 backdrop-blur-md border-b border-[#293a29] shadow-2xl">
            <div class="max-w-5xl lg:max-w-6xl mx-auto px-4 sm:px-6">
                <nav class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-white/20">

                    {{-- Tab 1: Overview --}}
                    <button type="button" @click="activeTab = 'overview'"
                        class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                        :class="activeTab === 'overview' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                        <span
                            class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                            :class="activeTab === 'overview' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                            {{ __('OVERVIEW') }}
                        </span>
                        <span class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                            {{ __('Journey Summary') }}
                        </span>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                            :class="activeTab === 'overview' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                    </button>

                    {{-- Tab 2: Day by Day Itinerary --}}
                    <button type="button" @click="activeTab = 'itinerary'"
                        class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                        :class="activeTab === 'itinerary' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                        <span
                            class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                            :class="activeTab === 'itinerary' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                            {{ __('ITINERARY') }}
                        </span>
                        <span class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                            {{ __('Day by Day Experience') }}
                        </span>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                            :class="activeTab === 'itinerary' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                    </button>

                    {{-- Tab 3: Rates --}}
                    <button type="button" @click="activeTab = 'rates'"
                        class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                        :class="activeTab === 'rates' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                        <span
                            class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                            :class="activeTab === 'rates' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                            {{ __('RATES') }}
                        </span>
                        <span class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                            {{ __('Pricing & Group Costs') }}
                        </span>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                            :class="activeTab === 'rates' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                    </button>

                    {{-- Tab 4: Included / Excluded --}}
                    <button type="button" @click="activeTab = 'details'"
                        class="flex flex-col items-center justify-center py-3.5 px-4 text-center transition-all duration-200 relative group cursor-pointer"
                        :class="activeTab === 'details' ? 'bg-white/15' : 'bg-transparent hover:bg-white/10'">
                        <span
                            class="text-[10px] md:text-[11px] uppercase tracking-[0.22em] font-bold block mb-1 transition-colors"
                            :class="activeTab === 'details' ? 'text-[#CA9A2E]' : 'text-gray-300 group-hover:text-white'">
                            {{ __('INCLUSIONS') }}
                        </span>
                        <span class="text-xs md:text-sm font-semibold text-white tracking-wide block truncate w-full">
                            {{ __('What’s Included & Excluded') }}
                        </span>
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 transition-all duration-200"
                            :class="activeTab === 'details' ? 'bg-[#CA9A2E]' : 'bg-transparent'"></div>
                    </button>

                </nav>
            </div>
        </div>


        {{-- ── 3. TAB CONTENT PANELS ────────────────────────────────────────── --}}
        <div class="w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex flex-col lg:flex-row w-full gap-12">

                    {{-- Left: Active Tab Content --}}
                    <div class="tour-tab-left w-full lg:flex-1">

                        {{-- ── OVERVIEW PANEL ── --}}
                        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 font-brand">
                                {{ __('Tour Overview') }}</h2>

                            <div
                                class="prose max-w-none text-gray-600 dark:text-gray-300 prose-headings:font-brand prose-a:text-brand-primary hover:prose-a:text-brand-primary-hover overflow-x-auto w-full font-lora text-base leading-relaxed">
                                {!! $tour->description ?? '<p>No description provided for this tour yet.</p>' !!}
                            </div>

                            {{-- Seasonal Months Section --}}
                            @php
                                $allMonths = [
                                    'January',
                                    'February',
                                    'March',
                                    'April',
                                    'May',
                                    'June',
                                    'July',
                                    'August',
                                    'September',
                                    'October',
                                    'November',
                                    'December'
                                ];
                                $activeMonths = collect($tour->seasonal_months ?? [])->map(fn($m) => strtolower(substr(trim($m), 0, 3)))->toArray();
                            @endphp
                            <div
                                class="mt-10 bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 rounded-[3px] p-6 shadow-sm">
                                <h3
                                    class="text-lg tracking-wide font-bold text-gray-900 dark:text-white mb-4 font-brand flex items-center gap-2">
                                    <x-icon name="calendar-days" class="w-5 h-5 text-brand-primary" />
                                    {{ __('Seasonal Months & Best Time to Travel') }}
                                </h3>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                                    @foreach($allMonths as $month)
                                        @php
                                            $isActive = in_array(strtolower(substr($month, 0, 3)), $activeMonths);
                                        @endphp
                                        <div
                                            class="flex flex-col items-center justify-center py-3 px-1 rounded-[3px] border transition-all duration-300 {{ $isActive ? 'bg-brand-primary/10 border-brand-primary/30 text-brand-primary shadow-sm font-semibold' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-400' }}">
                                            <span
                                                class="text-xs uppercase tracking-wider font-bold">{{ substr($month, 0, 3) }}</span>
                                            @if($isActive)
                                                <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary mt-1"></span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-sm bg-transparent mt-1"></span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-sm bg-brand-primary inline-block"></span>
                                    {{ __('Highlighted months indicate the peak/optimal seasonal period for this tour.') }}
                                </p>
                            </div>

                            {{-- Day-by-Day Summary Timeline Table --}}
                            @php
                                $groupedItineraries = [];
                                $currentGroup = null;

                                foreach ($tour->itineraries as $itinerary) {
                                    $loc = $itinerary->location;
                                    $accId = $itinerary->accommodation_id;

                                    if ($currentGroup === null) {
                                        $currentGroup = [
                                            'start_day' => $itinerary->day_number,
                                            'end_day' => $itinerary->day_number,
                                            'location' => $loc,
                                            'accommodation' => $itinerary->accommodationModel,
                                            'meal_plan' => $itinerary->mealPlan,
                                        ];
                                    } else {
                                        $locMatch = ($currentGroup['location'] === $loc);
                                        $accMatch = (($currentGroup['accommodation']?->id ?? null) === ($itinerary->accommodationModel?->id ?? null));

                                        if ($locMatch && $accMatch) {
                                            $currentGroup['end_day'] = $itinerary->day_number;
                                        } else {
                                            $groupedItineraries[] = $currentGroup;
                                            $currentGroup = [
                                                'start_day' => $itinerary->day_number,
                                                'end_day' => $itinerary->day_number,
                                                'location' => $loc,
                                                'accommodation' => $itinerary->accommodationModel,
                                                'meal_plan' => $itinerary->mealPlan,
                                            ];
                                        }
                                    }
                                }
                                if ($currentGroup !== null) {
                                    $groupedItineraries[] = $currentGroup;
                                }
                            @endphp

                            @if(count($groupedItineraries) > 0)
                                <div class="mt-10">
                                    <h3
                                        class="text-lg tracking-wide font-bold text-gray-900 dark:text-white mb-4 font-brand flex items-center gap-2">
                                        <x-icon name="clipboard-list" class="w-5 h-5 text-brand-primary" />
                                        {{ __('Day-by-Day Summary') }}
                                    </h3>

                                    <div
                                        class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-gray-600 dark:text-gray-300 mb-4 bg-brand-light/30 p-4 rounded-[3px] border border-brand-light/50 dark:border-gray-800">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="plane"
                                                class="w-4.5 h-4.5 text-brand-primary flex-shrink-0 rotate-90" />
                                            <span><strong
                                                    class="text-gray-800 dark:text-white">{{ __('Arrival:') }}</strong>{{ __(' Airport transfer included') }}</span>
                                        </div>
                                        <span class="hidden md:inline text-gray-300 dark:text-gray-750">|</span>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="map-pin" class="w-4.5 h-4.5 text-brand-primary flex-shrink-0" />
                                            <span><strong
                                                    class="text-gray-800 dark:text-white">{{ __('Start Destination:') }}</strong>
                                                {{ $groupedItineraries[0]['location'] ?? 'Arusha / Nairobi' }}</span>
                                        </div>
                                    </div>

                                    <div
                                        class="overflow-hidden border border-gray-200/70 dark:border-gray-800 rounded-[3px] shadow-sm">
                                        <table class="w-full text-left border-collapse bg-white dark:bg-gray-900 text-xs">
                                            <thead>
                                                <tr
                                                    class="border-b border-gray-200/80 dark:border-gray-800 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-950/80">
                                                    <th class="py-3 px-4 w-40 !text-sm">{{ __('Days') }}</th>
                                                    <th class="py-3 px-4 !text-sm">{{ __('Main Destination') }}</th>
                                                    <th class="py-3 px-4 !text-sm">{{ __('Accommodation') }}</th>
                                                    <th class="py-3 px-4 !text-sm">{{ __('Meal Plan') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                                                @foreach($groupedItineraries as $group)
                                                    <tr
                                                        class="relative group hover:bg-gray-50/40 dark:hover:bg-gray-850/40 transition-colors">
                                                        <td
                                                            class="py-2.5 px-4 font-semibold text-gray-900 dark:text-white relative align-middle">
                                                            <span class="!text-sm block font-semibold">
                                                                @if($group['start_day'] === $group['end_day'])
                                                                    Day {{ $group['start_day'] }}
                                                                @else
                                                                    Day {{ $group['start_day'] }}-{{ $group['end_day'] }}
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $group['location'] ?? 'Featured National Park' }}
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm text-gray-600 dark:text-gray-300">
                                                            {{ $group['accommodation']?->name ?? 'Standard/Luxury Lodge' }}
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm text-gray-600 dark:text-gray-300">
                                                            {{ $group['meal_plan']?->name ?? 'Full Board' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Tour Quick Facts --}}
                            @php
                                $firstAccommodation = $tour->accommodations()->first()
                                    ?? $tour->itineraries->pluck('accommodationModel')->filter()->first();
                                $accommodationRateClass = $firstAccommodation?->rates()->first()?->class;
                                if ($accommodationRateClass) {
                                    $tourClass = ucfirst(strtolower($accommodationRateClass)) . ' Tour';
                                } else {
                                    if ($tour->adult_price >= 2000) {
                                        $tourClass = 'Luxury Tour';
                                    } elseif ($tour->adult_price >= 1000) {
                                        $tourClass = 'Mid-range Tour';
                                    } else {
                                        $tourClass = 'Standard Tour';
                                    }
                                }
                            @endphp
                            <div class="mt-12 border-t border-gray-150 dark:border-gray-800 pt-8">
                                <h3
                                    class="text-lg tracking-wide font-bold text-gray-900 dark:text-white mb-6 font-brand flex items-center gap-2">
                                    <x-icon name="information-circle" class="w-5 h-5 text-brand-primary" />
                                    {{ __('Tour Quick Facts') }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="check-badge" class="w-5 h-5 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ __('Tour Class') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ $tourClass }}:</span>
                                                Hand-picked lodges and camps.</p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="user-group" class="w-5 h-5 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ __('Group Type') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">Shared/Private:</span>
                                                Max {{ $tour->max_guests ?? 6 }} people per vehicle.</p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="calendar-days" class="w-5 h-5 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ __('Start Day') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">Daily:</span>
                                                Flexible departures upon reservation.</p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="identification" class="w-5 h-5 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                                {{ __('Minimum Age') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">Min
                                                    {{ $tour->min_age ?? 5 }} Years:</span> Families warmly welcomed.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /overview panel -->


                        {{-- ── ITINERARY PANEL ── --}}
                        <div x-show="activeTab === 'itinerary'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10"
                            style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 font-brand flex items-center">
                                <x-icon name="map" class="w-7 h-7 mr-3 text-brand-primary" />
                                {{ __('Day by Day Experience') }}
                            </h2>

                            @if($tour->itineraries->count() > 0)
                                <div
                                    class="relative pl-6 md:pl-10 border-l border-dashed border-gray-300 dark:border-gray-800 ml-4 space-y-12 pb-6">
                                    @foreach($tour->itineraries as $index => $day)
                                        <div class="relative flex flex-col md:flex-row md:items-start gap-6 md:gap-8">
                                            {{-- Timeline Node --}}
                                            <div
                                                class="absolute -left-[32px] md:-left-[48px] top-6 md:top-8 w-4 h-4 rounded-sm border-2 border-brand-primary bg-white dark:bg-gray-950 shadow-sm flex items-center justify-center z-10">
                                                <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary"></span>
                                            </div>

                                            {{-- Left: Square Image --}}
                                            <div class="w-full md:w-1/3 lg:w-1/4 flex-shrink-0 order-2 md:order-1 mt-4 md:mt-0">
                                                <div
                                                    class="w-full aspect-square rounded-[3px] overflow-hidden shadow-sm group border border-gray-150 dark:border-gray-800 bg-gray-100 dark:bg-gray-900">
                                                    @if($day->featured_image)
                                                        <img src="{{ Storage::url($day->featured_image) }}"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                            alt="{{ $day->title }}">
                                                    @elseif($tour->featured_image)
                                                        <img src="{{ Storage::url($tour->featured_image) }}"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                                            alt="{{ $day->title }}">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <x-icon name="photo" class="w-10 h-10" />
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Right: Day Content --}}
                                            <div class="flex-1 space-y-4 order-1 md:order-2">
                                                <div>
                                                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-primary">
                                                        DAY {{ $day->day_number ?? ($index + 1) }}
                                                    </span>
                                                    <h3
                                                        class="text-xl md:text-2xl font-bold font-brand text-gray-900 dark:text-white mt-1">
                                                        {{ $day->title }}
                                                    </h3>
                                                </div>

                                                <div
                                                    class="prose max-w-none text-gray-600 dark:text-gray-300 font-lora leading-relaxed text-[15px]">
                                                    {!! $day->description !!}
                                                </div>

                                                {{-- Highlights card: Location, Meals, Accommodation --}}
                                                @if($day->location || $day->mealPlan || $day->accommodationModel)
                                                    <div
                                                        class="bg-gray-50/70 dark:bg-gray-900/60 border border-gray-200/70 dark:border-gray-800 rounded-[3px] p-4 text-xs space-y-2 mt-4">
                                                        @if($day->location)
                                                            <div class="flex items-center gap-2">
                                                                <x-icon name="map-pin" class="w-4 h-4 text-brand-primary shrink-0" />
                                                                <span
                                                                    class="text-gray-500 uppercase tracking-wider font-bold">{{ __('Main Destination:') }}</span>
                                                                <span
                                                                    class="text-gray-900 dark:text-white font-medium">{{ $day->location }}</span>
                                                            </div>
                                                        @endif
                                                        @if($day->accommodationModel)
                                                            <div class="flex items-center gap-2">
                                                                <x-icon name="home" class="w-4 h-4 text-emerald-600 shrink-0" />
                                                                <span
                                                                    class="text-gray-500 uppercase tracking-wider font-bold">{{ __('Accommodation:') }}</span>
                                                                <span
                                                                    class="text-gray-900 dark:text-white font-medium">{{ $day->accommodationModel->name }}</span>
                                                            </div>
                                                        @endif
                                                        @if($day->mealPlan)
                                                            <div class="flex items-center gap-2">
                                                                <x-icon name="cake" class="w-4 h-4 text-amber-500 shrink-0" />
                                                                <span
                                                                    class="text-gray-500 uppercase tracking-wider font-bold">{{ __('Meals & Drinks:') }}</span>
                                                                <span
                                                                    class="text-gray-900 dark:text-white font-medium">{{ $day->mealPlan->name }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="p-8 bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-300 dark:border-gray-800 rounded-[3px] text-center">
                                    <p class="text-gray-500 italic">
                                        {{ __('Detailed day-by-day itineraries are being finalized.') }}</p>
                                </div>
                            @endif
                        </div><!-- /itinerary panel -->


                        {{-- ── RATES PANEL ── --}}
                        <div x-show="activeTab === 'rates'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10"
                            style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 font-brand">
                                {{ __('Tour Rates & Pricing') }}</h2>

                            <div class="prose max-w-none text-gray-600 dark:text-gray-300 font-lora">
                                <p>Below are our group rates per person based on the number of guests traveling together.
                                    Discounts apply for larger parties sharing transport and guide costs.</p>
                            </div>

                            {{-- Rates Table --}}
                            <div
                                class="overflow-x-auto border border-gray-200/70 dark:border-gray-800 rounded-[3px] shadow-sm">
                                <table
                                    class="w-full text-center border-collapse bg-white dark:bg-gray-900 text-sm whitespace-nowrap">
                                    <thead>
                                        <tr
                                            class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-950/80">
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                {{ __('Solo') }}</th>
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                2 People</th>
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                3 People</th>
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                4 People</th>
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                5 People</th>
                                            <th
                                                class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                6 People</th>
                                            <th class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100">7+ People</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->adult_price ? '$' . number_format($tour->adult_price, 0) : 'Quote only' }}
                                            </td>
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_2_pax ? '$' . number_format($tour->price_2_pax, 0) : ($tour->adult_price ? '$' . number_format($tour->adult_price, 0) : '-') }}
                                            </td>
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_3_pax ? '$' . number_format($tour->price_3_pax, 0) : '-' }}
                                            </td>
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_4_pax ? '$' . number_format($tour->price_4_pax, 0) : '-' }}
                                            </td>
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_5_pax ? '$' . number_format($tour->price_5_pax, 0) : '-' }}
                                            </td>
                                            <td
                                                class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_6_pax ? '$' . number_format($tour->price_6_pax, 0) : '-' }}
                                            </td>
                                            <td class="py-4 px-4 text-gray-800 dark:text-gray-200">
                                                {{ $tour->price_7_plus_pax ? '$' . number_format($tour->price_7_plus_pax, 0) : '-' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-[3px] border border-gray-150 dark:border-gray-800 text-xs sm:text-sm text-gray-600 dark:text-gray-300 space-y-2">
                                <p><strong>Per Person Pricing:</strong> All rates listed are in USD per person sharing
                                    double/twin accommodation.</p>
                                <p><strong>Solo Travel:</strong> Solo travelers can join; requires a minimum of
                                    {{ $tour->min_guests ?? 2 }} people to operate.</p>
                                <p><strong>Age Requirements:</strong> Children under
                                    {{ $tour->children_free_under_age ?? 6 }} travel free; discounted entrance applies for
                                    older children.</p>
                            </div>
                        </div><!-- /rates panel -->


                        {{-- ── INCLUDED / EXCLUDED PANEL ── --}}
                        <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10"
                            style="display: none;">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 font-brand">
                                {{ __('What\'s Included & Excluded') }}</h2>

                            <div class="space-y-8 w-full">
                                {{-- Included --}}
                                <div
                                    class="w-full bg-emerald-50/50 dark:bg-emerald-950/20 rounded-[3px] p-8 border border-emerald-100 dark:border-emerald-900/20 shadow-sm">
                                    <h3
                                        class="text-xl font-bold text-gray-900 dark:text-white mb-6 font-brand flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-sm bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center mr-3 text-emerald-600">
                                            <x-icon name="check" class="w-5 h-5" />
                                        </div>
                                        {{ __('Included') }}
                                    </h3>
                                    <ul class="space-y-3">
                                        @if($tour->included_features && count($tour->included_features) > 0)
                                            @foreach($tour->included_features as $feature)
                                                <li
                                                    class="flex items-start text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                    <x-icon name="check" class="w-4 h-4 text-emerald-500 mr-3 mt-0.5 shrink-0" />
                                                    <span>{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-gray-400 italic text-xs">
                                                {{ __('Standard national park fees, professional guide, 4x4 safari cruiser, and full board accommodation.') }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                {{-- Excluded --}}
                                <div
                                    class="w-full bg-rose-50/50 dark:bg-rose-950/20 rounded-[3px] p-8 border border-rose-100 dark:border-rose-900/20 shadow-sm">
                                    <h3
                                        class="text-xl font-bold text-gray-900 dark:text-white mb-6 font-brand flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-sm bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center mr-3 text-rose-600">
                                            <x-icon name="x-mark" class="w-5 h-5" />
                                        </div>
                                        {{ __('Excluded') }}
                                    </h3>
                                    <ul class="space-y-3">
                                        @if($tour->excluded_features && count($tour->excluded_features) > 0)
                                            @foreach($tour->excluded_features as $feature)
                                                <li
                                                    class="flex items-start text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium">
                                                    <x-icon name="x-mark" class="w-4 h-4 text-rose-500 mr-3 mt-0.5 shrink-0" />
                                                    <span>{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-gray-400 italic text-xs">
                                                {{ __('International flights, visa fees, travel insurance, and driver/guide tips.') }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div><!-- /included/excluded panel -->

                    </div><!-- /left tab content -->


                    {{-- ── RIGHT SIDEBAR ── --}}
                    <div class="tour-tab-right w-full lg:w-1/3 xl:w-1/4">
                        <div class="sticky top-[100px] space-y-6">
                            <div
                                class="bg-white dark:bg-gray-900 rounded-[3px] shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-150 dark:border-gray-800 overflow-hidden">
                                {{-- Price Header --}}
                                <div
                                    class="bg-gray-900 dark:bg-gray-950 text-white p-6 text-center relative overflow-hidden">
                                    <div class="relative z-10">
                                        <div
                                            class="text-[10px] font-bold uppercase tracking-widest text-brand-primary mb-2">
                                            {{ __('Price From') }}</div>
                                        <div class="flex items-baseline justify-center mb-1">
                                            <span class="text-2xl font-bold text-brand-primary mr-1">$</span>
                                            <span
                                                class="text-4xl font-black font-brand text-white">{{ number_format($tour->adult_price, 0) }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 font-medium">{{ __('per person') }}</div>
                                    </div>
                                </div>

                                <div class="p-6 space-y-4">
                                    {{-- Request Quote Component --}}
                                    <livewire:frontend.request-quote :tour="$tour" />

                                    <a href="{{ route('tours.index') }}"
                                        class="w-full py-2.5 px-4 border border-gray-200 dark:border-gray-800 hover:border-brand-primary text-gray-600 dark:text-gray-300 hover:text-brand-primary text-center font-bold rounded-[3px] transition-all duration-200 flex items-center justify-center text-xs uppercase tracking-wider">
                                        ← {{ __('Back to All Tours') }}
                                    </a>
                                </div>
                            </div>

                            {{-- Trust Badge --}}
                            <div
                                class="bg-white dark:bg-gray-900 rounded-[3px] p-4 text-center border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col items-center">
                                <x-icon name="device-phone-mobile" class="w-7 h-7 text-brand-primary mb-2" />
                                <span
                                    class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">24/7
                                    Expert Support</span>
                                <span class="text-[11px] text-gray-400 mt-1">Direct assistance from our on-ground
                                    team</span>
                            </div>
                        </div>
                    </div><!-- /right sidebar -->

                </div>
            </div>
        </div><!-- /tab panels -->

    </div>

@endsection