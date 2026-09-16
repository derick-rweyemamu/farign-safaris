@props(['tour'])

<div class="group bg-white dark:bg-gray-900 rounded-[3px] shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full">
    <!-- Image Header -->
    <div class="relative h-64 overflow-hidden">
        <a href="#" class="block w-full h-full">
            @if($tour->featured_image)
                <img src="{{ Storage::url($tour->featured_image) }}" alt="{{ $tour->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @else
                <img src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=600&q=80" alt="{{ $tour->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            @endif
        </a>
        
        <!-- Category Badge -->
        <div class="absolute top-4 left-4 z-10">
            <span class="bg-white/90 dark:bg-gray-950/90 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm uppercase tracking-wide border border-white/50 dark:border-gray-800/50">
                {{ $tour->category->name ?? 'Group Tour' }}
            </span>
        </div>
        
        <!-- Seats Badge -->
        @if($tour->available_seats)
        <div class="absolute top-4 right-4 z-10">
            <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                {{ $tour->available_seats }} Seats Left
            </span>
        </div>
        @endif
    </div>

    <!-- Content Body -->
    <div class="p-6 flex flex-col flex-grow">
        <!-- Duration & Dates -->
        <div class="flex items-center text-sm text-gray-550 dark:text-gray-400 mb-4 gap-4">
            @if($tour->start_date && $tour->end_date)
            <div class="flex items-center font-medium">
                <svg class="w-4 h-4 mr-1.5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $tour->start_date->format('M d, Y') }} - {{ $tour->end_date->format('M d, Y') }}
            </div>
            @endif
        </div>

        <!-- Title -->
        <a href="#" class="block mb-3">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white font-brand leading-snug group-hover:text-brand-primary transition-colors line-clamp-2">
                {{ $tour->title }}
            </h3>
        </a>

        <!-- Description (if available) -->
        <div class="flex-grow">
            @if($tour->description)<p class="text-gray-650 dark:text-gray-300 text-sm line-clamp-2 mb-2">
                {{ strip_tags($tour->description) }}
            </p>
            @endif
        </div>

        <!-- Footer: Price and Action -->
        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800 flex items-end justify-between">
            <div class="flex flex-col">
                <span class="text-[11px] text-gray-550 dark:text-gray-400 font-bold uppercase tracking-widest mb-1">{{ __('Price From') }}</span>
                <div class="flex items-baseline">
                    <span class="text-lg font-bold text-brand-primary mr-1">$</span>
                    <span class="text-3xl font-black text-gray-900 dark:text-white font-brand leading-none">{{ number_format($tour->price, 0) }}</span>
                </div>
            </div>
            
            <a href="#" class="inline-flex items-center justify-center bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:hover:text-gray-950 px-5 py-2.5 rounded-[3px] font-bold text-sm transition-all duration-300 group/btn">{{ __('Book Now') }}<svg class="w-4 h-4 ml-1.5 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</div>
