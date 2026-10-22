<div class="bg-[#1c2e23] border border-white/10 hover:border-[#CA9A2E]/50 rounded-[10px] p-6 sm:p-7 flex flex-col justify-between h-full shadow-lg transition-all duration-300 relative group">
    <div>
        <!-- Card Top Bar: Platform & Rating -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                @if($review->platform === 'tripadvisor')
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#00AA6C]/15 border border-[#00AA6C]/30 text-[#00AA6C] text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="50" fill="#00AA6C"/>
                            <circle cx="50" cy="50" r="28" fill="white"/>
                            <circle cx="50" cy="50" r="16" fill="#00AA6C"/>
                            <circle cx="28" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
                            <circle cx="72" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
                            <circle cx="28" cy="50" r="5" fill="#00AA6C"/>
                            <circle cx="72" cy="50" r="5" fill="#00AA6C"/>
                        </svg>
                        <span>TripAdvisor</span>
                    </div>
                @elseif($review->platform === 'google')
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        <span>Google</span>
                    </div>
                @else
                    <div class="px-2.5 py-1 rounded-full bg-amber-600/20 text-amber-400 text-[11px] font-bold">
                        {{ $review->platform_label }}
                    </div>
                @endif
            </div>

            <!-- Stars / Rating -->
            <div class="flex items-center gap-1">
                @if($review->platform === 'tripadvisor')
                    <span class="text-[#00AA6C] text-sm tracking-widest font-black">●●●●●</span>
                @else
                    <span class="text-amber-400 text-sm tracking-tighter">★★★★★</span>
                @endif
            </div>
        </div>

        <!-- Headline -->
        @if($review->title)
            <h3 class="text-base sm:text-lg font-bold text-white mb-2 leading-snug group-hover:text-[#CA9A2E] transition-colors">
                "{{ $review->title }}"
            </h3>
        @endif

        <!-- Body -->
        <p class="text-sm text-gray-300 leading-relaxed font-light mb-6 line-clamp-5">
            {{ $review->content }}
        </p>
    </div>

    <!-- Reviewer Info -->
    <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-3 mt-auto">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#4E8243]/30 border border-[#4E8243]/40 text-[#CA9A2E] font-bold text-sm flex items-center justify-center flex-shrink-0">
                {{ $review->author_initials }}
            </div>
            <div>
                <p class="font-bold text-sm text-white">{{ $review->author_name }}</p>
                <p class="text-xs text-gray-400 flex items-center gap-1">
                    @if($review->author_location)
                        <span>{{ $review->author_location }}</span>
                        @if($review->trip_type || $review->review_date)
                            <span>·</span>
                        @endif
                    @endif
                    @if($review->trip_type)
                        <span>{{ $review->trip_type }}</span>
                    @elseif($review->review_date)
                        <span>{{ $review->review_date->format('M Y') }}</span>
                    @endif
                </p>
            </div>
        </div>

        @if($review->review_url)
            <a href="{{ $review->review_url }}" target="_blank" rel="noopener noreferrer"
                class="text-gray-400 hover:text-white transition-all p-1 hover:translate-x-0.5" title="{{ __('View on :p', ['p' => $review->platform_label]) }}">
                <x-icon name="arrow-right" class="w-4 h-4" />
            </a>
        @endif
    </div>
</div>
