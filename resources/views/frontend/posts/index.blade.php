@extends('layouts.frontend')

@section('title', 'Articles |  ')

@section('content')

    <!-- ── HERO ─────────────────────────────────────────────── -->
    <div class="relative w-full h-[40vh] min-h-[300px] bg-gray-900 bg-cover bg-center overflow-hidden"
        @if($companySetting?->article_hero_image)
        style="background-image: url('{{ Storage::url($companySetting->article_hero_image) }}');" @endif>
        <div class="absolute inset-0 bg-gray-950/60"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-6 z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 font-brand">{{ __('Travel Articles') }}</h1>
            <p class="text-lg text-gray-300 max-w-2xl">
                {{ __('Discover tips, destination guides, and inspiring stories for your next African adventure.') }}</p>
        </div>
    </div>

    <!-- ── FILTER BAR ───────────────────────────────────────── -->
    <div class="filter-bar font-lora dark:!bg-gray-900">
        <div class="filter-inner">
            <span class="filter-label">{{ __('Filter:') }}</span>
            <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}"
                class="filter-btn  {{ !request()->has('category') ? 'active' : '' }}">{{ __('All') }}</a>
            @foreach(\App\Models\PostCategory::limit(6)->get() as $category)
                <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug, 'page' => null]) }}"
                    class="filter-btn {{ request('category') === $category->slug ? 'active' : '' }}">{{ $category->name }}</a>
            @endforeach

            <div class="filter-sep"></div>
            <select class="sort-select" onchange="window.location.href=this.value">
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
            </select>
        </div>
    </div>

    <!-- ── BLOG LIST ────────────────────────────────────────── -->
    <div class="page-wrap font-lora">
        <div>
            @if($posts->count() > 0)
                <div class="articles-grid ">
                    @foreach($posts as $post)
                        <a href="{{ route('articles.show', $post->slug) }}" class="ac group">
                            <div class="ac-img-wrap">
                                @if($post->featured_image)
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="ac-img">
                                @else
                                    <div class="ac-img bg-gray-200 flex items-center justify-center">
                                        <x-icon name="photo" class="w-12 h-12 text-gray-400" />
                                    </div>
                                @endif
                                <span class="ac-tag">{{ auto_translate($post->category->name ?? 'Article') }}</span>
                            </div>
                            <div class="ac-body dark:bg-gray-900">
                                <div class="ac-cat">{{ auto_translate($post->category->name ?? 'Article') }}</div>
                                <h2
                                    class="text-lg font-bold text-gray-900 dark:text-white text-brand-secondary leading-snug group-hover:text-brand-primary transition-colors line-clamp-2 mb-2">
                                    {{ auto_translate($post->title) }}</h2>
                                <div
                                    class="text-[15px] text-gray-550 dark:text-gray-400 font-light leading-relaxed mb-3 line-clamp-3">
                                    {{ auto_translate(Str::words(strip_tags($post->excerpt ?? $post->content), 50)) }}
                                </div>
                                <div class="ac-meta">
                                    <span>{{ $post->published_at ? $post->published_at->format('M Y') : $post->created_at->format('M Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12 flex justify-center">
                    {{ $posts->links('vendor.pagination.frontend-tailwind') }}
                </div>
            @else
                <div
                    class="text-center py-20 bg-gray-50 dark:bg-gray-900 rounded-[3px] border border-dashed border-gray-300 dark:border-gray-800">
                    <x-icon name="document-text" class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('No Posts Yet') }}</h3>
                    <p class="text-gray-500">{{ __('Check back later for exciting travel stories and guides.') }}</p>
                </div>
            @endif
        </div>

        <aside class="blog-sidebar">
            <div class="sidebar-box">
                <h3 class="dark:!text-gray-300">{{ __('Categories') }}</h3>
                <ul class="sub-cats">
                    <li><a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}"
                            class="dark:!text-gray-300 {{ !request()->has('category') ? 'active' : '' }}">{{ __('All Articles') }}<span
                                class="!text-sm dark:!bg-gray-800 dark:!text-gray-300">{{ \App\Models\Post::where('is_published', true)->count() }}</span></a>
                    </li>
                    @foreach(\App\Models\PostCategory::all() as $category)
                        <li><a href="{{ request()->fullUrlWithQuery(['category' => $category->slug, 'page' => null]) }}"
                                class=" dark:!text-gray-300 {{ request('category') === $category->slug ? 'active' : '' }}">{{ $category->name }}
                                <span
                                    class="!text-sm dark:!bg-gray-800 dark:!text-gray-300">{{ $category->posts()->where('is_published', true)->count() }}</span></a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="newsletter-box">
                <h3>{{ __('Join Our Newsletter') }}</h3>
                <p>{{ __('Get safari tips, destination guides, and exclusive offers.') }}</p>
                <form id="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <input type="text" name="website_url" style="display:none" tabindex="-1" autocomplete="off">
                    <input type="email" name="email" placeholder="{{ __('Your email address') }}" required>
                    <button type="submit">{{ __('Subscribe') }}</button>
                </form>
            </div>
        </aside>
    </div>

@endsection