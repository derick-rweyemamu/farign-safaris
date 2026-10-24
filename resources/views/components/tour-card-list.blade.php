@props(['tour'])

<div class="group bg-white dark:bg-gray-900 rounded-[3px] shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden transition-all duration-300 transform hover:-translate-y-0.5 flex flex-col md:flex-row items-stretch"
    style="display: flex;">
    <!-- Image on Left (Full Height & Width) -->
    <div class="relative w-full md:w-80 lg:w-[380px] xl:w-[420px] shrink-0 h-64 sm:h-72 md:h-auto self-stretch overflow-hidden"
        style="position: relative; align-self: stretch;">
        <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}"
            class="block w-full h-full absolute inset-0"
            style="position: absolute; inset: 0; width: 100%; height: 100%; display: block;">
            @if($tour->featured_image)
                <img src="{{ Storage::url($tour->featured_image) }}" alt="{{ auto_translate($tour->name) }}"
                    onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80'"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @else
                <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=800&q=80"
                    alt="{{ auto_translate($tour->name) }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                    style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent md:hidden"
                style="position: absolute; inset: 0;"></div>
        </a>

        <!-- Category Badge -->
        @if($tour->category)
            <div class="absolute top-4 left-4 z-10 pointer-events-none"
                style="position: absolute; top: 1rem; left: 1rem; z-index: 10;">
                <span
                    class="bg-white/95 dark:bg-gray-950/90 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-bold px-3 py-1.5 rounded-[3px] shadow-sm uppercase tracking-wider border border-white/40 dark:border-gray-800">
                    {{ auto_translate($tour->category->name) }}
                </span>
            </div>
        @endif

        <!-- Duration Overlay -->
        <div class="absolute bottom-4 left-4 z-10 pointer-events-none"
            style="position: absolute; bottom: 1rem; left: 1rem; z-index: 10;">
            <span
                class="inline-flex items-center gap-1.5 bg-gray-950/85 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-[3px] shadow">
                <x-icon name="clock" class="w-3.5 h-3.5 text-brand-primary" />
                <span>{{ $tour->duration_days }} {{ __('Days') }}@if($tour->duration_nights) /
                {{ $tour->duration_nights }} {{ __('Nights') }}@endif</span>
            </span>
        </div>
    </div>

    <!-- Content on Right -->
    <div class="p-6 sm:p-7 md:p-8 flex flex-col justify-between flex-grow min-w-0">
        <div>
            <!-- Tour Title -->
            <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}" class="block group/title mb-3">
                <h3
                    class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white font-brand leading-snug group-hover/title:text-brand-primary transition-colors line-clamp-2">
                    {{ auto_translate($tour->name) }}
                </h3>
            </a>

            <!-- Description / Overview -->
            @if($tour->overview || $tour->description)
                <p
                    class="text-gray-650 dark:text-gray-300 text-sm sm:text-base leading-relaxed line-clamp-2 md:line-clamp-3 mb-4">
                    {{ auto_translate(strip_tags($tour->overview ?: $tour->description)) }}
                </p>
            @endif


        </div>

        <!-- Footer: Pricing & Action Button -->
        <div
            class="pt-5 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-auto">
            <div class="flex items-baseline gap-2">
                <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">{{ __('From') }}</span>
                <div class="flex items-baseline">
                    <span class="text-3xl font-black text-gray-900 dark:text-white font-brand leading-none">$</span>
                    <span
                        class="text-3xl font-black text-gray-900 dark:text-white font-brand leading-none">{{ number_format($tour->adult_price, 0) }}</span>
                </div>
                <span class="text-xs text-gray-500 font-medium">/ {{ __('person') }}</span>
            </div>

            <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}"
                class="inline-flex items-center justify-center bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:hover:text-gray-950 px-6 py-3 rounded-[3px] font-bold text-sm tracking-wide transition-all duration-300 group/btn shadow-sm hover:shadow">
                <span>{{ __('View Tour') }}</span>
                <x-icon name="arrow-right"
                    class="w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform" />
            </a>
        </div>
    </div>
</div>