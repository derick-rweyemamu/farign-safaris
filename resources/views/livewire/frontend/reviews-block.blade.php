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

            @if($showAddReview)
                <div class="flex items-center gap-3 shrink-0">
                    <button wire:click="openAddModal"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[3px] bg-[#CA9A2E] hover:bg-[#b58724] text-white font-bold text-xs sm:text-sm tracking-wide shadow-md transition-all hover:scale-105 active:scale-95"
                        title="{{ __('Share your safari feedback with us') }}">
                        <x-icon name="pencil-square" class="w-4 h-4" />
                        <span>{{ __('Write a Review') }}</span>
                    </button>
                </div>
            @endif
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

    <!-- Guest Add Review Modal -->
    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-xs"
            x-data @keydown.escape.window="$wire.closeAddModal()">
            <div class="bg-[#1c2e23] border border-white/15 text-white rounded-[4px] shadow-2xl max-w-lg w-full p-6 space-y-4 animate-scaleIn relative overflow-hidden select-text">
                <div class="flex items-center justify-between pb-3 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-[3px] bg-[#CA9A2E]/20 text-[#CA9A2E]">
                            <x-icon name="star" class="w-5 h-5" />
                        </span>
                        <h3 class="font-black text-lg text-white">{{ __('Share Your Safari Experience') }}</h3>
                    </div>
                    <button wire:click="closeAddModal" class="text-gray-400 hover:text-white transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                @if($submitSuccess)
                    <div class="p-4 rounded-[3px] bg-emerald-950/60 border border-emerald-500/40 text-emerald-200 text-center space-y-2 py-6">
                        <div class="w-12 h-12 rounded-full bg-emerald-600/30 border border-emerald-500/50 flex items-center justify-center mx-auto text-emerald-300">
                            <x-icon name="check" class="w-6 h-6" />
                        </div>
                        <h4 class="font-bold text-base text-white">{{ __('Thank You for Your Review!') }}</h4>
                        <p class="text-xs text-gray-300 max-w-sm mx-auto">
                            {{ __('Your review has been submitted successfully. It will appear on our website once reviewed by our team.') }}
                        </p>
                        <div class="pt-3">
                            <button wire:click="closeAddModal"
                                class="px-5 py-2 text-xs font-bold rounded-[3px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                                {{ __('Done') }}
                            </button>
                        </div>
                    </div>
                @else
                    <form wire:submit="submitReview" class="space-y-3.5 text-xs">
                        <!-- Rating Selector -->
                        <div>
                            <label class="block font-bold text-gray-300 mb-1.5">{{ __('Your Rating') }} *</label>
                            <div class="flex items-center gap-2" x-data="{ r: @entangle('newRating') }">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button type="button" @click="r = star"
                                        class="text-2xl transition-transform hover:scale-125 focus:outline-none"
                                        :class="star <= r ? 'text-[#FBBC05]' : 'text-gray-600'">
                                        ★
                                    </button>
                                </template>
                                <span class="text-xs font-semibold text-gray-300 ml-2" x-text="r + ' / 5 Stars'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-300 mb-1">{{ __('Your Name') }} *</label>
                                <input type="text" wire:model="newAuthorName"
                                    placeholder="e.g. John & Sarah"
                                    class="w-full px-3 py-2 bg-white/5 border border-white/15 rounded-[3px] text-white placeholder-gray-500 focus:outline-none focus:border-[#CA9A2E]" />
                                @error('newAuthorName') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block font-bold text-gray-300 mb-1">{{ __('Country / Origin') }}</label>
                                <input type="text" wire:model="newAuthorLocation"
                                    placeholder="e.g. London, UK"
                                    class="w-full px-3 py-2 bg-white/5 border border-white/15 rounded-[3px] text-white placeholder-gray-500 focus:outline-none focus:border-[#CA9A2E]" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-gray-300 mb-1">{{ __('Tour / Safari Type') }}</label>
                                <input type="text" wire:model="newTripType"
                                    placeholder="e.g. 7-Day Serengeti & Ngorongoro"
                                    class="w-full px-3 py-2 bg-white/5 border border-white/15 rounded-[3px] text-white placeholder-gray-500 focus:outline-none focus:border-[#CA9A2E]" />
                            </div>

                            <div>
                                <label class="block font-bold text-gray-300 mb-1">{{ __('Review Headline') }}</label>
                                <input type="text" wire:model="newTitle"
                                    placeholder="e.g. Unforgettable Safari Experience"
                                    class="w-full px-3 py-2 bg-white/5 border border-white/15 rounded-[3px] text-white placeholder-gray-500 focus:outline-none focus:border-[#CA9A2E]" />
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-300 mb-1">{{ __('Your Review / Feedback') }} *</label>
                            <textarea wire:model="newContent" rows="4"
                                placeholder="{{ __('Tell future travellers about your safari guide, wildlife sightings, lodges, and overall experience...') }}"
                                class="w-full px-3 py-2 bg-white/5 border border-white/15 rounded-[3px] text-white placeholder-gray-500 focus:outline-none focus:border-[#CA9A2E]"></textarea>
                            @error('newContent') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-3 border-t border-white/10">
                            <button type="button" wire:click="closeAddModal"
                                class="px-4 py-2 font-semibold rounded-[3px] border border-white/20 text-gray-300 hover:bg-white/10">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-5 py-2 font-bold rounded-[3px] bg-[#CA9A2E] hover:bg-[#b58724] text-white shadow-md disabled:opacity-50">
                                <span wire:loading wire:target="submitReview">⌛</span>
                                <x-icon wire:loading.remove wire:target="submitReview" name="paper-airplane" class="w-3.5 h-3.5" />
                                <span>{{ __('Submit Review') }}</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
</section>
