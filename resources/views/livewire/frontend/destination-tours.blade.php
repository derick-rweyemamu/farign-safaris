<div>
    @if($destination && $tours->total() > 0)
        <section id="tours" class="py-16 md:py-20 bg-gray-50 dark:bg-gray-900 scroll-mt-20">
            <div class="max-w-7xl mx-auto px-6 md:px-16">
                {{-- Section header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-4">
                    <div>
                        <span
                            class="inline-block text-xs font-bold uppercase tracking-widest text-brand-primary mb-3 border-b-2 border-brand-primary pb-1">{{ __('Explore') }}</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white font-brand">
                            Tours in {{ $destination->name }}
                        </h2>
                        <p class="mt-3 text-gray-500 dark:text-gray-400 max-w-xl">
                            {{ __('Hand-curated safari experiences taking you through the heart of this incredible destination.') }}
                        </p>
                    </div>
                    <a href="{{ route('tours.index') }}"
                        class="inline-flex items-center justify-center bg-brand-light text-brand-primary hover:bg-brand-primary hover:text-white dark:hover:text-gray-950 px-5 py-2.5 rounded-[3px] font-bold text-sm transition-all duration-300 group/btn">{{ __('View all tours') }}<x-icon
                            name="arrow-right"
                            class="w-4 h-4 ml-1.5 transform group-hover/btn:translate-x-1 transition-transform" />
                    </a>
                </div>

                {{-- Tour cards list (3 at a time, horizontal layout) --}}
                <div class="flex flex-col gap-6 md:gap-8" wire:loading.class="opacity-60 transition-opacity">
                    @foreach($tours as $tour)
                        <div wire:key="tour-item-{{ $tour->id }}">
                            <x-tour-card-list :tour="$tour" />
                        </div>
                    @endforeach
                </div>

                {{-- Pagination if more than six tours --}}
                @if($tours->hasPages())
                    <div class="mt-12 flex justify-center">
                        {{ $tours->links('vendor.livewire.frontend-tailwind') }}
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
