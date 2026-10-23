<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                <x-icon name="star" class="w-7 h-7 text-amber-500" />
                {{ __('Reviews & Testimonials') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ __('Curate and manage traveler reviews from TripAdvisor, Google, SafariBookings, and direct submissions.') }}
            </p>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3">
            <button wire:click="openLinksModal"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold rounded-[3px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 shadow-xs transition-all"
                title="{{ __('Configure TripAdvisor, Google & SafariBookings profile links') }}">
                <x-icon name="link" class="w-4 h-4 text-amber-500" />
                <span class="hidden sm:inline">{{ __('Platform Links') }}</span>
            </button>
            <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-[3px] bg-[#455537] hover:bg-[#354329] text-white shadow-sm transition-all">
                <x-icon name="plus" class="w-4 h-4" />
                {{ __('Add Review') }}
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
        <!-- Total Reviews -->
        <div class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Total') }}</span>
                <span class="p-1.5 rounded-[3px] bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                    <x-icon name="chat-bubble-bottom-center-text" class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Overall collected') }}</p>
        </div>

        <!-- Published (On Website) -->
        <div class="bg-card rounded-[3px] p-4 border border-emerald-100 dark:border-emerald-950/40 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('On Website') }}</span>
                <span class="p-1.5 rounded-[3px] bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <x-icon name="check-circle" class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">{{ $stats['published'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Active in builder') }}</p>
        </div>

        <!-- Featured (Top Picks) -->
        <div class="bg-card rounded-[3px] p-4 border border-amber-100 dark:border-amber-950/40 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ __('Featured') }}</span>
                <span class="p-1.5 rounded-[3px] bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                    <x-icon name="star" class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-2">{{ $stats['featured'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Pinned to top') }}</p>
        </div>

        <!-- TripAdvisor -->
        <div class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#00AA6C] uppercase tracking-wider">{{ __('TripAdvisor') }}</span>
                <span class="text-xs font-black text-[#00AA6C]">TA</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $stats['tripadvisor'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('TripAdvisor') }}</p>
        </div>

        <!-- Google -->
        <div class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#4285F4] uppercase tracking-wider">{{ __('Google') }}</span>
                <span class="text-xs font-black text-[#4285F4]">G</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $stats['google'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Google Reviews') }}</p>
        </div>

        <!-- SafariBookings -->
        <div class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">{{ __('SafariBookings') }}</span>
                <span class="text-xs font-black text-amber-600">SB</span>
            </div>
            <p class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $stats['safari_bookings'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('SafariBookings') }}</p>
        </div>

        <!-- Average Rating -->
        <div class="bg-card rounded-[3px] p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Avg Rating') }}</span>
                <span class="p-1.5 rounded-[3px] bg-amber-50 dark:bg-amber-900/30 text-amber-500">
                    <x-icon name="sparkles" class="w-4 h-4" />
                </span>
            </div>
            <div class="flex items-baseline gap-1 mt-2">
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($stats['avg_rating'], 1) }}</p>
                <span class="text-xs text-amber-500 font-bold">★★★★★</span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('Score average') }}</p>
        </div>
    </div>

    <!-- Filters & Table Card -->
    <div class="bg-card rounded-[3px] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search Input -->
            <div class="w-full md:w-80 relative">
                <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by reviewer, title, text...') }}"
                    class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#455537] focus:border-[#455537]" />
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Platform Filter -->
                <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-800/50 p-1 rounded-[3px] border border-gray-200 dark:border-gray-700 text-xs">
                    <button wire:click="$set('platformFilter', 'all')"
                        class="px-2.5 py-1 font-semibold rounded-[2px] transition-all {{ $platformFilter === 'all' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ __('All Platforms') }}
                    </button>
                    <button wire:click="$set('platformFilter', 'tripadvisor')"
                        class="px-2.5 py-1 font-semibold rounded-[2px] transition-all {{ $platformFilter === 'tripadvisor' ? 'bg-[#00AA6C] text-white shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        TripAdvisor
                    </button>
                    <button wire:click="$set('platformFilter', 'google')"
                        class="px-2.5 py-1 font-semibold rounded-[2px] transition-all {{ $platformFilter === 'google' ? 'bg-[#4285F4] text-white shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        Google
                    </button>
                    <button wire:click="$set('platformFilter', 'safari_bookings')"
                        class="px-2.5 py-1 font-semibold rounded-[2px] transition-all {{ $platformFilter === 'safari_bookings' ? 'bg-amber-600 text-white shadow-xs' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        SafariBookings
                    </button>
                </div>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter"
                    class="py-1.5 px-3 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-[#455537]">
                    <option value="all">{{ __('All Status') }}</option>
                    <option value="published">{{ __('Published on Website') }}</option>
                    <option value="hidden">{{ __('Hidden from Website') }}</option>
                    <option value="featured">{{ __('Featured Only') }}</option>
                </select>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">{{ __('Platform') }}</th>
                        <th>{{ __('Reviewer') }}</th>
                        <th>{{ __('Rating') }}</th>
                        <th>{{ __('Headline & Review Text') }}</th>
                        <th>{{ __('Trip Date / Type') }}</th>
                        <th class="text-center">{{ __('Website Display') }}</th>
                        <th class="text-center">{{ __('Featured') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40 transition-colors {{ !$review->is_published ? 'opacity-60 bg-gray-50/30 dark:bg-gray-900/30' : '' }}">
                            <!-- Platform Icon -->
                            <td class="text-center align-middle">
                                @if($review->platform === 'tripadvisor')
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#00AA6C]/10 text-[#00AA6C]" title="TripAdvisor">
                                        <svg class="w-5 h-5" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="50" fill="#00AA6C"/>
                                            <circle cx="50" cy="50" r="28" fill="white"/>
                                            <circle cx="50" cy="50" r="16" fill="#00AA6C"/>
                                            <circle cx="28" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
                                            <circle cx="72" cy="50" r="12" fill="white" stroke="#00AA6C" stroke-width="4"/>
                                            <circle cx="28" cy="50" r="5" fill="#00AA6C"/>
                                            <circle cx="72" cy="50" r="5" fill="#00AA6C"/>
                                        </svg>
                                    </span>
                                @elseif($review->platform === 'google')
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#4285F4]/10" title="Google Reviews">
                                        <svg class="w-5 h-5" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                        </svg>
                                    </span>
                                @elseif($review->platform === 'safari_bookings')
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-600/10 text-amber-600 font-bold text-xs" title="SafariBookings">
                                        SB
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 font-bold text-xs" title="Direct">
                                        DIR
                                    </span>
                                @endif
                            </td>

                            <!-- Reviewer Info -->
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-gray-700">
                                        {{ $review->author_initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                            {{ $review->author_name }}
                                        </p>
                                        @if($review->author_location)
                                            <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                                <x-icon name="map-pin" class="w-3 h-3" />
                                                <span class="truncate">{{ $review->author_location }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Rating -->
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-amber-500 font-black text-sm tracking-tighter">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($review->rating))
                                                ★
                                            @else
                                                <span class="text-gray-200 dark:text-gray-700">★</span>
                                            @endif
                                        @endfor
                                    </span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ number_format($review->rating, 1) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Content -->
                            <td class="max-w-md">
                                @if($review->title)
                                    <p class="font-bold text-xs text-gray-900 dark:text-white truncate mb-0.5">
                                        "{{ $review->title }}"
                                    </p>
                                @endif
                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                    {{ $review->content }}
                                </p>
                                @if($review->review_url)
                                    <a href="{{ $review->review_url }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] text-indigo-500 hover:text-indigo-600 hover:underline mt-1">
                                        <span>{{ __('View original') }}</span>
                                        <x-icon name="arrow-top-right-on-square" class="w-3 h-3" />
                                    </a>
                                @endif
                            </td>

                            <!-- Trip Date / Type -->
                            <td>
                                <div class="text-xs text-gray-700 dark:text-gray-300">
                                    @if($review->review_date)
                                        <p class="font-medium">{{ $review->review_date->format('M d, Y') }}</p>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                    @if($review->trip_type)
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                            {{ $review->trip_type }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Toggle Published (Display on Website) -->
                            <td class="text-center align-middle">
                                <button wire:click="togglePublished({{ $review->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-all {{ $review->is_published ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700' }}"
                                    title="{{ $review->is_published ? __('Click to hide from website') : __('Click to display on website') }}">
                                    @if($review->is_published)
                                        <x-icon name="eye" class="w-3.5 h-3.5" />
                                        <span>{{ __('Visible') }}</span>
                                    @else
                                        <x-icon name="eye-slash" class="w-3.5 h-3.5" />
                                        <span>{{ __('Hidden') }}</span>
                                    @endif
                                </button>
                            </td>

                            <!-- Toggle Featured -->
                            <td class="text-center align-middle">
                                <button wire:click="toggleFeatured({{ $review->id }})"
                                    class="p-1.5 rounded-full transition-all {{ $review->is_featured ? 'text-amber-500 bg-amber-50 dark:bg-amber-500/10' : 'text-gray-300 dark:text-gray-600 hover:text-amber-400' }}"
                                    title="{{ $review->is_featured ? __('Remove from featured') : __('Make featured') }}">
                                    <x-icon name="star" class="w-5 h-5 {{ $review->is_featured ? 'fill-current' : '' }}" />
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="openEditModal({{ $review->id }})"
                                        class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-[3px] hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        title="{{ __('Edit Review') }}">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="confirmDelete({{ $review->id }})"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded-[3px] hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                        title="{{ __('Delete') }}">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                <x-icon name="star" class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-700 mb-2" />
                                <p class="font-bold text-sm">{{ __('No reviews found') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('Try adjusting your filters or pull reviews from TripAdvisor.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- ── Platform Links Modal ────────────────────────────────────────── -->
    @if($showLinksModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white dark:bg-card border border-gray-200 dark:border-gray-800 rounded-[3px] shadow-2xl max-w-lg w-full p-6 space-y-4 animate-scaleIn">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <x-icon name="link" class="w-5 h-5 text-amber-500" />
                        <h3 class="font-bold text-base text-gray-900 dark:text-white">{{ __('Review Platform Links') }}</h3>
                    </div>
                    <button wire:click="$set('showLinksModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Save your profile links for TripAdvisor, Google Reviews, and SafariBookings to display badges across the website.') }}
                </p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#00AA6C] text-white text-[8px] flex items-center justify-center font-bold">TA</span>
                            {{ __('TripAdvisor Listing URL') }}
                        </label>
                        <input type="url" wire:model="tripadvisorUrl"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white"
                            placeholder="https://www.tripadvisor.com/Attraction_Review-..." />
                        @error('tripadvisorUrl') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-[#4285F4] text-white text-[8px] flex items-center justify-center font-bold">G</span>
                            {{ __('Google Reviews / Maps URL') }}
                        </label>
                        <input type="text" wire:model="googleUrl"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white"
                            placeholder="https://maps.app.goo.gl/... or https://www.google.com/maps/place/..." />
                        @error('googleUrl') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 flex items-center gap-1.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-amber-600 text-white text-[8px] flex items-center justify-center font-bold">SB</span>
                            {{ __('SafariBookings Reviews URL') }}
                        </label>
                        <input type="url" wire:model="safaribookingsUrl"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white"
                            placeholder="https://www.safaribookings.com/reviews/p... or https://www.safaribookings.com/operators/..." />
                        @error('safaribookingsUrl') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Google Place ID (Optional)') }}</label>
                            <input type="text" wire:model="googlePlaceId"
                                class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white"
                                placeholder="ChIJ..." />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ __('Google Places API Key (Optional)') }}</label>
                            <input type="password" wire:model="googleApiKey"
                                class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px] text-gray-900 dark:text-white"
                                placeholder="AIzaSy..." />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showLinksModal', false)"
                        class="px-4 py-2 text-xs font-semibold rounded-[3px] border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="savePlatformLinks" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold rounded-[3px] bg-[#455537] hover:bg-[#354329] text-white shadow-sm disabled:opacity-50">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        {{ __('Save Links') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ── Create / Edit Modal ────────────────────────────────────────────────── -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white dark:bg-card border border-gray-200 dark:border-gray-800 rounded-[3px] shadow-2xl max-w-xl w-full p-6 space-y-4 animate-scaleIn">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">
                        {{ $editingId ? __('Edit Review') : __('Add New Review') }}
                    </h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Platform') }} *
                        </label>
                        <select wire:model="platform"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]">
                            <option value="tripadvisor">TripAdvisor</option>
                            <option value="google">Google</option>
                            <option value="safari_bookings">SafariBookings</option>
                            <option value="direct">Direct</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Rating (1 to 5)') }} *
                        </label>
                        <input type="number" step="0.5" min="1" max="5" wire:model="rating"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Reviewer Name') }} *
                        </label>
                        <input type="text" wire:model="author_name"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                            placeholder="e.g. Maria & Karl" />
                        @error('author_name') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Origin / Location') }}
                        </label>
                        <input type="text" wire:model="author_location"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                            placeholder="e.g. Germany" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Review Date') }}
                        </label>
                        <input type="date" wire:model="review_date"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Trip Type / Tag') }}
                        </label>
                        <input type="text" wire:model="trip_type"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                            placeholder="e.g. Couples, Family Safari" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Review Headline / Title') }}
                    </label>
                    <input type="text" wire:model="title"
                        class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                        placeholder="e.g. 15 Day East Africa Amazing Safari" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Review Text / Comments') }} *
                    </label>
                    <textarea wire:model="content" rows="4"
                        class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                        placeholder="{{ __('Write or paste full review...') }}"></textarea>
                    @error('content') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Original Review URL') }}
                    </label>
                    <input type="url" wire:model="review_url"
                        class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-[3px]"
                        placeholder="https://www.tripadvisor.com/..." />
                </div>

                <div class="flex items-center gap-6 pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model="is_published" class="rounded-[3px] text-[#455537] focus:ring-[#455537]" />
                        <span>{{ __('Display on Website') }}</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model="is_featured" class="rounded-[3px] text-amber-500 focus:ring-amber-500" />
                        <span>{{ __('Featured / Pinned Top Pick') }}</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="$set('showCreateModal', false)"
                        class="px-4 py-2 text-xs font-semibold rounded-[3px] border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="saveReview"
                        class="px-5 py-2 text-xs font-semibold rounded-[3px] bg-[#455537] hover:bg-[#354329] text-white shadow-sm">
                        {{ $editingId ? __('Update Review') : __('Save Review') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ── Delete Confirmation Modal ──────────────────────────────────────────── -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white dark:bg-card border border-gray-200 dark:border-gray-800 rounded-[3px] shadow-2xl max-w-sm w-full p-6 space-y-4">
                <div class="flex items-center gap-3 text-red-600">
                    <x-icon name="trash" class="w-6 h-6" />
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">{{ __('Delete Review?') }}</h3>
                </div>
                <p class="text-xs text-gray-500">
                    {{ __('Are you sure you want to delete this review? This action cannot be undone.') }}
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-[3px] border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="deleteReview"
                        class="px-4 py-1.5 text-xs font-semibold rounded-[3px] bg-red-600 hover:bg-red-700 text-white">
                        {{ __('Yes, Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
