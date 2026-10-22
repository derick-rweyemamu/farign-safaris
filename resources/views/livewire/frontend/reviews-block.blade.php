<section class="safaris-reviews-section relative py-16 sm:py-24 bg-[#16271c] text-white overflow-hidden select-none" id="reviews">
    <!-- Decorative Ambient Lights -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#4E8243]/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#CA9A2E]/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-12">
            <div class="max-w-2xl">
                @if($subheading)
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#CA9A2E]/15 border border-[#CA9A2E]/30 text-[#CA9A2E] text-xs font-bold tracking-widest uppercase mb-3">
                        <x-icon name="star" class="w-3.5 h-3.5" />
                        <span>{{ $subheading }}</span>
                    </div>
                @endif
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                    {!! nl2br(e($heading)) !!}
                </h2>
                @if($description)
                    <p class="text-gray-300 text-sm sm:text-base mt-3 leading-relaxed">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        @if($layout === 'slider')
            <!-- Slider Layout with Navigation Buttons -->
            <div class="relative" x-data="{
                scrollLeft() {
                    const el = this.$refs.track;
                    if (el) {
                        const card = el.firstElementChild;
                        const step = card ? card.offsetWidth + 24 : 360;
                        el.scrollBy({ left: -step, behavior: 'smooth' });
                    }
                },
                scrollRight() {
                    const el = this.$refs.track;
                    if (el) {
                        const card = el.firstElementChild;
                        const step = card ? card.offsetWidth + 24 : 360;
                        el.scrollBy({ left: step, behavior: 'smooth' });
                    }
                }
            }">
                <!-- Slider Nav Arrows -->
                <div class="hidden sm:flex items-center justify-end gap-2 mb-4">
                    <button @click="scrollLeft()"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 border border-white/20 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95"
                        title="{{ __('Previous') }}">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                    </button>
                    <button @click="scrollRight()"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/25 border border-white/20 text-white flex items-center justify-center transition-all hover:scale-105 active:scale-95"
                        title="{{ __('Next') }}">
                        <x-icon name="arrow-right" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Horizontal Scroll Track -->
                <div x-ref="track"
                    class="flex gap-6 overflow-x-auto scrollbar-none pb-6 pt-2 snap-x snap-mandatory"
                    style="scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach($reviews as $review)
                        <div class="snap-start flex-shrink-0 w-[300px] sm:w-[380px] lg:w-[400px]">
                            @include('livewire.frontend.partials.review-card', ['review' => $review])
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($reviews as $review)
                    @include('livewire.frontend.partials.review-card', ['review' => $review])
                @endforeach
            </div>
        @endif
    </div>
</section>
