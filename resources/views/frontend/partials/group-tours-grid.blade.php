<div
    class="w-full overflow-x-auto rounded-[3px] border border-gray-200 dark:border-gray-800 shadow-sm bg-white dark:bg-gray-950">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">
                    {{ __('Photo') }}</th>
                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Departure Date') }}</th>
                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Trip Name') }}</th>
                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Duration') }}</th>
                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Price') }}</th>
                <th class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ __('Availability') }}</th>
                <th
                    class="py-4 px-6 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">
                    {{ __('Book') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($groupTours as $tour)
                @php
                    $days = 0;
                    $nights = 0;
                    if ($tour->start_date && $tour->end_date) {
                        $days = $tour->start_date->diffInDays($tour->end_date) + 1;
                        $nights = max(0, $days - 1);
                    }

                    // Dynamic availability formatting
                    $availabilityColor = 'text-emerald-600 dark:text-emerald-400';
                    $availabilityText = 'Seats Available';

                    if ($tour->available_seats <= 3) {
                        $availabilityColor = 'text-rose-600 dark:text-rose-400';
                        $availabilityText = 'Seats Left';
                    } elseif ($tour->available_seats <= 7) {
                        $availabilityColor = 'text-amber-600 dark:text-amber-400';
                        $availabilityText = 'Seats Left';
                    }

                    // Dynamic subtitle text
                    $sub = 'Book early!';
                    $now = \Carbon\Carbon::now();
                    if ($tour->available_seats <= 3) {
                        $sub = 'Few seats left — reserve yours today!';
                    } elseif ($tour->available_seats <= 7) {
                        $sub = 'Limited — reserve early!';
                    } elseif ($tour->start_date && $tour->start_date->diffInDays($now) < 30) {
                        $sub = 'Departure soon! in ' . $tour->start_date->format('F');
                    }

                    // Resolve featured image — tour image first, then company fallback
                    $tourImage = null;
                    if (!empty($tour->featured_image)) {
                        $tourImage = asset('storage/' . ltrim($tour->featured_image, '/'));
                    } elseif (!empty($companySetting->tour_fallback_image ?? null)) {
                        $tourImage = asset('storage/' . ltrim($companySetting->tour_fallback_image, '/'));
                    }
                @endphp
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors align-middle">

                    <!-- Featured Image -->
                    <td class="py-3 px-4">
                        <div
                            class="w-16 h-14 rounded-[4px] overflow-hidden bg-gray-100 dark:bg-gray-800 shrink-0 border border-gray-200 dark:border-gray-700">
                            @if($tourImage)
                                <img src="{{ $tourImage }}" alt="{{ $tour->title }}" class="w-full h-full object-cover"
                                    loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </td>

                    <!-- Departure Date -->
                    <td class="py-5 px-6 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ $tour->start_date ? $tour->start_date->format('F d, Y') : 'Flexible' }}
                    </td>

                    <!-- Trip Name -->
                    <td class="py-5 px-6">
                        <a href="#quote-form"
                            class="text-sm font-semibold text-gray-900 dark:text-white hover:text-brand-primary transition-colors">
                            {{ $tour->title }}
                        </a>
                    </td>

                    <!-- Duration -->
                    <td class="py-5 px-6 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                        @if($days > 0)
                            {{ $days }} {{ __('Days') }} / {{ $nights }} {{ Str::plural(__('Night'), $nights) }}
                        @else
                            {{ __('N/A') }}
                        @endif
                    </td>

                    <!-- Price -->
                    <td class="py-5 px-6 whitespace-nowrap">
                        <div class="text-sm text-gray-950 dark:text-white">
                            <span class="text-base font-extrabold">${{ number_format($tour->price, 0) }}</span>
                            <span class="text-xs text-gray-500 font-medium">{{ __('Per Person') }}</span>
                        </div>
                        <div class="text-[11px] text-gray-400 dark:text-gray-500">{{ __('Based on 2+ trekkers') }}</div>
                    </td>

                    <!-- Availability -->
                    <td class="py-5 px-6 whitespace-nowrap">
                        <div class="text-sm font-bold {{ $availabilityColor }}">
                            {{ $tour->available_seats ?? 0 }} {{ $availabilityText }}
                        </div>
                        <div class="text-xs text-indigo-500/80 dark:text-indigo-400/80 font-medium">{{ $sub }}</div>
                    </td>

                    <!-- Book Button -->
                    <td class="py-5 px-6 text-center whitespace-nowrap">
                        <livewire:frontend.join-group :groupTour="$tour" :key="'join-group-' . $tour->id" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-12 text-center bg-gray-50 dark:bg-gray-900/30">
                        <p class="text-gray-500 dark:text-gray-400 italic">
                            {{ __('No upcoming group tours scheduled at the moment.') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($groupTours instanceof \Illuminate\Pagination\LengthAwarePaginator && $groupTours->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
            {{ $groupTours->links('vendor.livewire.frontend-tailwind') }}
        </div>
    @endif
</div>