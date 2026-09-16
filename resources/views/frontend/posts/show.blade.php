@extends('layouts.frontend')

@if($post->seo?->title)
@section('title', $post->seo->title)
@else
@section('title', $post->title . ' |  ')
@endif

@section('content')

    <!-- ── HERO ─────────────────────────────────────────────── -->
    <div class="relative w-full h-[50vh] min-h-[400px] bg-gray-900 bg-cover bg-center overflow-hidden"
        style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : ' ' }}')">

        <div class="absolute inset-0 bg-gray-950/50"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/40 to-transparent"></div>

        <!-- Breadcrumb -->
        <div class="absolute top-6 left-0 right-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 z-20">
            <nav
                class="flex items-center gap-2 !text-xs text-gray-300 font-medium bg-black/30 w-fit px-4 py-2 rounded-sm backdrop-blur-sm">
                <a href="{{ route('home') }}" class="!text-xs hover:text-white transition-colors">{{ __('Home') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <a href="{{ route('articles.index') }}"
                    class="!text-xs hover:text-white transition-colors">{{ __('Articles') }}</a>
                <span class="!text-xs opacity-50">›</span>
                <span class="!text-xs text-gray-400">{{ Str::limit($post->title, 30) }}</span>
            </nav>
        </div>

        <div class="absolute bottom-0 left-0 right-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-12 z-10 text-white">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 font-brand max-w-4xl">{{ $post->title }}</h1>

            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-200">
                <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-sm backdrop-blur-sm">
                    <x-icon name="calendar" class="w-4 h-4 text-brand-primary" />
                    <span
                        class="!text-xs text-gray-400">{{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CONTENT & SIDEBAR ────────────────────────────────── -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16 relative">

            <!-- MAIN CONTENT (LEFT) -->
            <div class="lg:w-2/3">
                <div
                    class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 prose-headings:font-brand prose-headings:text-gray-900 dark:prose-headings:text-white prose-a:text-brand-primary hover:prose-a:text-brand-primary-hover prose-img:rounded-[3px]">
                    {!! $post->getRenderedHtml() !!}
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold uppercase tracking-wider text-gray-500">{{ __('Connect:') }}</span>
                        <div class="flex gap-2">
                            @php
                                $settings = app(\App\Settings\GeneralSetting::class);
                            @endphp

                            @if($settings->facebook_url)
                                <a href="{{ $settings->facebook_url }}" target="_blank"
                                    class="w-10 h-10 rounded-sm bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-colors">
                                    <x-icon name="facebook-solid" class="w-4 h-4" />
                                </a>
                            @endif

                            @if($settings->x_url)
                                <a href="{{ $settings->x_url }}" target="_blank"
                                    class="w-10 h-10 rounded-sm bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-colors">
                                    <x-icon name="x" class="w-4 h-4" />
                                </a>
                            @endif

                            @if($settings->instagram_url)
                                <a href="{{ $settings->instagram_url }}" target="_blank"
                                    class="w-10 h-10 rounded-sm bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-colors">
                                    <x-icon name="instagram" class="w-4 h-4" />
                                </a>
                            @endif

                            @if($settings->youtube_url)
                                <a href="{{ $settings->youtube_url }}" target="_blank"
                                    class="w-10 h-10 rounded-sm bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-colors">
                                    <x-icon name="youtube" class="w-4 h-4" />
                                </a>
                            @endif

                            @if($settings->tiktok_url)
                                <a href="{{ $settings->tiktok_url }}" target="_blank"
                                    class="w-10 h-10 rounded-sm bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-colors">
                                    <x-icon name="tiktok" class="w-4 h-4" />
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="lg:w-1/3">
                <div class="sticky top-24 space-y-8">

                    <!-- Search Widget -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-[3px] p-6 border border-gray-100 dark:border-gray-800">
                        <h4 class="text-lg font-bold font-brand text-gray-900 dark:text-white mb-4">{{ __('Search Blog') }}
                        </h4>
                        <form action="{{ route('articles.index') }}" method="GET" class="relative">
                            <input type="text" name="search" placeholder="{{ __('Search...') }}"
                                class="w-full bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-[3px] py-3 px-4 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary">
                            <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-primary">
                                <x-icon name="magnifying-glass" class="w-5 h-5" />
                            </button>
                        </form>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-[3px] p-6 border border-gray-100 dark:border-gray-800">
                        <h4 class="text-lg font-bold font-brand text-gray-900 dark:text-white mb-6">
                            {{ __('Recent Articles') }}</h4>

                        <div class="space-y-6">
                            @forelse($recentPosts as $recent)
                                <div class="flex gap-4 group">
                                    <a href="{{ route('articles.show', $recent->slug) }}"
                                        class="flex-shrink-0 w-20 h-20 rounded-[3px] overflow-hidden bg-gray-200 dark:bg-gray-800">
                                        @if($recent->featured_image)
                                            <img src="{{ Storage::url($recent->featured_image) }}" alt="{{ $recent->title }}"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <x-icon name="photo" class="w-6 h-6 text-gray-400" />
                                            </div>
                                        @endif
                                    </a>
                                    <div class="flex flex-col justify-center">
                                        <a href="{{ route('articles.show', $recent->slug) }}">
                                            <h5
                                                class="text-sm font-bold text-gray-900 dark:text-white leading-tight group-hover:text-brand-primary transition-colors line-clamp-2 mb-1.5">
                                                {{ $recent->title }}
                                            </h5>
                                        </a>
                                        <span
                                            class="text-[10px] uppercase tracking-wider font-bold text-gray-500">{{ $recent->published_at ? $recent->published_at->format('M d, Y') : $recent->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic">{{ __('No other recent posts found.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Call to Action Widget (Tours) -->
                    <div class="relative rounded-[3px] overflow-hidden shadow-lg">
                        <img src="{{ asset('images/safari-promo.jpg') }}" alt="Safari Promo" class="w-full h-auto"
                            onerror="this.src='https://images.unsplash.com/photo-1516426122078-c23e76319801?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h4 class="text-xl font-bold font-brand mb-2">{{ __('Ready for an Adventure?') }}</h4>
                            <p class="text-sm text-gray-200 mb-4">{{ __('Book your dream safari tour today and experience the wild
                                    like never before.') }}</p>
                            <a href="{{ route('tours.index') }}"
                                class="inline-block bg-brand-primary text-white text-sm font-bold px-5 py-2.5 rounded-[3px] hover:bg-brand-primary-hover transition-colors shadow-lg">{{ __('View Tours') }}</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection