@php
    $totalCount = $tours->count();
    $bgUrl = $bgImage ? (str_starts_with($bgImage, 'http') || str_starts_with($bgImage, '/') ? $bgImage : \Illuminate\Support\Facades\Storage::url($bgImage)) : asset('images/mega-menu/kilimanjaro-summit.jpg');
@endphp

<section id="trekking" class="safaris-trekking-slider-section relative py-16 sm:py-20 lg:py-24 bg-[#F8F9FA] overflow-hidden select-none"
    style="font-family: 'Instrument Sans', 'Montserrat', sans-serif;"
    x-data="{
        currentIndex: 0,
        autoplayTimer: null,
        isHovered: false,
        totalItems: {{ $totalCount }},
        autoplayEnabled: {{ $autoplay ? 'true' : 'false' }},

        init() {
            this.$nextTick(() => {
                if (this.autoplayEnabled) {
                    this.startAutoplay();
                }
            });

            if (this.$refs.track) {
                this.$refs.track.addEventListener('scroll', () => {
                    const el = this.$refs.track;
                    const card = el.querySelector('.trekking-card') || el.firstElementChild;
                    if (card) {
                        const step = card.offsetWidth + 24;
                        this.currentIndex = Math.max(0, Math.round(el.scrollLeft / step));
                    }
                }, { passive: true });
            }
        },

        scrollPrev() {
            const el = this.$refs.track || document.getElementById('trekking-slider-track');
            if (!el) return;
            const card = el.querySelector('.trekking-card') || el.firstElementChild;
            const step = card ? (card.offsetWidth + 24) : 320;
            
            if (el.scrollLeft <= 15) {
                el.scrollTo({ left: el.scrollWidth, behavior: 'smooth' });
            } else {
                el.scrollBy({ left: -step, behavior: 'smooth' });
            }
        },

        scrollNext() {
            const el = this.$refs.track || document.getElementById('trekking-slider-track');
            if (!el) return;
            const card = el.querySelector('.trekking-card') || el.firstElementChild;
            const step = card ? (card.offsetWidth + 24) : 320;
            
            const maxScroll = el.scrollWidth - el.clientWidth;
            if (el.scrollLeft >= maxScroll - 15) {
                el.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                el.scrollBy({ left: step, behavior: 'smooth' });
            }
        },

        scrollToIndex(idx) {
            const el = this.$refs.track || document.getElementById('trekking-slider-track');
            if (!el) return;
            const card = el.querySelector('.trekking-card') || el.firstElementChild;
            const step = card ? (card.offsetWidth + 24) : 320;
            el.scrollTo({ left: idx * step, behavior: 'smooth' });
        },

        startAutoplay() {
            if (this.autoplayTimer) clearInterval(this.autoplayTimer);
            this.autoplayTimer = setInterval(() => {
                if (!this.isHovered && this.totalItems > 1) {
                    this.scrollNext();
                }
            }, 6000);
        },

        pauseAutoplay() {
            this.isHovered = true;
        },

        resumeAutoplay() {
            this.isHovered = false;
        }
    }"
    x-on:mouseenter="pauseAutoplay()"
    x-on:mouseleave="resumeAutoplay()">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alex+Brush&display=swap');
        .trekking-cursive {
            font-family: 'Alex Brush', 'Playfair Display', cursive, serif !important;
        }
        .trekking-no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }
        .trekking-no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>

    {{-- Scenic Kilimanjaro Mountain Background - Takes strictly 40% from the right edge on desktop --}}
    <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-full lg:w-[40%] bg-cover bg-no-repeat bg-right-top transition-opacity duration-700 opacity-90 lg:opacity-100"
             style="background-image: url('{{ $bgUrl }}');">
            {{-- Smooth subtle gradient fade along the left edge of the 40% container --}}
            <div class="absolute inset-0 hidden lg:block"
                 style="background: linear-gradient(90deg, #F8F9FA 0%, rgba(248, 249, 250, 0.65) 20%, transparent 55%);"></div>
            <div class="absolute inset-0 block lg:hidden"
                 style="background: linear-gradient(180deg, #F8F9FA 0%, rgba(248, 249, 250, 0.85) 30%, rgba(248, 249, 250, 0.25) 100%);"></div>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
            
            {{-- LEFT COLUMN: Headings & Brand Narrative (Editable with Tailwind Classes) --}}
            <div class="lg:col-span-5 flex flex-col justify-center pr-0 lg:pr-6 relative z-20">
                
                {{-- Green Subtitle Badge / Tagline --}}
                <div class="mb-3">
                    <span class="trekking-badge inline-block {{ $subheadingClass ?: 'text-[#4E8243] font-serif font-bold text-lg sm:text-xl tracking-tight' }}">
                        {{ $subheadingTag }}
                    </span>
                </div>

                {{-- Stacked Main Headline --}}
                <h2 class="trekking-heading text-3xl sm:text-4xl lg:text-[2.85rem] font-serif font-extrabold text-gray-900 leading-[1.12] mb-5 tracking-tight">
                    <span class="trekking-heading-prefix block {{ $headingPrefixClass ?: 'text-gray-900 font-extrabold' }}">{{ $headingPrefix }}</span>
                    <span class="trekking-heading-accent block {{ $headingAccentClass ?: 'text-[#8FA5B5] font-bold' }}">{{ $headingAccent }}</span>
                    <span class="trekking-heading-suffix block {{ $headingSuffixClass ?: 'text-gray-900 font-extrabold' }}">{{ $headingSuffix }}</span>
                </h2>

                {{-- Description Paragraph --}}
                <p class="trekking-desc {{ $descriptionClass ?: 'text-gray-600 text-sm sm:text-base leading-relaxed mb-6 font-normal max-w-lg' }}">
                    {{ $resolvedDescription }}
                </p>

                {{-- Elegant Calligraphy Script Line --}}
                <div class="trekking-cursive {{ $cursiveClass ?: 'text-2xl sm:text-3xl text-gray-600 tracking-wide select-none pt-2 mb-6' }}" style="line-height: 1.3;">
                    {{ $cursiveText }}
                </div>

                {{-- Navigation Buttons & Indicators --}}
                <div class="flex items-center gap-3 pt-2 relative z-30">
                    {{-- Previous Button --}}
                    <button type="button" 
                            x-on:click.stop="scrollPrev()" 
                            onclick="const el = document.getElementById('trekking-slider-track') || this.closest('section')?.querySelector('[x-ref=track]'); if (el) { const card = el.querySelector('.trekking-card') || el.firstElementChild; const step = card ? (card.offsetWidth + 24) : 320; if (el.scrollLeft <= 15) { el.scrollTo({ left: el.scrollWidth, behavior: 'smooth' }); } else { el.scrollBy({ left: -step, behavior: 'smooth' }); } }"
                            class="relative z-30 pointer-events-auto w-11 h-11 rounded-full border border-gray-300 bg-white/95 text-gray-700 flex items-center justify-center transition-all duration-200 cursor-pointer hover:bg-gray-900 hover:text-white hover:border-gray-900 shadow-sm active:scale-90 shrink-0"
                            aria-label="{{ __('Previous Tour') }}">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Next Button --}}
                    <button type="button" 
                            x-on:click.stop="scrollNext()" 
                            onclick="const el = document.getElementById('trekking-slider-track') || this.closest('section')?.querySelector('[x-ref=track]'); if (el) { const card = el.querySelector('.trekking-card') || el.firstElementChild; const step = card ? (card.offsetWidth + 24) : 320; const max = el.scrollWidth - el.clientWidth; if (el.scrollLeft >= max - 15) { el.scrollTo({ left: 0, behavior: 'smooth' }); } else { el.scrollBy({ left: step, behavior: 'smooth' }); } }"
                            class="relative z-30 pointer-events-auto w-11 h-11 rounded-full border border-gray-300 bg-white/95 text-gray-700 flex items-center justify-center transition-all duration-200 cursor-pointer hover:bg-gray-900 hover:text-white hover:border-gray-900 shadow-sm active:scale-90 shrink-0"
                            aria-label="{{ __('Next Tour') }}">
                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Small Pagination Dots --}}
                    @if($totalCount > 1)
                        <div class="flex items-center gap-1.5 ml-3">
                            <template x-for="i in Math.min(totalItems, 6)" :key="i">
                                <button type="button" 
                                        x-on:click="scrollToIndex(i - 1)"
                                        :data-idx="i - 1"
                                        onclick="const el = document.getElementById('trekking-slider-track'); if (el) { const card = el.querySelector('.trekking-card') || el.firstElementChild; const step = card ? (card.offsetWidth + 24) : 320; el.scrollTo({ left: (parseInt(this.getAttribute('data-idx') || 0) * step), behavior: 'smooth' }); }"
                                        class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                        :class="(currentIndex === (i - 1)) ? 'w-6 bg-[#4E8243]' : 'w-1.5 bg-gray-300 hover:bg-gray-400'"></button>
                            </template>
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT COLUMN: Dynamic Trekking Tours Slider --}}
            <div class="lg:col-span-7 overflow-hidden">
                @if($tours->isNotEmpty())
                    <div x-ref="track" 
                         id="trekking-slider-track" 
                         class="trekking-no-scrollbar flex gap-5 sm:gap-6 overflow-x-auto pb-4 pt-2 snap-x snap-mandatory"
                         style="-webkit-overflow-scrolling: touch;">
                        
                        @foreach($tours as $tour)
                            @php
                                $img = null;
                                if ($tour->featured_image) {
                                    $img = str_starts_with($tour->featured_image, 'http') || str_starts_with($tour->featured_image, '/')
                                        ? $tour->featured_image
                                        : \Illuminate\Support\Facades\Storage::url($tour->featured_image);
                                } elseif ($tour->gallery && is_array($tour->gallery) && count($tour->gallery) > 0) {
                                    $firstGal = $tour->gallery[0];
                                    $img = str_starts_with($firstGal, 'http') || str_starts_with($firstGal, '/') ? $firstGal : \Illuminate\Support\Facades\Storage::url($firstGal);
                                }
                                if (!$img) {
                                    $img = 'https://images.unsplash.com/photo-1589553460732-57ef51160601?auto=format&fit=crop&w=800&q=80';
                                }

                                $tourUrl = route('tours.show', $tour->slug);
                                $days = $tour->duration_days ?: 1;
                            @endphp

                            <a href="{{ $tourUrl }}" 
                               class="trekking-card group flex-shrink-0 w-[260px] sm:w-[280px] md:w-[300px] bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] border border-gray-100/90 transition-all duration-300 flex flex-col overflow-hidden snap-start hover:-translate-y-1 block text-left"
                               title="{{ $tour->name }}">
                                
                                {{-- Card Image --}}
                                <div class="relative aspect-[16/11] overflow-hidden bg-gray-100">
                                    <img src="{{ $img }}" 
                                         alt="{{ $tour->name }}" 
                                         loading="lazy"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>

                                {{-- Card Content --}}
                                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between bg-white">
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-sm sm:text-[15px] leading-snug line-clamp-2 group-hover:text-[#4E8243] transition-colors mb-3 min-h-[2.5rem]">
                                            {{ $tour->name }}
                                        </h3>
                                    </div>

                                    <div>
                                        {{-- Thin Divider --}}
                                        <div class="border-t border-gray-100 my-3"></div>

                                        {{-- Meta Row: Days & Price --}}
                                        <div class="flex items-center justify-between text-xs sm:text-sm">
                                            {{-- Duration with Clock Icon --}}
                                            <div class="flex items-center gap-1.5 text-gray-500 font-medium">
                                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $days }} {{ Str::plural('Day', $days) }}</span>
                                            </div>

                                            {{-- Price in Amber / Gold --}}
                                            <div class="text-[#D97706] font-bold text-base sm:text-lg tracking-tight">
                                                @if($tour->adult_price > 0)
                                                    ${{ number_format($tour->adult_price, 0) }}
                                                @else
                                                    <span class="text-xs text-gray-400 font-semibold">{{ __('Inquire') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach

                    </div>
                @else
                    <div class="p-8 bg-white/80 rounded-2xl border border-gray-200 text-center">
                        <p class="text-gray-500">{{ __('No trekking tours available at the moment.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
