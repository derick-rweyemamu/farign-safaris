@extends('layouts.frontend')

@section('title', $destination->seo?->title ?? $destination->name . ' | ' . config('app.name', ' '))

@section('content')

    {{-- ── HERO ──────────────────────────────────────────────────────── --}}
    <div class="relative w-full h-[70vh] min-h-[520px] bg-gray-900 bg-cover bg-center overflow-hidden"
        style="background-image: url('{{ $destination->featured_image ? Storage::url($destination->featured_image) : '' }}')">

        {{-- Layered dark overlays for depth --}}
        <div class="absolute inset-0 bg-gray-950/50"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/50 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-gray-950/70 to-transparent"></div>

        {{-- Breadcrumb --}}
        <div class="absolute top-6 left-0 right-0 max-w-7xl px-6 md:px-16 mx-auto">
            <nav
                class="flex items-center gap-2 !text-xs text-gray-300 font-medium bg-black/30 w-fit px-4 py-2 rounded-sm backdrop-blur-sm">
                <a href="{{ route('home') }}" class="!text-xs hover:text-white transition-colors">{{ __('Home') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <a href="{{ route('frontend.destinations.index') }}"
                    class="!text-xs hover:text-white transition-colors">{{ __('Destinations') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <span class="!text-xs text-gray-400">{{ $destination->name }}</span>
            </nav>
        </div>

        {{-- Hero Text --}}
        <div class="absolute bottom-0 left-0 right-0 max-w-7xl p-8 md:p-16 mx-auto text-white z-10">
            {{-- Location badge --}}
            @if($destination->city)
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[3px] bg-brand-primary/20 text-brand-primary border border-brand-primary/30 uppercase tracking-widest text-xs font-bold mb-4">
                    <x-icon name="map-pin" class="w-3.5 h-3.5" />
                    {{ $destination->city->name }}@if($destination->city->country),
                    {{ $destination->city->country->name }}@endif
                </div>
            @endif

            <h1
                class="text-4xl md:text-5xl font-bold mb-4 font-brand leading-tight tracking-wide text-white drop-shadow-lg">
                {{ $destination->name }}
            </h1>

            @if($destination->tagline)
                <p class="text-lg md:text-xl text-gray-200 font-light italic max-w-2xl leading-relaxed">
                    "{{ $destination->tagline }}"
                </p>
            @endif

            {{-- Quick stats --}}
            <div class="flex flex-wrap items-center gap-3 mt-6">
                @if($destination->activities->count())<span
                        class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-[3px] text-sm font-medium text-gray-200">
                        <x-icon name="bolt" class="w-4 h-4 mr-2 text-brand-primary" />
                        {{ $destination->activities->count() }} {{ Str::plural('Activity', $destination->activities->count()) }}
                    </span>
                @endif
                @if($destination->animals->count())<span
                        class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-[3px] text-sm font-medium text-gray-200">
                        <x-icon name="heart" class="w-4 h-4 mr-2 text-brand-primary" />
                        {{ $destination->animals->count() }} {{ Str::plural('Animal', $destination->animals->count()) }}
                    </span>
                @endif
                @php $totalToursCount = $destination->tours_count ?? (isset($tours) ? $tours->total() : $destination->tours->count()); @endphp
                @if($totalToursCount > 0)<span
                        class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-[3px] text-sm font-medium text-gray-200">
                        <x-icon name="map-folded" class="w-4 h-4 mr-2 text-brand-primary" />
                        {{ $totalToursCount }} {{ Str::plural('Tour', $totalToursCount) }}
                    </span>
                @endif
            </div>
        </div>
    </div>{{-- /hero --}}


    {{-- ── SECTION 2: DESCRIPTION + GALLERY ──────────────────────────── --}}
    <section class="py-16 md:py-20 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-6 md:px-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start md:items-center">

                {{-- Left: Description --}}
                <div>
                    <span
                        class="inline-block text-xs font-bold uppercase tracking-widest text-brand-primary mb-3 border-b-2 border-brand-primary pb-1">{{ __('About
                                                                    this Destination') }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white font-brand leading-snug mb-6">
                        Discover {{ $destination->name }}
                    </h2>
                    @if($destination->description || $destination->builder_html)
                        <div
                            class="prose prose-lg max-w-none text-gray-600 dark:text-gray-300 prose-a:text-brand-primary leading-relaxed">
                            {!! $destination->getRenderedHtml() !!}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic text-lg">{{ __('No description available for this destination
                                                                                                                    yet.') }}
                        </p>
                    @endif
                </div>

                {{-- Right: Gallery Slider --}}
                <div>
                    @php $gallery = array_values($destination->gallery ?? []); @endphp
                    @if(count($gallery))
                        <div x-data="{
                                                                                                                                                                        current: 0,
                                                                                                                                                                        total: {{ count($gallery) }},
                                                                                                                                                                        lightbox: null,
                                                                                                                                                                        autoSlideInterval: null,
                                                                                                                                                                        startAutoSlide() {
                                                                                                                                                                            this.autoSlideInterval = setInterval(() => { this.next(); }, 4500);
                                                                                                                                                                        },
                                                                                                                                                                        stopAutoSlide() {
                                                                                                                                                                            clearInterval(this.autoSlideInterval);
                                                                                                                                                                        },
                                                                                                                                                                        prev() { this.current = (this.current - 1 + this.total) % this.total; },
                                                                                                                                                                        next() { this.current = (this.current + 1) % this.total; }
                                                                                                                                                                    }"
                            x-init="startAutoSlide()" @keydown.escape.window="lightbox = null" @mouseenter="stopAutoSlide()"
                            @mouseleave="startAutoSlide()"
                            class="relative w-full rounded-[3px] overflow-hidden shadow-xl border border-gray-100 dark:border-gray-800 group"
                            style="aspect-ratio: 4/3;">
                            {{-- Slides --}}
                            @foreach($gallery as $index => $img)
                                <div x-show="current === {{ $index }}" x-transition:enter="transition ease-in-out duration-500"
                                    x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in-out duration-300"
                                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute inset-0 cursor-pointer" @click="lightbox = '{{ Storage::url($img) }}'"
                                    style="display:none;">
                                    <img src="{{ Storage::url($img) }}" alt="{{ $destination->name }} — photo {{ $index + 1 }}"
                                        class="w-full h-full object-cover">
                                    {{-- Click hint overlay --}}
                                    <div
                                        class="absolute inset-0 bg-gray-950/0 group-hover:bg-gray-950/20 transition-all duration-300 flex items-end justify-end p-4">
                                        <span
                                            class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/50 text-white text-xs px-2 py-1 rounded backdrop-blur-sm">{{ __('Click to expand') }}</span>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Gradient overlays for buttons --}}
                            <div
                                class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-black/30 to-transparent pointer-events-none">
                            </div>
                            <div
                                class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-black/30 to-transparent pointer-events-none">
                            </div>

                            {{-- Prev button --}}
                            <button @click.stop="prev()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-sm bg-white/20 hover:bg-white/40 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white transition-all duration-200 hover:scale-110 focus:outline-none">
                                <x-icon name="chevron-left" class="w-4 h-4" />
                            </button>

                            {{-- Next button --}}
                            <button @click.stop="next()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-sm bg-white/20 hover:bg-white/40 backdrop-blur-sm border border-white/30 flex items-center justify-center text-white transition-all duration-200 hover:scale-110 focus:outline-none">
                                <x-icon name="chevron-right" class="w-4 h-4" />
                            </button>

                            {{-- Counter badge --}}
                            <div
                                class="absolute top-3 right-3 z-20 bg-black/50 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-sm">
                                <span x-text="current + 1"></span> / {{ count($gallery) }}
                            </div>

                            {{-- Dot indicators --}}
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                                @foreach($gallery as $index => $img)
                                    <button @click.stop="current = {{ $index }}"
                                        class="transition-all duration-300 rounded-sm focus:outline-none"
                                        :class="current === {{ $index }} ? 'w-5 h-2 bg-white' : 'w-2 h-2 bg-white/50 hover:bg-white/80'">
                                    </button>
                                @endforeach
                            </div>

                            {{-- Fullscreen Lightbox (inside same x-data scope) --}}
                            <div x-show="lightbox" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[999] bg-gray-950/95 flex items-center justify-center p-4"
                                @click.self="lightbox = null" style="display:none;">
                                <button @click="lightbox = null"
                                    class="absolute top-5 right-6 text-white/60 hover:text-white transition-colors focus:outline-none">
                                    <x-icon name="x-mark" class="w-9 h-9" />
                                </button>
                                <img :src="lightbox" class="max-h-[88vh] max-w-full rounded-[3px] shadow-2xl object-contain"
                                    alt="Gallery image">
                            </div>

                        </div>
                    @else
                        {{-- Fallback: just the featured image --}}
                        @if($destination->featured_image)
                            <div class="rounded-[3px] overflow-hidden shadow-xl" style="aspect-ratio: 4/3;">
                                <img src="{{ Storage::url($destination->featured_image) }}" alt="{{ $destination->name }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endif
                    @endif
                </div>


            </div>
        </div>
    </section>


    {{-- ── SECTION 3: ACTIVITIES ───────────────────────────────────────── --}}
    @if($destination->activities->count())
        <section class="py-16 md:py-20 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-6 md:px-16">
                {{-- Section header --}}
                <div class="text-center mb-12">
                    <span
                        class="inline-block text-xs font-bold uppercase tracking-widest text-brand-primary mb-3 border-b-2 border-brand-primary pb-1">{{ __('Things to Do') }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white font-brand">
                        Activities at {{ $destination->name }}
                    </h2>
                    <p class="mt-3 text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        {{ $destination->tagline }}
                    </p>
                </div>

                {{-- Activity cards --}}
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($destination->activities as $activity)
                        @php
                            // Extract first paragraph text cleanly
                            preg_match('/<p[^>]*>(.*?)<\/p>/is', $activity->description ?? '', $firstPMatch);
                            $rawSnippet = !empty($firstPMatch[1])
                                ? $firstPMatch[1]
                                : ($activity->description ?? '');
                            $snippet = Str::words(
                                html_entity_decode(strip_tags($rawSnippet), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                                25,
                                '...'
                            );
                        @endphp
                        <div x-data="{
                                                                                                                                                                    activityName: {{ Js::from($activity->name) }},
                                                                                                                                                                    activityContent: {{ Js::from($activity->description ?? '') }}
                                                                                                                                                                }"
                            class="w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)] group bg-white dark:bg-gray-800 rounded-[3px] border border-gray-100 dark:border-gray-700 p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col">
                            <h3
                                class="text-lg text-center font-bold text-gray-900 dark:text-white font-brand mb-3 group-hover:text-brand-primary transition-colors">
                                {{ $activity->name }}
                            </h3>
                            @if($activity->description)
                                <p class="text-gray-550 dark:text-gray-400 text-sm leading-relaxed flex-grow">
                                    {{ $snippet }}
                                </p>
                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button"
                                        @click="$dispatch('open-activity-modal', { name: activityName, content: activityContent })"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-primary-hover transition-colors group/btn">{{ __('Read More') }}<x-icon
                                            name="arrow-right"
                                            class="w-3.5 h-3.5 transform group-hover/btn:translate-x-1 transition-transform" />
                                    </button>
                                </div>
                            @else
                                <p class="text-gray-400 dark:text-gray-500 text-sm italic flex-grow">
                                    {{ __('An exciting activity awaiting you at this destination.') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    {{-- ── ATTRACTIONS CAROUSEL ─────────────────────────────────────────────── --}}
    @if($destination->attractions->count())
        <section class="py-16 md:py-24 bg-white dark:bg-gray-950 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 md:px-10 lg:px-16">
                {{-- Section header --}}
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white font-brand">
                        {{ ($destination->name) }} Attractions
                    </h2>
                </div>

                {{-- Alpine.js Carousel --}}
                <div x-data="{
                                                                    currentPage: 0,
                                                                    totalPages: 0,
                                                                    itemWidth: 0,
                                                                    init() {
                                                                        this.$nextTick(() => this.calculate());
                                                                        window.addEventListener('resize', () => this.calculate());
                                                                        this.$refs.slider.addEventListener('scroll', () => {
                                                                            if(this.itemWidth > 0) {
                                                                                this.currentPage = Math.round(this.$refs.slider.scrollLeft / this.itemWidth);
                                                                            }
                                                                        });
                                                                    },
                                                                    calculate() {
                                                                        const totalItems = {{ $destination->attractions->count() }};
                                                                        let itemsPerPage = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 768 ? 2 : 1);
                                                                        this.totalPages = Math.max(1, totalItems - itemsPerPage + 1);
                                                                        if (this.$refs.slider.children.length > 0) {
                                                                            const gap = 24; // 1.5rem (gap-6)
                                                                            this.itemWidth = this.$refs.slider.children[0].offsetWidth + gap;
                                                                        }
                                                                    },
                                                                    scrollNext() { this.$refs.slider.scrollBy({ left: this.itemWidth, behavior: 'smooth' }); },
                                                                    scrollPrev() { this.$refs.slider.scrollBy({ left: -this.itemWidth, behavior: 'smooth' }); },
                                                                    scrollToPage(index) { this.$refs.slider.scrollTo({ left: index * this.itemWidth, behavior: 'smooth' }); }
                                                                }" class="relative group">
                    <!-- Carousel Container -->
                    <div x-ref="slider"
                        class="flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-4 -mx-4 px-4 md:mx-0 md:px-0"
                        style="scrollbar-width: none; -ms-overflow-style: none;">
                        @foreach($destination->attractions->sortBy('sort_order') as $index => $attraction)
                            <div class="w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] flex-none snap-start relative">
                                <!-- Image -->
                                <div class="w-full h-[320px] overflow-hidden">
                                    @if($attraction->featured_image)
                                        <img src="{{ Storage::url($attraction->featured_image) }}"
                                            alt="{{ is_array($attraction->title) ? ($attraction->title['en'] ?? reset($attraction->title)) : $attraction->title }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                                            <x-icon name="photo" class="w-12 h-12 text-gray-400" />
                                        </div>
                                    @endif
                                </div>
                                <!-- Content Box -->
                                <div
                                    class="bg-[#F8F9FA] dark:bg-gray-900 mx-6 -mt-16 relative z-10 p-6 md:p-8 text-center h-[200px] flex flex-col justify-start shadow-sm hover:shadow-md transition-shadow">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white font-brand mb-3 tracking-wide">
                                        {{ is_array($attraction->title) ? ($attraction->title['en'] ?? reset($attraction->title)) : $attraction->title }}
                                    </h3>
                                    @if($attraction->content)
                                        <div class="text-gray-600 dark:text-gray-400 text-[13px] leading-[1.6] line-clamp-4 font-serif">
                                            {!! strip_tags(is_array($attraction->content) ? ($attraction->content['en'] ?? reset($attraction->content)) : $attraction->content) !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows -->
                    <button @click="scrollPrev" :disabled="currentPage === 0"
                        class="absolute left-2 top-[30%] -translate-y-1/2 w-8 h-8 rounded-full bg-white/70 hover:bg-white text-gray-800 flex items-center justify-center shadow backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-0 hidden md:flex z-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button @click="scrollNext" :disabled="currentPage >= totalPages - 1"
                        class="absolute right-2 top-[30%] -translate-y-1/2 w-8 h-8 rounded-full bg-white/70 hover:bg-white text-gray-800 flex items-center justify-center shadow backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-0 hidden md:flex z-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <!-- Pagination Dots -->
                    <div class="flex justify-center items-center gap-2 mt-8" x-show="totalPages > 1" style="display: none;">
                        <template x-for="(_, index) in totalPages" :key="index">
                            <button @click="scrollToPage(index)" class="h-1.5 transition-all duration-300 rounded-full"
                                :class="currentPage === index ? 'w-8 bg-[#fbb158]' : 'w-4 bg-[#F9E3C5] dark:bg-gray-700 hover:bg-[#fbb158]/50'">
                            </button>
                        </template>
                    </div>
                </div>

                <style>
                    .scrollbar-hide::-webkit-scrollbar {
                        display: none;
                    }
                </style>
            </div>
        </section>
    @endif


    {{-- ── SECTION 4: WILDLIFE / ANIMALS ──────────────────────────────── --}}
    @if($destination->animals->count())
        <section class="relative py-16 md:py-20 bg-brand-primary overflow-hidden">
            {{-- Subtle ambient layers for depth --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-transparent to-black/15 pointer-events-none"></div>
            <div class="absolute -top-24 -left-24 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-black/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-6 md:px-16 z-10">
                {{-- Section header --}}
                <div class="text-center mb-12">
                    <span
                        class="inline-block text-xs font-bold uppercase tracking-widest text-white/90 mb-3 border-b-2 border-white/40 pb-1">{{ __('Wildlife') }}</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white font-brand">
                        {{ __('Animals You Can Spot') }}
                    </h2>
                    <p class="mt-3 text-white/90 max-w-xl mx-auto text-base font-light">
                        {{ $destination->name }} is home to an incredible array of wildlife. Keep your eyes open for these
                        amazing creatures.
                    </p>
                </div>

                {{-- Animal grid: circular icon card + name + hover tooltip --}}
                <div class="flex flex-wrap justify-center gap-5">
                    @foreach($destination->animals as $animal)
                        <div
                            class="w-[140px] sm:w-[160px] lg:w-[200px] group relative text-center flex flex-col items-center justify-center h-full gap-3 p-5 bg-white dark:bg-gray-900 rounded-[3px] border border-white/20 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-help">

                            {{-- Icon pulled from the database --}}
                            @if($animal->icon)<img src="{{ Storage::url($animal->icon) }}" alt="{{ $animal->name }}"
                                class="w-14 h-14 object-contain transition-transform duration-500 group-hover:scale-110 flex-shrink-0">
                            @else
                                <x-icon name="heart" class="w-10 h-10 text-brand-primary" />
                            @endif

                            <span
                                class="text-sm font-semibold text-gray-800 dark:text-gray-100 font-brand group-hover:text-brand-primary transition-colors leading-snug">
                                {{ $animal->name }}
                            </span>

                            {{-- Hover Tooltip: full image_path photo --}}
                            <div
                                class="absolute bottom-full mb-3 left-1/2 -translate-x-1/2 w-48 bg-white dark:bg-gray-900 rounded-[3px] shadow-xl border border-gray-100 dark:border-gray-800 p-2 z-30 invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 pointer-events-none">
                                <div class="relative w-full h-36 rounded-[3px] overflow-hidden mb-2 bg-gray-100 dark:bg-gray-800">
                                    @if($animal->image_path)
                                        <img src="{{ Storage::url($animal->image_path) }}" alt="{{ $animal->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-brand-light text-brand-primary">
                                            <x-icon name="photo" class="w-8 h-8 opacity-60" />
                                        </div>
                                    @endif
                                </div>
                                <div class="text-center font-semibold text-xs text-gray-800 dark:text-white truncate px-1">
                                    {{ $animal->name }}
                                </div>
                                {{-- Tooltip arrow --}}
                                <div
                                    class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-white dark:border-t-gray-900">
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    {{-- ── SECTION 5: TOURS AT THIS DESTINATION (LIVEWIRE ASYNC) ─────────── --}}
    <livewire:frontend.destination-tours :destination-id="$destination->id" />


    {{-- ── CTA BANNER ──────────────────────────────────────────────────── --}}
    <section class="relative py-20 overflow-hidden bg-brand-primary">
        {{-- Subtle decorative ambient layers for depth --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black/10 via-transparent to-black/20 pointer-events-none"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-black/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto px-6 text-center z-10">
            <div
                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-sm bg-white/15 border border-white/25 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm shadow-sm">
                Ready to Visit {{ $destination->name }}?
            </div>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white font-brand mb-5 leading-tight drop-shadow-sm">
                {{ __('Your Safari Adventure Awaits') }}
            </h2>
            <p class="text-white/90 text-lg md:text-xl mb-8 leading-relaxed max-w-2xl mx-auto font-light">
                Let our experienced guides take you on an unforgettable journey through {{ $destination->name }}. Enquire
                today to start planning.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('tours.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-brand-primary font-bold rounded-[3px] hover:bg-white/95 transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-0.5 text-sm uppercase tracking-wide">
                    <x-icon name="map-folded" class="w-4 h-4 text-brand-primary" />
                    {{ __('Browse All Tours') }}
                </a>
                <a href="{{ route('frontend.destinations.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 border border-white/30 text-white font-bold rounded-[3px] hover:bg-white/20 transition-all duration-300 text-sm uppercase tracking-wide backdrop-blur-sm hover:-translate-y-0.5">
                    <x-icon name="map-pin" class="w-4 h-4" />
                    {{ __('More Destinations') }}
                </a>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* Prose overrides for dark mode */
        .dark .prose {
            color: #d1d5db;
        }

        .dark .prose h1,
        .dark .prose h2,
        .dark .prose h3 {
            color: #f9fafb;
        }

        .dark .prose strong {
            color: #f3f4f6;
        }

        .dark .prose a {
            color: var(--brand-primary);
        }
    </style>
@endpush