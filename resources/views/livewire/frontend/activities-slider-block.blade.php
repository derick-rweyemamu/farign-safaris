@php
    $totalCount = $activities->count();
@endphp

<section class="safaris-activities-slider-section py-16 sm:py-24 relative overflow-hidden bg-white text-gray-900"
    x-data="{
        activeModal: null,
        currentIndex: 0,
        maxIndex: 0,
        itemWidth: 0,
        autoplayTimer: null,
        isHovered: false,
        totalItems: {{ $totalCount }},
        autoplayEnabled: {{ $autoplay ? 'true' : 'false' }},

        init() {
            this.$nextTick(() => {
                this.calculateMetrics();
                if (this.autoplayEnabled) {
                    this.startAutoplay();
                }
            });

            window.addEventListener('resize', () => {
                this.calculateMetrics();
            });

            if (this.$refs.track) {
                this.$refs.track.addEventListener('scroll', () => {
                    if (this.itemWidth > 0) {
                        const idx = Math.round(this.$refs.track.scrollLeft / this.itemWidth);
                        this.currentIndex = Math.max(0, Math.min(idx, this.maxIndex));
                    }
                }, { passive: true });
            }
        },

        calculateMetrics() {
            if (!this.$refs.track || this.$refs.track.children.length === 0) return;
            const firstChild = this.$refs.track.children[0];
            const gap = window.innerWidth >= 640 ? 24 : 16;
            this.itemWidth = firstChild.offsetWidth + gap;
            
            const visibleCount = window.innerWidth >= 1024 ? 3 : (window.innerWidth >= 640 ? 2 : 1);
            this.maxIndex = Math.max(0, this.totalItems - visibleCount);
        },

        scrollPrev() {
            if (!this.$refs.track) return;
            this.$refs.track.scrollBy({ left: -this.itemWidth, behavior: 'smooth' });
        },

        scrollNext() {
            if (!this.$refs.track) return;
            if (this.currentIndex >= this.maxIndex) {
                this.$refs.track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                this.$refs.track.scrollBy({ left: this.itemWidth, behavior: 'smooth' });
            }
        },

        scrollToIndex(idx) {
            if (!this.$refs.track) return;
            this.$refs.track.scrollTo({ left: idx * this.itemWidth, behavior: 'smooth' });
        },

        startAutoplay() {
            if (this.autoplayTimer) clearInterval(this.autoplayTimer);
            this.autoplayTimer = setInterval(() => {
                if (!this.isHovered && !this.activeModal && this.totalItems > 1) {
                    this.scrollNext();
                }
            }, 5500);
        },

        pauseAutoplay() {
            this.isHovered = true;
        },

        resumeAutoplay() {
            this.isHovered = false;
        },

        openActivityModal(activity) {
            this.activeModal = activity;
            document.body.style.overflow = 'hidden';
        },

        closeActivityModal() {
            this.activeModal = null;
            document.body.style.overflow = '';
        }
    }"
    @keydown.escape.window="closeActivityModal()"
    @mouseenter="pauseAutoplay()"
    @mouseleave="resumeAutoplay()">

    <!-- Ambient Subtle Warm Glow Spot -->
    <div class="pointer-events-none absolute -top-40 -right-40 w-96 h-96 bg-[#CA9A2E]/5 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- Top Header & Controls Row -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 sm:mb-14">
            <div class="max-w-2xl">
                <!-- Main Title & Accent -->
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold font-brand tracking-tight text-gray-900 leading-[1.12]">
                    {{ $heading ?: __('Curated Safari Activities') }}
                    <span class="font-serif italic font-normal text-[#CA9A2E] block sm:inline">
                        {{ $headingAccent ?: __('in the Wild') }}
                    </span>
                </h2>

                <!-- Subheading Description -->
                <p class="mt-4 text-sm sm:text-base text-gray-600 font-normal leading-relaxed max-w-xl">
                    {{ $subheading ?: __('Experience the wonder of Africa from unique perspectives. Each activity is carefully designed to immerse you deeply into the pristine wilderness.') }}
                </p>
            </div>

            <!-- Top Desktop Slider Navigation Controls -->
            @if($totalCount > 1)
                <div class="flex items-center gap-3 self-start md:self-end">
                    <!-- Previous Button -->
                    <button type="button"
                        @click="scrollPrev()"
                        class="w-11 h-11 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-800 hover:bg-[#CA9A2E] hover:text-white hover:border-[#CA9A2E] transition-all duration-300 shadow-sm disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                        title="{{ __('Previous Activity') }}"
                        aria-label="{{ __('Previous') }}">
                        <svg class="w-5 h-5 -translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Next Button -->
                    <button type="button"
                        @click="scrollNext()"
                        class="w-11 h-11 rounded-full flex items-center justify-center bg-white border border-gray-200 text-gray-800 hover:bg-[#CA9A2E] hover:text-white hover:border-[#CA9A2E] transition-all duration-300 shadow-sm disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                        title="{{ __('Next Activity') }}"
                        aria-label="{{ __('Next') }}">
                        <svg class="w-5 h-5 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        <!-- Slider Track Container -->
        @if($totalCount > 0)
            <div class="relative">
                <div x-ref="track"
                    class="flex gap-4 sm:gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-none pb-4 pt-2 -mx-4 px-4 sm:mx-0 sm:px-0"
                    style="scrollbar-width: none; -ms-overflow-style: none;">

                    @foreach($activities as $index => $activity)
                        @php
                            $imgSrc = $activity->image_url ?: 'https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=1200&q=80';
                            $activityTitle = $activity->name;
                            $activityDesc = $activity->description;
                            $plainDesc = strip_tags($activityDesc);
                        @endphp

                        <!-- Activity Card -->
                        <div class="w-[88%] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] flex-none snap-start relative group rounded-2xl sm:rounded-3xl overflow-hidden h-[500px] sm:h-[540px] md:h-[560px] flex flex-col justify-end p-6 sm:p-8 bg-black/60 border border-gray-200 hover:border-[#CA9A2E]/60 transition-all duration-500 cursor-pointer select-none"
                            @click="openActivityModal({{ json_encode([
                                'id' => $activity->id,
                                'title' => $activityTitle,
                                'image' => $imgSrc,
                                'description' => $activityDesc
                            ]) }})">

                            <!-- Background Image with Zoom on Hover -->
                            <div class="absolute inset-0 overflow-hidden z-0">
                                <img src="{{ $imgSrc }}"
                                    alt="{{ $activityTitle }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover transform transition-transform duration-700 ease-out group-hover:scale-110">
                            </div>

                            <!-- Gradient Overlays for Superb Readability -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/55 to-black/20 z-[1] transition-opacity duration-300"></div>
                            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-transparent z-[1]"></div>
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition-colors duration-500 z-[1]"></div>

                            <!-- Card Top Action Icon -->
                            <div class="absolute top-5 right-5 z-10">
                                <span class="w-8 h-8 rounded-full backdrop-blur-md bg-black/40 text-white flex items-center justify-center border border-white/20 group-hover:bg-[#CA9A2E] group-hover:text-black group-hover:border-[#CA9A2E] transition-all duration-300 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </span>
                            </div>

                            <!-- Card Bottom Content Box -->
                            <div class="relative z-10 flex flex-col justify-end">
                                <!-- Golden Accent Line -->
                                <div class="w-10 h-0.5 bg-[#CA9A2E] rounded-full group-hover:w-20 transition-all duration-500 mb-3.5 shadow-sm"></div>

                                <!-- Activity Title -->
                                <h3 class="text-2xl sm:text-[1.65rem] font-bold font-brand text-white group-hover:text-[#F6E27A] transition-colors duration-300 leading-snug line-clamp-2 drop-shadow-sm">
                                    {{ $activityTitle }}
                                </h3>

                                <!-- Activity Description -->
                                @if(!empty($plainDesc))
                                    <p class="mt-2.5 text-sm sm:text-[14px] text-gray-200/90 font-serif leading-relaxed line-clamp-3 group-hover:text-white transition-colors duration-300">
                                        {{ $plainDesc }}
                                    </p>
                                @endif

                                <!-- Action Link / Read More -->
                                <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#CA9A2E] group-hover:text-white transition-colors duration-300">
                                        <span>{{ __('Explore Details') }}</span>
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bottom Pagination Indicators -->
            @if($totalCount > 1)
                <div class="flex items-center justify-center gap-2 mt-8">
                    @for($i = 0; $i < $totalCount; $i++)
                        <button type="button"
                            @click="scrollToIndex({{ $i }})"
                            class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                            :class="currentIndex === {{ $i }} ? 'w-8 bg-[#CA9A2E]' : 'w-2.5 bg-black/20 hover:bg-black/40'"
                            aria-label="Go to slide {{ $i + 1 }}">
                        </button>
                    @endfor
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white/5 border border-dashed border-white/10 rounded-2xl p-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-base font-semibold text-gray-300">{{ __('No activities available yet') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('Add activities in the admin dashboard to showcase them here.') }}</p>
            </div>
        @endif

    </div>

    <!-- Quick Modal for Full Activity Story & Details -->
    <template x-if="activeModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8"
            style="backdrop-filter: blur(8px); background-color: rgba(0, 0, 0, 0.75);"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Modal Content Card -->
            <div class="relative w-full max-w-3xl bg-[#121c15] text-[#e8eee9] rounded-2xl sm:rounded-3xl border border-[#CA9A2E]/40 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col"
                @click.outside="closeActivityModal()"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                <!-- Close Button -->
                <button type="button"
                    @click="closeActivityModal()"
                    class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-black/60 hover:bg-[#CA9A2E] text-white hover:text-black border border-white/20 hover:border-[#CA9A2E] flex items-center justify-center transition-all duration-200 cursor-pointer"
                    title="{{ __('Close') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Modal Hero Header with Image -->
                <div class="relative h-64 sm:h-80 w-full overflow-hidden shrink-0">
                    <img :src="activeModal.image"
                        :alt="activeModal.title"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#121c15] via-[#121c15]/40 to-transparent"></div>

                    <!-- Floating Title -->
                    <div class="absolute bottom-4 left-6 sm:left-8 z-10">
                        <h3 class="text-2xl sm:text-3xl font-extrabold font-brand text-white leading-tight drop-shadow-md" x-text="activeModal.title"></h3>
                    </div>
                </div>

                <!-- Modal Body with Scroll -->
                <div class="p-6 sm:p-8 overflow-y-auto space-y-4 flex-1">
                    <div class="w-12 h-1 bg-[#CA9A2E] rounded-full"></div>
                    <div class="text-base sm:text-lg text-gray-200 font-serif leading-relaxed prose prose-invert max-w-none" x-html="activeModal.description"></div>
                </div>

                <!-- Modal Footer CTA -->
                <div class="p-5 sm:p-6 bg-black/40 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-gray-400">
                        {{ __('Want to include this activity in your custom itinerary?') }}
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button"
                            @click="closeActivityModal()"
                            class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl border border-white/20 text-xs font-bold text-gray-300 hover:text-white hover:bg-white/10 transition-colors">
                            {{ __('Close') }}
                        </button>
                        <a href="/request-quote"
                            class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-[#CA9A2E] hover:bg-[#b88523] text-black font-bold text-xs uppercase tracking-wider transition-all shadow-md text-center">
                            {{ __('Plan This Experience') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </template>
</section>
