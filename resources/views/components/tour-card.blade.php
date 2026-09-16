@props(['tour'])

<div
    class="group bg-white dark:bg-gray-900 rounded-[3px] shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
    <!-- Image Header -->
    <div class="relative h-64 overflow-hidden">
        <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}" class="block w-full h-full">
            @if($tour->featured_image)
                <img src="{{ Storage::url($tour->featured_image) }}" alt="{{ $tour->name }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @else
                <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=600&q=80"
                    alt="{{ $tour->name }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @endif
        </a>

        <!-- Category Badge -->
        <div class="absolute top-4 left-4 z-10">
            <span
                class="bg-white/90 dark:bg-gray-950/90 backdrop-blur-sm text-gray-900 dark:text-white !text-xs font-bold px-3 py-1.5 rounded-md shadow-sm uppercase tracking-wide border border-white/50 dark:border-gray-800/50">
                {{ auto_translate($tour->category->name ?? 'Adventure') }}
            </span>
        </div>
    </div>

    <!-- Content Body -->
    <div class="p-6 flex flex-col flex-grow">
        <!-- Duration & Location -->
        <div class="flex items-center text-sm text-gray-550 dark:text-gray-400 mb-4 gap-4">
            <div class="flex items-center font-medium">
                <x-icon name="clock" class="w-4 h-4 mr-1.5 text-brand-primary" />
                {{ $tour->duration_days }} Days
            </div>
            @if(isset($tour->destinations) && $tour->destinations->count() > 0)
                <div class="flex items-center font-medium truncate">
                    <x-icon name="map-pin" class="w-4 h-4 mr-1.5 text-brand-primary" />
                    <span class="truncate">{{ auto_translate($tour->destinations->first()->name ?? 'Multiple Places') }}</span>
                </div>
            @endif
        </div>

        <!-- Title -->
        <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}" class="block mb-3">
            <h3
                class="text-xl font-bold text-gray-900 dark:text-white font-brand leading-snug group-hover:text-brand-primary transition-colors line-clamp-2">
                {{ auto_translate($tour->name) }}
            </h3>
        </a>

        <!-- Description (if available) -->
        <div class="flex-grow">
            @if($tour->description)<p class="text-gray-650 dark:text-gray-300 text-sm line-clamp-2 mb-2">
                    {{ auto_translate(strip_tags($tour->description)) }}
                </p>
            @endif
        </div>

        <!-- Footer: Price and Action -->
        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800 flex items-end justify-between">
            <div class="flex flex-col">
                <span class="!text-xs">{{ __('From') }}</span>
                <div class="flex items-baseline">
                    <span class="text-3xl font-black text-gray-900 dark:text-white font-brand leading-none">$</span>
                    <span
                        class="text-3xl font-black text-gray-900 dark:text-white font-brand leading-none">{{ number_format($tour->adult_price, 0) }}</span>
                </div>
            </div>

            <a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}"
                class="inline-flex items-center justify-center bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:hover:text-gray-950 px-5 py-2.5 rounded-[3px] font-bold text-sm transition-all duration-300 group/btn">{{ __('View Tour') }}<x-icon name="arrow-right"
                    class="w-4 h-4 ml-1.5 transform group-hover/btn:translate-x-1 transition-transform" />
            </a>
        </div>
    </div>
</div>