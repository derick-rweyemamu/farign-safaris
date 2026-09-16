@props(['tour'])

<a href="{{ route('tours.show', $tour->slug ?? $tour->id ?? $tour) }}" class="trip-card group relative rounded-[3px] overflow-hidden cursor-pointer no-underline block" style="aspect-ratio: 4/3;">
    @if($tour->featured_image)
        <img class="trip-img w-full h-full min-h-[220px] object-cover block transition-transform duration-600 ease-in-out group-hover:scale-105" src="{{ Storage::url($tour->featured_image) }}" alt="{{ $tour->name }}">
    @else
        <img class="trip-img w-full h-full min-h-[220px] object-cover block transition-transform duration-600 ease-in-out group-hover:scale-105" src="https://images.unsplash.com/photo-1516426122078-c23e76319801?auto=format&fit=crop&w=600&q=80" alt="{{ $tour->name }}">
    @endif
    
    <div class="trip-overlay absolute inset-0 flex flex-col justify-end p-[1.4rem]" style="background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.45) 60%, rgba(0,0,0,0.1) 100%);">
        @if($tour->category)<span class="trip-tag text-[10px] tracking-[0.12em] uppercase py-[3px] px-[10px] rounded-[2px] inline-block mb-[6px] w-fit border border-solid bg-[rgba(201,168,76,0.22)] border-[rgba(201,168,76,0.5)] text-[#E8D5A3]">
                {{ auto_translate($tour->category->name) }}
            </span>
        @endif
        
        <div class="trip-name font-display text-[1.15rem] font-medium text-white leading-[1.25] mb-[6px] drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] line-clamp-2">
            {{ auto_translate($tour->name) }}
        </div>
        
        <div class="trip-from text-[12px] text-[rgba(255,255,255,0.65)] font-light">{{ __('From') }}<strong class="text-[#E8D5A3] font-medium">${{ number_format($tour->adult_price, 0) }}</strong> &middot; {{ $tour->duration_days }} Days
        </div>
    </div>
</a>
