<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[1.2rem] max-w-7xl mx-auto">
    @forelse($tours as $tour)
        <div class="@if($loop->first) md:col-span-2 md:row-span-2 @endif">
            <x-trip-card :tour="$tour" />
        </div>
    @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-12 bg-gray-50 border border-dashed border-gray-300 rounded-[3px]">
            <p class="text-gray-550 italic">{{ __('No tours matching the selection were found.') }}</p>
        </div>
    @endforelse
</div>

@if($tours instanceof \Illuminate\Pagination\LengthAwarePaginator && $tours->hasPages())<div class="mt-8 flex justify-center">
        {{ $tours->links('vendor.livewire.frontend-tailwind') }}
    </div>
@endif
