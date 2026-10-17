<div>
    @if(isset($isMultiCountry) && $isMultiCountry)
        {{-- SafariHQ Luxury Multi-Country Showcase ("Six Worlds. One Journey.") --}}
        <div class="safarihq-multi-country-showcase w-full text-[#e8eee9] font-sans antialiased relative">
            <!-- Ambient Luxury Lighting -->
            <div class="absolute -top-24 right-1/4 w-[500px] h-[500px] bg-[#223d2b]/25 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-10 w-96 h-96 bg-[#c9a063]/5 rounded-full blur-[120px] pointer-events-none"></div>

            <div class="w-full relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    
                    <!-- LEFT COLUMN: Headline & Vertical Country Selector -->
                    <div class="lg:col-span-5 flex flex-col justify-start">
                        <!-- Eyebrow -->
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-block w-8 h-[2px] bg-[#c9a063]"></span>
                            <span class="text-[11px] font-bold tracking-[0.22em] text-[#c9a063] uppercase">
                                {{ __('WHERE WILL YOU GO') }}
                            </span>
                        </div>

                        <!-- Main Title -->
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif text-white font-normal leading-[1.12] mb-4">
                            {!! $heading !!}
                            <br>
                            <em class="font-serif italic text-[#c9a063] font-normal">{!! $headingAccent !!}</em>
                        </h2>

                        <!-- Subtitle -->
                        <p class="text-sm sm:text-base text-[#8fa593] font-normal leading-relaxed mb-8 max-w-md">
                            {{ $subheading }}
                        </p>

                        <!-- Vertical Country Tabs Box (Framed Container) -->
                        <div class="border border-[#1e3024] rounded-xl overflow-hidden divide-y divide-[#1e3024] bg-[#0c150f]/80 shadow-2xl">
                            @foreach($countries as $index => $country)
                                @php
                                    $meta = $country->meta_data ?? [];
                                    $isActive = (int)$activeCountryId === (int)$country->id;
                                    $formattedIndex = sprintf('%02d', $index + 1);
                                @endphp
                                <button type="button"
                                        wire:click="setActiveCountry({{ $country->id }})"
                                        class="w-full text-left p-5 sm:p-6 transition-all duration-300 cursor-pointer group flex items-start gap-4 relative text-left block
                                               {{ $isActive 
                                                  ? 'bg-[#293d2f] text-white shadow-inner' 
                                                  : 'bg-transparent hover:bg-[#16271c] text-[#b3c7b8] hover:text-white' }}">
                                    
                                    <!-- Index Number (01, 02, etc.) -->
                                    <span class="font-mono text-xs sm:text-sm font-semibold tracking-wider pt-1 shrink-0
                                                {{ $isActive ? 'text-[#8ba893]' : 'text-[#4e6a56] group-hover:text-[#8ba893]' }}">
                                        {{ $formattedIndex }}
                                    </span>

                                    <!-- Country Name & Tagline -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-2xl sm:text-3xl font-serif font-normal tracking-tight mb-1 leading-tight
                                                   {{ $isActive ? 'text-white' : 'text-[#d6e4da] group-hover:text-white' }}">
                                            {{ $country->name }}
                                        </h3>
                                        <p class="text-xs sm:text-sm leading-relaxed truncate
                                                  {{ $isActive ? 'text-[#a9c3b0]' : 'text-[#6a8773] group-hover:text-[#8fa593]' }}">
                                            {{ $meta['tagline'] ?? '' }}
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Country Hero Banner & Featured Journeys -->
                    <div class="lg:col-span-7 flex flex-col"
                         wire:loading.class="opacity-60 transition-opacity duration-200"
                         wire:target="setActiveCountry">
                        
                        @if(isset($activeCountry) && $activeCountry)
                            <!-- FEATURED JOURNEYS -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-xs font-bold tracking-[0.2em] text-[#c9a063] uppercase">
                                        {{ __('FEATURED JOURNEYS') }}
                                    </span>
                                    @if(isset($tours) && $tours->isNotEmpty())
                                        <a href="/tours" class="text-xs font-semibold text-[#8fa593] hover:text-[#c9a063] transition flex items-center gap-1">
                                            {{ __('View All') }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </a>
                                    @endif
                                </div>

                                <!-- Tour Cards List -->
                                @if(isset($tours) && $tours->isNotEmpty())
                                    <div class="space-y-3" wire:key="country-tours-list-{{ $activeCountryId }}-{{ $tourPage }}">
                                        @foreach($tours as $tour)
                                            <a href="{{ route('tours.show', $tour->slug) }}"
                                               class="group bg-[#16271c] hover:bg-[#1e3325] border border-[#233829] hover:border-[#c9a063]/50 rounded-xl p-3.5 sm:p-4 flex items-center justify-between gap-4 transition-all duration-300 shadow-md hover:shadow-xl hover:-translate-y-0.5">
                                                
                                                <!-- Tour Thumbnail -->
                                                <div class="w-24 sm:w-28 h-16 sm:h-20 rounded-lg overflow-hidden shrink-0 bg-[#0c1610] border border-[#223829]">
                                                    <img src="{{ $tour->thumb_url }}" 
                                                         alt="{{ $tour->name }}" 
                                                         class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                                                </div>

                                                <!-- Tour Information -->
                                                <div class="flex-1 min-w-0">
                                                    <h4 class="text-base sm:text-lg font-serif font-normal text-white group-hover:text-[#c9a063] transition-colors leading-snug truncate">
                                                        {{ $tour->name }}
                                                    </h4>
                                                    <p class="text-xs sm:text-sm text-[#7f9c85] mt-1 truncate">
                                                        {{ $tour->highlights_text }}
                                                    </p>
                                                </div>

                                                <!-- Right Arrow -->
                                                <div class="shrink-0 text-[#c9a063] text-lg sm:text-xl pr-1 sm:pr-2 group-hover:translate-x-1.5 transition-transform duration-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                    </svg>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    <!-- Bottom Navigation: Previous and Next Arrows on Bottom Right -->
                                    <div class="flex items-center justify-end pt-4 mt-3 border-t border-[#1e3224]/80 gap-2">
                                        <!-- Previous Arrow Button -->
                                        <button type="button"
                                                wire:click="previousTourPage"
                                                @if(!$hasPrevPage) disabled @endif
                                                class="w-9 h-9 rounded-lg border border-[#233829] bg-[#16271c] hover:bg-[#223a2a] text-[#c9a063] disabled:opacity-25 disabled:hover:bg-[#16271c] disabled:cursor-not-allowed flex items-center justify-center transition duration-200 group cursor-pointer shadow-sm"
                                                title="{{ __('Previous') }}"
                                                aria-label="{{ __('Previous') }}">
                                            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Next Arrow Button -->
                                        <button type="button"
                                                wire:click="nextTourPage"
                                                @if(!$hasNextPage) disabled @endif
                                                class="w-9 h-9 rounded-lg border border-[#233829] bg-[#16271c] hover:bg-[#223a2a] text-[#c9a063] disabled:opacity-25 disabled:hover:bg-[#16271c] disabled:cursor-not-allowed flex items-center justify-center transition duration-200 group cursor-pointer shadow-sm"
                                                title="{{ __('Next') }}"
                                                aria-label="{{ __('Next') }}">
                                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <!-- Fallback when no tours are matched to this country yet -->
                                    <div class="bg-[#142319]/80 border border-dashed border-[#263e2e] rounded-xl p-8 text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#203728] flex items-center justify-center text-[#c9a063]">
                                            <i class="fa-solid fa-compass text-xl"></i>
                                        </div>
                                        <h4 class="text-lg font-serif text-white mb-2">
                                            {{ __('Bespoke :country Journeys', ['country' => $activeCountry->name]) }}
                                        </h4>
                                        <p class="text-xs sm:text-sm text-[#8fa593] max-w-md mx-auto mb-5 leading-relaxed">
                                            {{ __('We craft custom tailored safaris and expeditions across :country suited to your pace, preferences, and style.', ['country' => $activeCountry->name]) }}
                                        </p>
                                        <a href="/contact" 
                                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded bg-[#c9a063] hover:bg-[#b58e53] text-[#0c1610] text-xs font-bold tracking-wider uppercase transition shadow-md">
                                            <i class="fa-solid fa-paper-plane text-xs"></i>
                                            {{ __('Design a Custom Safari') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @else
        {{-- Standard Category Tabs & Tours Grid Layout --}}
        @if(isset($categories) && $categories->count() > 1)
            <!-- TABS -->
            <div class="max-w-7xl mx-auto mb-10 relative z-10">
                <nav class="flex overflow-x-auto w-full border-b-2 border-[#EEE5D4] dark:border-gray-800 md:[&::-webkit-scrollbar]:hidden md:[-ms-overflow-style:none] md:[scrollbar-width:none]">
                    @foreach($categories as $category)
                        <button wire:click="setActiveTab('{{ $category->slug }}')"
                            class="px-7 py-3 text-[13px] font-medium tracking-[0.06em] uppercase cursor-pointer border-b-2 -mb-[2px] transition-all duration-200 flex flex-shrink-0 items-center justify-center gap-2 bg-transparent whitespace-nowrap focus:outline-none 
                                    {{ $activeCategoryTab === $category->slug
                                        ? 'text-[#2E1F12] border-[#fbb158] dark:text-white dark:border-[#fbb158] bg-[#fbb158]/5 dark:bg-[#fbb158]/10'
                                        : 'text-[#8C7565] border-transparent hover:text-[#fbb158] hover:border-[#fbb158]/50 dark:text-gray-400 dark:hover:text-[#fbb158] hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <span class="text-[16px]">✦</span>
                            {{ $category->name }}
                            @php
                                $minPrice = $category->tours()->min('adult_price');
                            @endphp
                            @if($minPrice > 0)
                                <span class="{{ $activeCategoryTab === $category->slug ? 'bg-[rgba(201,168,76,0.18)] text-[#8B6914] dark:bg-[#fbb158]/20 dark:text-[#fbb158]' : 'bg-[#EEE5D4] text-[#8C7565] dark:bg-gray-800 dark:text-gray-450' }} text-[10px] px-2 py-0.5 rounded-full font-normal tracking-normal transition-colors duration-200">
                                    {{__('From')}} ${{ number_format($minPrice, 0) }}
                                </span>
                            @endif
                        </button>
                    @endforeach
                </nav>
            </div>
        @endif

        <div wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-200" wire:target="setActiveTab">
            @include('frontend.partials.tours-grid', ['tours' => $tours])
        </div>
    @endif
</div>