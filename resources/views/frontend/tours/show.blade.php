@extends('layouts.frontend')

@section('title', $tour->name . ' |  ')



@section('content')

    <!-- ── HERO ─────────────────────────────────────────────── -->
    <div class="relative w-full h-[62vh] min-h-[480px] bg-gray-900 bg-cover bg-center overflow-hidden"
        style="background-image: url('{{ $tour->featured_image ? Storage::url($tour->featured_image) : ' ' }}')">

        <!-- Dark overlay to simulate image opacity -->
        <div class="absolute inset-0 bg-gray-950/40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-gray-950/80 to-transparent"></div>

        <!-- Breadcrumb -->
        <div class="absolute top-6 left-0 right-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 z-20">
            <nav class="flex items-center gap-2 !text-xs text-gray-300 font-medium bg-black/30 w-fit px-4 py-2 rounded-sm backdrop-blur-sm">
                <a href="{{ route('home') }}" class="!text-xs hover:text-white transition-colors">{{ __('Home') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <a href="{{ route('tours.index') }}" class="!text-xs hover:text-white transition-colors">{{ __('Tours') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <span class="!text-xs text-gray-400">{{ $tour->name }}</span>
            </nav>
        </div>

        <div class="absolute bottom-0 left-0 right-0 max-w-7xl p-8 md:p-16 mx-auto text-white z-10">
            <div
                class="inline-flex items-center px-3 py-1 rounded-[3px] bg-brand-primary/20 text-brand-primary border border-brand-primary/30 uppercase tracking-widest text-xs font-bold mb-5">
                {{ $tour->category->name ?? 'Safari Adventure' }}
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-5 font-brand leading-normal tracking-wide text-white">
                {{ $tour->name }}
            </h1>
            <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-200">
                <span class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm">
                    <x-icon name="clock" class="w-4 h-4 mr-2 text-brand-primary" />
                    {{ $tour->duration_days }} Days / {{ $tour->duration_nights }} Nights
                </span>
                @if($tour->destinations && $tour->destinations->count())<span class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm">
                        <x-icon name="map-pin" class="w-4 h-4 mr-2 text-brand-primary" />
                        {{ $tour->destinations->count() }} Destination{{ $tour->destinations->count() !== 1 ? 's' : '' }}
                    </span>
                @endif
                @if($tour->difficulty)<span class="flex items-center bg-white/10 px-4 py-2 rounded-[3px] backdrop-blur-sm">
                        <x-icon name="trending-up" class="w-4 h-4 mr-2 text-brand-primary" />
                        {{ $tour->difficulty }}
                    </span>
                @endif
            </div>
        </div>
    </div><!-- /hero -->


    <!-- ── TABS ──────────────────────────────────────────────── -->
    <div x-data="{ activeTab: 'overview' }" class="flex flex-col">

        <!-- Tab Panels -->
        <div class="w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex flex-col lg:flex-row w-full gap-15">
                    <!-- Left: Tab Content -->
                    <div class="tour-tab-left  w-full lg:flex-1">
                        <!-- Sticky Tab Bar -->
                        <div class="sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm mb-8 sm:mx-0 -mx-4 px-4 sm:px-0">
                            <nav class="flex overflow-x-auto w-full">
                                <!-- Overview -->
                                <button @click="activeTab = 'overview'"
                                    :class="activeTab === 'overview' ? 'border-b-2 border-brand-primary text-brand-primary font-bold bg-brand-light/60' : 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'"
                                    class="flex-1 flex shrink-0 items-center justify-center gap-2 whitespace-nowrap py-4 px-5 text-sm transition-all duration-200 focus:outline-none">
                                    <x-icon name="bars-3-bottom-left" class="w-4 h-4 flex-shrink-0" />
                                    {{ __('Overview') }}
                                </button>

                                <!-- Itinerary -->
                                <button @click="activeTab = 'itinerary'"
                                    :class="activeTab === 'itinerary' ? 'border-b-2 border-brand-primary text-brand-primary font-bold bg-brand-light/60' : 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'"
                                    class="flex-1 flex shrink-0 items-center justify-center gap-2 whitespace-nowrap py-4 px-5 text-sm transition-all duration-200 focus:outline-none">
                                    <x-icon name="map" class="w-4 h-4 flex-shrink-0" />
                                    {{ __('Day by Day Itinerary') }}
                                </button>

                                <!-- Rates -->
                                <button @click="activeTab = 'rates'"
                                    :class="activeTab === 'rates' ? 'border-b-2 border-brand-primary text-brand-primary font-bold bg-brand-light/60' : 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'"
                                    class="flex-1 flex shrink-0 items-center justify-center gap-2 whitespace-nowrap py-4 px-5 text-sm transition-all duration-200 focus:outline-none">
                                    <x-icon name="currency-dollar" class="w-4 h-4 flex-shrink-0" />
                                    {{ __('Rates') }}
                                </button>

                                <!-- Included/Excluded -->
                                <button @click="activeTab = 'details'"
                                    :class="activeTab === 'details' ? 'border-b-2 border-brand-primary text-brand-primary font-bold bg-brand-light/60' : 'border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800'"
                                    class="flex-1 flex shrink-0 items-center justify-center gap-2 whitespace-nowrap py-4 px-5 text-sm transition-all duration-200 focus:outline-none">
                                    <x-icon name="clipboard-list" class="w-4 h-4 flex-shrink-0" />
                                    {{ __('Included / Excluded') }}
                                </button>
                            </nav>
                        </div> <!-- End of Sticky Tab Bar -->

                        <!-- ── OVERVIEW ── -->
                        <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 font-brand">{{ __('Tour Overview') }}</h2>

                            <div
                                class="prose max-w-none text-gray-600 dark:text-gray-300 prose-headings:font-brand prose-a:text-brand-primary hover:prose-a:text-brand-primary-hover overflow-x-auto w-full font-lora">
                                {!! $tour->description ?? '<p>No description provided for this tour yet.</p>' !!}
                            </div>

                            <!-- 1. Seasonal Months Section -->
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
                                    class="text-lg tracking-wide leading-normal font-bold text-gray-900 dark:text-white mb-4 font-brand flex items-center gap-2">
                                    <x-icon name="calendar-days" class="w-5 h-5 text-brand-primary" />
                                    {{ __('Seasonal Months & Best Time to Travel') }}
                                </h3>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-12 gap-2">
                                    @foreach($allMonths as $month)
                                        @php
                                            $isActive = in_array(strtolower(substr($month, 0, 3)), $activeMonths);
                                        @endphp
                                        <div
                                            class="flex flex-col items-center justify-center py-3 px-1 rounded-[3px] border transition-all duration-300 {{ $isActive ? 'bg-brand-primary/10 border-brand-primary/30 text-brand-primary shadow-sm font-semibold' : 'bg-white dark:bg-gray-900 border-gray-200/60 dark:border-gray-800 text-gray-400 dark:text-gray-550' }}">
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

                            <!-- 2. Day-by-Day Summary Timeline Table -->
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
                                        class="text-lg tracking-wide leading-normal font-bold text-gray-900 dark:text-white mb-4 font-brand flex items-center gap-2">
                                        <x-icon name="clipboard-list" class="w-5 h-5 text-brand-primary" />{{ __('Day-by-Day Summary') }}</h3>

                                    <!-- Arrival Info -->
                                    <div
                                        class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-gray-600 dark:text-gray-300 mb-4 bg-brand-light/30 p-4 rounded-[3px] border border-brand-light/50 dark:border-gray-800">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="plane"
                                                class="w-4.5 h-4.5 text-brand-primary flex-shrink-0 rotate-90" />
                                            <span><strong class="text-gray-800 dark:text-white">{{ __('Arrival:') }}</strong>{{ __(' Airport transfer included') }}</span>
                                        </div>
                                        <span class="hidden md:inline text-gray-300 dark:text-gray-750">|</span>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="map-pin" class="w-4.5 h-4.5 text-brand-primary flex-shrink-0" />
                                            <span><strong class="text-gray-800 dark:text-white">{{ __('Start Destination:') }}</strong>
                                                {{ $groupedItineraries[0]['location'] ?? ' ' }}</span>
                                        </div>
                                    </div>

                                    <!-- Table -->
                                    <div
                                        class="overflow-hidden border border-gray-200/70 dark:border-gray-800 rounded-[3px] shadow-sm">
                                        <table class="w-full text-left border-collapse bg-white dark:bg-gray-900 text-xs">
                                            <thead>
                                                <tr
                                                    class="border-b border-gray-200/80 dark:border-gray-800 text-xs font-bold uppercase tracking-wider text-gray-505 dark:text-gray-400 bg-gray-50/80 dark:bg-gray-950/80">
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
                                                        <!-- Days Column with Vertical Timeline -->
                                                        <td class="py-2.5 px-4 font-semibold text-gray-900 dark:text-white relative align-middle">
                                                            <!-- Vertical connector line -->
                                                            @if(count($groupedItineraries) > 1)
                                                                <div
                                                                    class="absolute left-6 w-0.5 bg-brand-primary/30 group-hover:bg-brand-primary/50 transition-colors {{ $loop->first ? 'top-1/2 bottom-0' : ($loop->last ? 'top-0 bottom-1/2' : 'top-0 bottom-0') }}">
                                                                </div>
                                                            @endif

                                                            <!-- Timeline node circle -->
                                                            <div
                                                                class="absolute left-[18px] top-1/2 -translate-y-1/2 w-3.5 h-3.5 rounded-sm bg-white dark:bg-gray-905 border-2 border-brand-primary flex items-center justify-center z-10 shadow-sm">
                                                                <div class="w-1.5 h-1.5 rounded-sm bg-brand-primary"></div>
                                                            </div>

                                                            <!-- Day Label -->
                                                            <span class="pl-8 !text-sm block font-semibold">
                                                                @if($group['start_day'] === $group['end_day'])
                                                                    Day {{ $group['start_day'] }}
                                                                @else
                                                                    Day {{ $group['start_day'] }}-{{ $group['end_day'] }}
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm font-medium text-gray-900 dark:text-white">
                                                            @if($group['location'])
                                                                <span class="!text-sm text-gray-800 dark:text-gray-200">{{ $group['location'] }}</span>
                                                            @else
                                                                <span class="!text-sm text-gray-400 dark:text-gray-500 italic">{{ __('Based on the intenary') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm text-gray-600 dark:text-gray-300">
                                                            @if($group['accommodation'])
                                                                <span
                                                                    class="!text-sm text-gray-800 dark:text-gray-200">{{ $group['accommodation']->name }}</span>
                                                            @else
                                                                <span class="!text-sm text-gray-400 dark:text-gray-500 italic">{{ __('Based on the intenary') }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2.5 px-4 !text-sm text-gray-600 dark:text-gray-300">
                                                            @if($group['meal_plan'])
                                                                <span class="!text-sm text-gray-800 dark:text-gray-200">{{ $group['meal_plan']->name }}</span>
                                                            @else
                                                                <span class="!text-sm text-gray-400 dark:text-gray-500 italic">{{ __('Based on the intenary') }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Departure Info -->
                                    <div
                                        class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm text-gray-600 dark:text-gray-300 mt-4 bg-brand-light/30 p-4 rounded-[3px] border border-brand-light/50 dark:border-gray-800">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="map-pin" class="w-4.5 h-4.5 text-brand-primary flex-shrink-0" />
                                            <span><strong class="text-gray-800 dark:text-white">{{ __('End Destination:') }}</strong>
                                                {{ end($groupedItineraries)['location'] ?? 'Departure' }}</span>
                                        </div>
                                        <span class="hidden md:inline text-gray-300 dark:text-gray-750">|</span>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="plane" class="w-4.5 h-4.5 text-brand-primary flex-shrink-0" />
                                            <span><strong class="text-gray-800 dark:text-white">{{ __('Departure:') }}</strong>{{ __('Onward
                                                flight or optional
                                                extensions') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- 3. Redesigned Tour Quick Facts -->
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
                                    class="text-lg tracking-wide leading-normal font-bold text-gray-900 dark:text-white mb-6 font-brand flex items-center gap-2">
                                    <x-icon name="information-circle" class="w-5 h-5 text-brand-primary" />{{ __('Tour Quick Facts') }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Tour Class -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="check-badge" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Tour Class') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ $tourClass }}
                                                    :</span>{{ __('This safari
                                                includes comfort-focused hand-picked lodges and camps.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Group Type -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="user-group" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Group Type') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Shared group tour
                                                    :') }}</span> Join a group
                                                of other travelers. Max {{ $tour->max_guests ?? 7 }} people per vehicle.</p>
                                        </div>
                                    </div>

                                    <!-- Start Day -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="calendar-days" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Start Day') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Can
                                                    start any day :') }}</span>{{ __('If availability permits, this tour can start on
                                                any
                                                day.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Customization -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="adjustments-horizontal" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Customization') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Can
                                                    be customized :') }}</span>{{ __('Request minor changes to accommodations and
                                                destinations.') }}</p>
                                        </div>
                                    </div>

                                    <!-- Solo Travelers -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="user" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Single Travelers') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">{{ __('Suitable for
                                                    single travelers :') }}</span>
                                                Solo travelers can join, requires a minimum of {{ $tour->min_guests ?? 2 }}
                                                people to run.</p>
                                        </div>
                                    </div>

                                    <!-- Minimum Age -->
                                    <div
                                        class="flex items-start gap-4 p-4 rounded-[3px] border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition-all duration-300">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-[3px] bg-brand-light flex items-center justify-center">
                                            <x-icon name="identification" class="w-6 h-6 text-brand-primary" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('Minimum Age') }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><span
                                                    class="font-semibold text-gray-800 dark:text-gray-200">Minimum age of
                                                    {{ $tour->min_age ?? 5 }}
                                                    years :</span>{{ __('Children and families are welcome under parental
                                                guidance.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!--endof overview-->

                        <!-- ── RATES ── -->
                        <div x-show="activeTab === 'rates'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 font-brand">{{ __('Tour Rates &
                                Pricing') }}</h2>

                            <div class="prose prose-lg max-w-none text-gray-600 dark:text-gray-300">
                                <p class=" mb-4">Below are our rates for travelers visiting as a group. Discounts are
                                    available for larger parties, making group travel more affordable and enjoyable. Whether
                                    you are planning a family trip, a getaway with friends, or a special event with
                                    colleagues, our group rates are designed to offer better value while keeping the
                                    experience comfortable and convenient. Please contact us for more details on pricing,
                                    availability, and any special arrangements for your group.</p>
                                <ol class="space-y-2 mb-4 pl-4">
                                    <li class="flex items-start gap-4">
                                        <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary mt-1.5 shrink-0"></span>
                                        <span>
                                            <strong>{{ __('Per Person Pricing:') }}</strong>{{ __('All rates listed are per person and do not include international flights.') }}</span>
                                    </li>

                                    <li class="flex items-start gap-4">
                                        <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary mt-1.5 shrink-0"></span>
                                        <span>
                                            <strong>{{ __('Solo Travel:') }}</strong>
                                            Solo travelers are welcome; however, the tour requires a minimum of
                                            {{ $tour->min_guests ?? 2 }} people to run.
                                        </span>
                                    </li>

                                    <li class="flex items-start gap-4">
                                        <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary mt-1.5 shrink-0"></span>
                                        <span>
                                            <strong>{{ __('Age Requirements:') }}</strong>
                                            Children aged {{ $tour->min_age ?? 5 }} and above are allowed and discounted.
                                            Free entrance fees apply for children under
                                            {{ $tour->children_free_under_age ?? 5 }} years old.
                                        </span>
                                    </li>

                                </ol>

                                <p class=" mb-4">
                                    <strong>{{ __('Please Note:') }}</strong> All rates are subject to change based on availability,
                                    seasonality, and any updates to park fees, taxes, or tourism levies. We recommend
                                    contacting us directly to confirm pricing for your specific travel dates and group size.
                                </p>
                            </div>

                            <!-- Group Pricing -->
                            <div>
                                <h3
                                    class="text-lg tracking-wide leading-normal font-bold text-gray-900 dark:text-white mb-6 font-brand flex items-center gap-2">
                                    <x-icon name="currency-dollar" class="w-5 h-5 text-brand-primary" />{{ __('Group Pricing Rates') }}</h3>
                                <div
                                    class="overflow-x-auto border border-gray-200/70 dark:border-gray-800 rounded-[3px] shadow-sm">
                                    <table
                                        class="w-full text-center border-collapse bg-white dark:bg-gray-900 text-sm whitespace-nowrap">
                                        <thead>
                                            <tr
                                                class="border-b border-gray-200/80 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-950/80">
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">{{ __('Solo') }}</th>
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                    2 people
                                                </th>
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                    3 people
                                                </th>
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                    4 people
                                                </th>
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                    5 people
                                                </th>
                                                <th
                                                    class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100 border-r border-gray-200/80 dark:border-gray-800">
                                                    6 people
                                                </th>
                                                <th class="py-4 px-4 font-bold text-gray-900 dark:text-gray-100">
                                                    7+ people
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->adult_price ? '$' . number_format($tour->adult_price, 0) : 'n/a*' }}
                                                </td>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_2_pax ? '$' . number_format($tour->price_2_pax, 0) : 'n/a*' }}
                                                </td>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_3_pax ? '$' . number_format($tour->price_3_pax, 0) : 'n/a*' }}
                                                </td>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_4_pax ? '$' . number_format($tour->price_4_pax, 0) : 'n/a*' }}
                                                </td>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_5_pax ? '$' . number_format($tour->price_5_pax, 0) : 'n/a*' }}
                                                </td>
                                                <td
                                                    class="py-4 px-4 border-r border-gray-200/80 dark:border-gray-800 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_6_pax ? '$' . number_format($tour->price_6_pax, 0) : 'n/a*' }}
                                                </td>
                                                <td class="py-4 px-4 text-gray-800 dark:text-gray-200">
                                                    {{ $tour->price_7_plus_pax ? '$' . number_format($tour->price_7_plus_pax, 0) : 'n/a*' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div><!--endof rates-->

                        <!-- ── ITINERARY ── -->
                        <div x-show="activeTab === 'itinerary'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 font-brand flex items-center">
                                <x-icon name="map" class="w-8 h-8 mr-3 text-brand-primary" />{{ __('Day by Day Experience') }}</h2>

                            @if($tour->itineraries->count() > 0)
                                <div
                                    class="relative pl-6 md:pl-10 border-l border-dashed border-gray-300 dark:border-gray-800 ml-4 space-y-12 pb-6">
                                    @foreach($tour->itineraries as $index => $day)
                                        <div class="relative flex flex-col md:flex-row md:items-center gap-6 md:gap-8">
                                            <!-- Timeline Node -->
                                            <div
                                                class="absolute -left-[32px] md:-left-[48px] top-6 md:top-8 w-4 h-4 rounded-sm border-2 border-brand-primary bg-white dark:bg-gray-950 shadow-sm flex items-center justify-center z-10">
                                                <span class="w-1.5 h-1.5 rounded-sm bg-brand-primary"></span>
                                            </div>

                                            <!-- Left: Image (Square) -->
                                            <div class="w-full md:w-1/3 lg:w-1/4 flex-shrink-0 order-2 md:order-1 mt-4 md:mt-0">
                                                <div class="w-full aspect-square rounded-[3px] overflow-hidden shadow-sm group border border-gray-100 dark:border-gray-800">
                                                    @if($day->featured_image)
                                                        <img src="{{ Storage::url($day->featured_image) }}"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                                                            alt="{{ $day->title }}">
                                                    @elseif($companySetting?->tour_fallback_image)
                                                        <img src="{{ Storage::url($companySetting->tour_fallback_image) }}"
                                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                                                            alt="{{ $day->title }}">
                                                    @else
                                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                            <x-icon name="photo" class="w-12 h-12 text-gray-400" />
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Right: Day Content -->
                                            <div class="flex-1 space-y-6 order-1 md:order-2">
                                                <div>
                                                    <h3 class="text-xl md:text-2xl font-bold font-brand text-gray-900 dark:text-white uppercase tracking-wide">
                                                        DAY {{ $day->day_number ?? $loop->iteration }}: {{ $day->title }}
                                                    </h3>
                                                </div>

                                                <div class="prose max-w-none text-gray-600 dark:text-gray-300 font-lora">
                                                    {!! $day->description !!}
                                                </div>

                                                <!-- Day Highlights Single Card -->
                                                @if($day->location || $day->mealPlan || $day->accommodationModel || ($day->animals && $day->animals->count() > 0) || ($day->transports && $day->transports->count() > 0) || ($day->pivotActivities && $day->pivotActivities->count() > 0))
                                                    <div
                                                        class="bg-gray-50/50 dark:bg-gray-900/50 border border-gray-200/60 dark:border-gray-800 rounded-[3px] p-6 shadow-sm w-full">
                                                        @php
                                                            $hasPrevious = false;
                                                        @endphp

                                                        <!-- Destination, Meals & Transport Grid -->
                                                        @if($day->location || $day->mealPlan || ($day->transports && $day->transports->count() > 0))
                                                            @php $hasPrevious = true; @endphp
                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                                <!-- Main Destination -->
                                                                @if($day->location)<div class="flex items-center gap-3">
                                                                        <div
                                                                            class="w-9 h-9 rounded-sm bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                                            <x-icon name="map-pin" class="w-5 h-5" />
                                                                        </div>
                                                                        <div>
                                                                            <span class="!text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block leading-none mb-1">{{ __('Main Destination') }}</span>
                                                                            <h4 class="!text-sm font-bold text-gray-800 dark:text-white leading-none"> {{ $day->location }}</h4>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <!-- Meals & Drinks -->
                                                                @if($day->mealPlan)<div class="flex items-center gap-3">
                                                                        <div
                                                                            class="w-9 h-9 rounded-sm bg-brand-light text-brand-primary flex items-center justify-center flex-shrink-0">
                                                                            <x-icon name="cake" class="w-5 h-5" />
                                                                        </div>
                                                                        <div>
                                                                            <span
                                                                                class="!text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block leading-none mb-1">{{ __('Meals
                                                                                &amp; Drinks') }}</span>
                                                                            <h4
                                                                                class="!text-sm font-bold text-gray-800 dark:text-white leading-none">
                                                                                {{ $day->mealPlan->name }}</h4>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                <!-- Transport -->
                                                                @if($day->transports && $day->transports->count() > 0)
                                                                    <div class="flex items-center gap-3">
                                                                        <div class="w-9 h-9 rounded-sm bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                                            <x-icon name="truck" class="w-5 h-5" />
                                                                        </div>
                                                                        <div>
                                                                            <span
                                                                                class="!text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block leading-none mb-1">{{ __('Transport') }}</span>
                                                                            <h4
                                                                                class="!text-sm font-bold text-gray-800 dark:text-white leading-none">
                                                                                {{ $day->transports->pluck('name')->join(', ') }}
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif



                                                        <!-- Accommodation -->
                                                        @if($day->accommodationModel)
                                                            @if($hasPrevious)
                                                                <div
                                                                    class="border-t border-dotted border-gray-300/60 dark:border-gray-800 my-5">
                                                                </div>
                                                            @endif
                                                            @php $hasPrevious = true; @endphp
                                                            @php
                                                                $acc = $day->accommodationModel;
                                                                $accImages = collect();
                                                                if ($acc->featured_image) {
                                                                    $accImages->push($acc->featured_image);
                                                                }
                                                                if (!empty($acc->gallery)) {
                                                                    foreach ($acc->gallery as $galImg) {
                                                                        $accImages->push($galImg);
                                                                    }
                                                                }
                                                                $totalAccImages = $accImages->count();
                                                                $visibleImages = $accImages->take(5);
                                                            @endphp
                                                            <div class="w-full"
                                                                x-data="{ openLightbox: false, activeIndex: 0, images: {{ json_encode($accImages->map(fn($img) => Storage::url($img))->toArray()) }} }">

                                                                <div class="flex items-center gap-3 mb-4">
                                                                    <div
                                                                        class="w-9 h-9 rounded-sm bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                                        <x-icon name="home" class="w-5 h-5" />
                                                                    </div>
                                                                    <div>
                                                                        <span
                                                                            class="text-sm font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block leading-none mb-1">{{ __('Accommodation') }}</span>
                                                                        <h4
                                                                            class="text-lg font-bold text-gray-800 dark:text-white leading-none">
                                                                            {{ $acc->name }}</h4>
                                                                    </div>
                                                                </div>

                                                                @if($totalAccImages > 0)
                                                                    <!-- Image Grid (5 Columns) -->
                                                                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                                                        @foreach($visibleImages as $imgIndex => $img)
                                                                            @php
                                                                                $isLast = ($imgIndex === 4 && $totalAccImages > 5);
                                                                            @endphp
                                                                            <div class="relative aspect-[4/3] rounded-[3px] overflow-hidden bg-gray-155 border border-gray-200/50 group cursor-pointer"
                                                                                @click="activeIndex = {{ $imgIndex }}; openLightbox = true">
                                                                                <img src="{{ Storage::url($img) }}" alt="{{ $acc->name }}"
                                                                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                                                                @if($isLast)
                                                                                    <!-- View Gallery / Plus Button Overlay -->
                                                                                    <div
                                                                                        class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center text-white transition-colors group-hover:bg-black/70">
                                                                                        <div
                                                                                            class="w-9 h-9 rounded-sm bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/30 mb-1 shadow-sm">
                                                                                            <x-icon name="plus" class="w-5 h-5 text-white" />
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-[10px] font-bold uppercase tracking-wider">+{{ $totalAccImages - 4 }}
                                                                                            More</span>
                                                                                    </div>
                                                                                @else
                                                                                    <div
                                                                                        class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                                                        <div
                                                                                            class="w-8 h-8 rounded-sm bg-white/35 backdrop-blur-sm flex items-center justify-center border border-white/40">
                                                                                            <x-icon name="magnifying-glass"
                                                                                                class="w-4 h-4 text-white" />
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <p class="text-xs text-gray-400 italic">{{ __('No images available for this
                                                                        accommodation.') }}</p>
                                                                @endif

                                                                <!-- Alpine.js Lightbox Modal -->
                                                                <div x-show="openLightbox"
                                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                                                                    x-transition:enter="transition ease-out duration-300"
                                                                    x-transition:leave="transition ease-in duration-200"
                                                                    style="display: none;" @click.self="openLightbox = false"
                                                                    @keydown.escape.window="openLightbox = false">

                                                                    <!-- Close Button -->
                                                                    <button type="button"
                                                                        class="absolute top-6 right-6 text-white/70 hover:text-white p-2 z-50 transition-colors"
                                                                        @click="openLightbox = false">
                                                                        <x-icon name="x-mark" class="w-8 h-8" />
                                                                    </button>

                                                                    <!-- Prev Button -->
                                                                    <button type="button"
                                                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white/75 hover:text-white p-3 z-50 transition-colors bg-black/30 hover:bg-black/50 rounded-sm"
                                                                        @click="activeIndex = (activeIndex === 0) ? images.length - 1 : activeIndex - 1">
                                                                        <x-icon name="chevron-left" class="w-8 h-8" />
                                                                    </button>

                                                                    <!-- Next Button -->
                                                                    <button type="button"
                                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/75 hover:text-white p-3 z-50 transition-colors bg-black/30 hover:bg-black/50 rounded-sm"
                                                                        @click="activeIndex = (activeIndex === images.length - 1) ? 0 : activeIndex + 1">
                                                                        <x-icon name="chevron-right" class="w-8 h-8" />
                                                                    </button>

                                                                    <!-- Active Slide -->
                                                                    <div
                                                                        class="max-w-4xl max-h-[80vh] w-full flex flex-col items-center justify-center">
                                                                        <img :src="images[activeIndex]"
                                                                            class="max-w-full max-h-[75vh] object-contain rounded-[3px] shadow-2xl">
                                                                        <p
                                                                            class="text-white/60 text-xs font-bold uppercase tracking-widest mt-4">{{ __('Image') }}<span x-text="activeIndex + 1"></span>{{ __('of') }}<span
                                                                                x-text="images.length"></span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Wildlife to Spot -->
                                                        @if($day->animals && $day->animals->count() > 0)
                                                            @if($hasPrevious)
                                                                <div
                                                                    class="border-t border-dotted border-gray-300/60 dark:border-gray-800 my-5">
                                                                </div>
                                                            @endif
                                                            @php $hasPrevious = true; @endphp
                                                            <div class="flex flex-col items-center text-center w-full">
                                                                <div
                                                                    class="w-9 h-9 rounded-sm bg-brand-light text-brand-primary flex items-center justify-center mb-2">
                                                                    <x-icon name="eye" class="w-5 h-5" />
                                                                </div>
                                                                <h4
                                                                    class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">{{ __('Wildlife to Spot') }}</h4>

                                                                <div class="flex flex-wrap items-center justify-center gap-4 w-full">
                                                                    @foreach($day->animals as $animal)
                                                                        <div
                                                                            class="group relative flex flex-col items-center justify-center p-3 bg-white dark:bg-gray-900 rounded-[3px] shadow-sm border border-gray-200 dark:border-gray-800 hover:border-brand-primary/50 hover:shadow-md transition-all duration-300 w-16 h-16 cursor-help">
                                                                            @if($animal->icon)<img src="{{ Storage::url($animal->icon) }}"
                                                                                    alt="{{ $animal->name }}"
                                                                                    class="w-8 h-8 object-contain transition-transform group-hover:scale-110">
                                                                            @else
                                                                                <span
                                                                                    class="text-xs font-bold text-gray-650 dark:text-gray-300 truncate w-full text-center">{{ $animal->name }}</span>
                                                                            @endif

                                                                            <!-- Hover Tooltip -->
                                                                            <div
                                                                                class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 bg-white dark:bg-gray-900 rounded-[3px] shadow-xl border border-gray-100 dark:border-gray-800 p-2 z-30 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 pointer-events-none">
                                                                                <div
                                                                                    class="relative w-full h-32 rounded-[3px] overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                                                                                    @if($animal->image_path)
                                                                                        <img src="{{ Storage::url($animal->image_path) }}"
                                                                                            alt="{{ $animal->name }}"
                                                                                            class="w-full h-full object-cover">
                                                                                    @else
                                                                                        <div
                                                                                            class="w-full h-full flex items-center justify-center bg-brand-light text-brand-primary">
                                                                                            <x-icon name="photo" class="w-8 h-8 opacity-60" />
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                                <div
                                                                                    class="text-center font-semibold text-xs text-gray-800 dark:text-white truncate px-1">
                                                                                    {{ $animal->name }}</div>
                                                                                <div
                                                                                    class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-white dark:border-t-gray-900">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Activities & Experiences (with Prices) -->
                                                        @if($day->pivotActivities && $day->pivotActivities->count() > 0)
                                                            @php
                                                                $includedActivities = $day->pivotActivities->filter(fn($act) => empty($act->pivot->amount) || $act->pivot->amount<= 0);
                                                                $extraActivities = $day->pivotActivities->filter(fn($act) => !empty($act->pivot->amount) && $act->pivot->amount > 0);
                                                            @endphp

                                                            @if($includedActivities->count() > 0)
                                                                @if($hasPrevious)
                                                                    <div class="border-t border-dotted border-gray-300/60 dark:border-gray-800 my-5"></div>
                                                                @endif
                                                                @php $hasPrevious = true; @endphp
                                                                <div class="flex flex-col items-center text-center w-full">
                                                                    <div class="w-9 h-9 rounded-sm flex items-center justify-center mb-2"
                                                                        style="background-color: rgb(204 251 241); color: rgb(13 148 136);">
                                                                        <x-icon name="map-sparkles" class="w-5 h-5" />
                                                                    </div>
                                                                    <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">{{ __('Included Activities &amp; Experiences') }}</h4>
                                                                    <div class="flex flex-wrap items-center justify-center gap-3 w-full">
                                                                        @foreach($includedActivities as $act)
                                                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-[3px] text-xs font-semibold bg-teal-50 dark:bg-teal-950/30 border border-teal-100 dark:border-teal-900/30 text-teal-800 dark:text-teal-400 shadow-xs">
                                                                                <span>{{ $act->name }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if($extraActivities->count() > 0)
                                                                @if($hasPrevious)
                                                                    <div class="border-t border-dotted border-gray-300/60 dark:border-gray-800 my-5"></div>
                                                                @endif
                                                                @php
                                                                    $currency_symbol = \App\Models\Currency::where('code', app(\App\Settings\GeneralSetting::class)->currency_default)->first()?->symbol ?? '$';
                                                                @endphp
                                                                <div class="flex flex-col items-center text-center w-full">
                                                                    <div class="w-9 h-9 rounded-sm flex items-center justify-center mb-2"
                                                                        style="background-color: rgb(255 251 235); color: rgb(217 119 6);">
                                                                        <x-icon name="currency-dollar" class="w-5 h-5" />
                                                                    </div>
                                                                    <h4 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">{{ __('Optional Add-ons (Extra Cost)') }}</h4>
                                                                    <div class="flex flex-wrap items-center justify-center gap-3 w-full">
                                                                        @foreach($extraActivities as $act)
                                                                            <div class="inline-flex gap-2 px-3 py-1.5 rounded-[3px] text-xs font-semibold bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/30 text-amber-800 dark:text-amber-500 shadow-xs">
                                                                                <span>{{ $act->name }}</span>
                                                                                <span class="px-1.5 py-1 rounded text-[10px] bg-amber-500 text-white font-bold leading-none">
                                                                                    + {{ $currency_symbol }}{{ number_format($act->pivot->amount, 2) }}
                                                                                </span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif {{-- end @if pivotActivities --}}
                                                    </div>{{-- end bg-gray-50 card div --}}
                                                @endif {{-- end day highlights @if --}}

                                                <!-- Day Gallery -->
                                                @php
                                                    $galleryImages = collect($day->gallery_images ?? [])->filter(fn($img) => $img !== $day->featured_image);
                                                @endphp
                                                @if($galleryImages->count() > 0)
                                                    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                                                        @foreach($galleryImages as $img)
                                                            <div
                                                                class="aspect-video rounded-[3px] overflow-hidden relative shadow-sm group">
                                                                <img src="{{ Storage::url($img) }}"
                                                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                                                <div
                                                                    class="absolute inset-0 bg-black/10 transition-colors group-hover:bg-transparent">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Divider line at the end of the day -->
                                            @if(!$loop->last)<div class="border-b border-gray-200 dark:border-gray-800 my-12"></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="p-8 bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-300 dark:border-gray-800 rounded-[3px] text-center">
                                    <p class="text-gray-500 dark:text-gray-400 italic">{{ __('No itinerary details have been added for
                                        this tour yet.') }}</p>
                                </div>
                            @endif

                        </div><!--end of itinerary-->

                        <!-- ── INCLUDED / EXCLUDED ── -->
                        <div x-show="activeTab === 'details'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" class="w-full space-y-10">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 font-brand text-left">{{ __('What\'s
                                Included') }}</h2>

                            <div class="space-y-8 w-full">
                                <!-- Included -->
                                <div
                                    class="w-full bg-emerald-50/50 dark:bg-emerald-950/20 rounded-[3px] p-8 border border-emerald-100 dark:border-emerald-900/20 shadow-sm overflow-hidden">
                                    <h3
                                        class="text-2xl font-bold text-gray-900 dark:text-white mb-8 font-brand flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-sm bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center mr-4">
                                            <x-icon name="check" class="w-6 h-6 text-emerald-600" />
                                        </div>{{ __('Included') }}</h3>
                                    <ul class="space-y-4">
                                        @if($tour->included_features && count($tour->included_features) > 0)
                                            @foreach($tour->included_features as $feature)
                                                <li class="flex items-start text-gray-700 dark:text-gray-300 font-medium">
                                                    <x-icon name="check"
                                                        class="w-4 h-4 rounded-sm text-emerald-500 mr-4 mt-0.5 flex-shrink-0" />
                                                    <span class="flex-1">{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-gray-550 dark:text-gray-400 italic">{{ __('Not specified') }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <!-- Excluded -->
                                <div
                                    class="w-full bg-rose-50/50 dark:bg-rose-950/20 rounded-[3px] p-8 border border-rose-100 dark:border-rose-900/20 shadow-sm overflow-hidden">
                                    <h3
                                        class="text-2xl font-bold text-gray-900 dark:text-white mb-8 font-brand flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-sm bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center mr-4">
                                            <x-icon name="x-mark" class="w-6 h-6 text-rose-600" />
                                        </div>{{ __('Excluded') }}</h3>
                                    <ul class="space-y-4">
                                        @if($tour->excluded_features && count($tour->excluded_features) > 0)
                                            @foreach($tour->excluded_features as $feature)
                                                <li class="flex items-start text-gray-700 dark:text-gray-300 font-medium">
                                                    <x-icon name="x-mark" class="w-4 h-4 text-rose-500 mr-4 mt-0.5 flex-shrink-0" />
                                                    <span class="flex-1">{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="text-gray-550 dark:text-gray-400 italic">{{ __('Not specified') }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- ── RIGHT SIDEBAR ── -->
                    <div class="tour-tab-right  w-full lg:w-1/4 ">
                        <div class="sticky top-[140px] space-y-6">
                            <div
                                class="bg-white dark:bg-gray-900 rounded-[3px] shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 dark:border-gray-800 overflow-hidden">
                                <!-- Price Header -->
                                <div
                                    class="bg-gray-900 dark:bg-gray-950 text-white p-8 text-center relative overflow-hidden">
                                    <div class="absolute inset-0 opacity-10"
                                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;">
                                    </div>
                                    <div class="relative z-10">
                                        <div class="text-xs font-bold uppercase tracking-widest text-brand-primary mb-3">{{ __('Price
                                            From') }}</div>
                                        <div class="flex items-baseline justify-center mb-2">
                                            <span class="text-3xl font-bold text-brand-primary mr-1">$</span>
                                            <span
                                                class="text-5xl font-black font-brand text-white">{{ number_format($tour->adult_price, 0) }}</span>
                                        </div>
                                        <div class="text-sm text-gray-450 font-medium">{{ __('per person sharing') }}</div>
                                        @if($tour->child_price)
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Children:
                                                ${{ number_format($tour->child_price, 0) }}/person</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-8 space-y-6">
                                    <!-- CTA Buttons -->
                                    <livewire:frontend.request-quote :tour="$tour" />
                                    <a href="{{ route('tours.index') }}"
                                        class="w-full py-3 px-6 border-2 border-gray-200 dark:border-gray-800 hover:border-brand-primary text-gray-655 dark:text-gray-300 hover:text-brand-primary text-center font-bold rounded-[3px] transition-all duration-200 flex items-center justify-center text-sm">
                                        ← Back to All Tours
                                    </a>
                                </div>
                            </div>

                            <!-- Trust Badges -->
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-white dark:bg-gray-900 rounded-[3px] p-4 text-center border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col items-center col-span-2">
                                    <x-icon name="device-phone-mobile" class="w-8 h-8 text-brand-primary mb-2" />
                                    <span
                                        class="!text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">24/7 Expert Support</span>
                                </div>
                            </div>
                        </div>
                    </div><!-- /right sidebar -->

                </div><!-- /relative -->

            </div> <!--end of Tab Panels -->
        </div>
    </div><!-- /tabs -->

@endsection