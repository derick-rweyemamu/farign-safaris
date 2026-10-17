<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', ' '))</title>
    @if(!empty($companySetting?->favicon))
        <link rel="icon"
            href="{{ Storage::url($companySetting->favicon) }}?v={{ file_exists(storage_path('app/public/' . $companySetting->favicon)) ? filemtime(storage_path('app/public/' . $companySetting->favicon)) : '1' }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&family=Lora:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Indie+Flower&family=Instrument+Sans:wght@400;500;600;700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Disable dark mode on frontend (Always use Light mode) -->
    <script>
        document.documentElement.classList.remove('dark');
        window.__IS_FRONTEND__ = true;
    </script>

    <!-- Styles / Scripts -->
    @vite(['resources/themes/frontend/assets/css/app.css', 'resources/themes/frontend/assets/js/app.js'])

    <!-- GrapesJS Custom Styles -->
    <style>
        {!! isset($page) ? ($page->builder_css ?? '') : '' !!}

        @keyframes nav-slide-down {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(0);
            }
        }

        .animate-nav-slide-down {
            animation: nav-slide-down 0.4s ease-out forwards;
        }
    </style>
    @stack('styles')
    @livewireStyles
</head>

<body class="frontend-body bg-white text-gray-900 antialiased font-lora">

    <!-- Navbar Wrapper to share state between nav and fixed drawer -->
    <header x-data="{ mobileMenuOpen: false, isSticky: false, navScrollY: 0 }"
        @scroll.window="isSticky = (window.pageYOffset > 150); navScrollY = window.pageYOffset"
        class="sticky top-0 z-50 w-full transition-all duration-300">

        <!-- Top Bar (Collapses smoothly on scroll) -->
        <div class="hidden md:block bg-[#2E1F12] border-b border-[#fbb158]/15 transition-all duration-300 overflow-hidden"
            :class="isSticky ? 'max-h-0 opacity-0 border-none' : 'max-h-[50px] opacity-100'">
            <div class="mx-auto max-w-7xl px-6 lg:px-4 h-[38px] flex items-center justify-between">
                <div class="flex items-center gap-7">
                    <a href="tel:{{ $companySetting?->phone ?? ' ' }}"
                        class="flex items-center gap-1.5 text-white/65 hover:text-[#fbb158] text-[12px] font-light tracking-[0.02em] transition-colors"
                        style="font-family: 'Lora', Georgia, serif;">
                        <x-icon name="phone" class="w-3.5 h-3.5 text-[#fbb158]" />
                        {{ $companySetting?->phone ?? '' }}
                    </a>
                    <a href="mailto:{{ $companySetting?->email ?? ' ' }}"
                        class="flex items-center gap-1.5 text-white/65 hover:text-[#fbb158] text-[12px] font-light tracking-[0.02em] transition-colors"
                        style="font-family: 'Lora', Georgia, serif;">
                        <x-icon name="envelope" class="w-3.5 h-3.5 text-[#fbb158]" />
                        {{ $companySetting?->email ?? '' }}
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @if($companySetting?->facebook_url)
                        <a href="{{ $companySetting->facebook_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-[#fbb158] hover:border-[#fbb158] transition-all text-[10px] font-sans"><span
                                class="sr-only">{{ __('Facebook') }}</span><x-icon name="facebook" type="solid"
                                class="w-3 h-3" /></a>
                    @endif
                    @if($companySetting?->instagram_url)
                        <a href="{{ $companySetting->instagram_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-[#fbb158] hover:border-[#fbb158] transition-all text-[10px] font-sans"><span
                                class="sr-only">{{ __('Instagram') }}</span><x-icon name="instagram" type="solid"
                                class="w-3 h-3" /></a>
                    @endif
                    @if($companySetting?->youtube_url)
                        <a href="{{ $companySetting->youtube_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-[#fbb158] hover:border-[#fbb158] transition-all text-[10px] font-sans"><span
                                class="sr-only">{{ __('YouTube') }}</span><x-icon name="youtube" type="solid"
                                class="w-3 h-3" /></a>
                    @endif
                    @if($companySetting?->x_url)
                        <a href="{{ $companySetting->x_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-[#fbb158] hover:border-[#fbb158] transition-all text-[10px] font-sans"><span
                                class="sr-only">X</span><x-icon name="x" type="solid" class="w-3 h-3" /></a>
                    @endif
                    @if($companySetting?->tiktok_url)
                        <a href="{{ $companySetting->tiktok_url }}" target="_blank" rel="noopener noreferrer"
                            class="w-6 h-6 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-[#fbb158] hover:border-[#fbb158] transition-all text-[10px] font-sans"><span
                                class="sr-only">{{ __('TikTok') }}</span><x-icon name="tiktok" type="solid"
                                class="w-3 h-3" /></a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sticky Navbar Container -->
        <nav class="w-full bg-[#140c05]/97 backdrop-blur-[20px] saturate-150 border-b border-[#fbb158]/22 shadow-[0_2px_30px_rgba(0,0,0,0.3)] transition-all duration-300"
            :class="isSticky ? 'h-[60px] shadow-2xl' : 'h-[68px]'">
            <div class="mx-auto max-w-7xl px-6 lg:px-4 h-full">
                <div class="flex items-center justify-between h-full">
                    <div>
                        <!-- Logo -->
                        <div class="flex items-center h-full">
                            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                                @if(!empty($companySetting?->company_logo))
                                    <img src="{{ asset('storage/' . $companySetting->company_logo) }}"
                                        alt="{{ $companySetting->company_name ?? config('app.name') }}"
                                        class="w-auto object-contain transition-all duration-300" style="height: 52px;"
                                        :style="isSticky ? 'height: 42px;' : 'height: 52px;'">
                                @else
                                    <div class="text-[#fbb158] font-semibold text-[22px] tracking-[0.01em] leading-[1.1]"
                                        style="font-family: 'Playfair Display', Georgia, serif;">
                                        {{ $companySetting?->company_name ?? config('app.name', ' ') }}
                                    </div>
                                @endif
                            </a>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-8 h-full justify-center flex-1 ml-10">
                        <!-- Desktop Menu -->
                        <div class="flex items-center space-x-0 h-full">
                            @if($headerMenu->count() > 0)
                                {{-- Dynamic menu from DB --}}
                                @foreach($headerMenu as $item)
                                    @php
                                        $hasChildren = $item->is_mega_menu || $item->children->count() > 0;
                                        $hasRealUrl = !empty($item->resolved_url) && $item->resolved_url !== '#' && $item->resolved_url !== 'javascript:void(0);' && $item->resolved_url !== 'javascript:void(0)';
                                        $isActive = $item->isCurrentActive();
                                    @endphp
                                    @if($item->is_mega_menu)
                                        <div class="relative group h-full flex items-center" x-data="{ open: false }"
                                            @mouseenter="open = true" @mouseleave="open = false">
                                            <a href="{{ $hasRealUrl ? $item->resolved_url : 'javascript:void(0);' }}"
                                                @if(!$hasRealUrl) @click.prevent="open = !open" @endif
                                                @if($item->is_external && $hasRealUrl) target="_blank" rel="noopener noreferrer" @endif
                                                class="flex h-full items-center gap-1 {{ $isActive ? 'text-[#fbb158] font-bold border-b-2 border-[#fbb158]' : 'text-white/80 hover:text-[#fbb158]' }} px-4 text-[12px] font-medium tracking-[0.08em] uppercase transition-colors whitespace-nowrap"
                                                style="font-family: 'Lora', Georgia, serif;">
                                                <span>{{ $item->label }}</span>
                                                <button type="button" @click.stop.prevent="open = !open" class="p-1 focus:outline-none" aria-label="Toggle {{ $item->label }} submenu">
                                                    <x-icon name="chevron-down"
                                                        class="w-[9px] h-[9px] mt-px opacity-50 transition-transform duration-200"
                                                        x-bind:class="open ? 'rotate-180 opacity-100 text-[#fbb158]' : ''" />
                                                </button>
                                            </a>
                                            <x-frontend.mega-menu :item="$item" />
                                        </div>
                                    @elseif($item->children->count() > 0)
                                        {{-- ── STANDARD DROPDOWN ITEM ──────────────────────────────────── --}}
                                        <div class="relative group h-full flex items-center" x-data="{ open: false }"
                                            @mouseenter="open = true" @mouseleave="open = false">
                                            <a href="{{ $hasRealUrl ? $item->resolved_url : 'javascript:void(0);' }}"
                                                @if(!$hasRealUrl) @click.prevent="open = !open" @endif
                                                @if($item->is_external && $hasRealUrl) target="_blank" rel="noopener noreferrer" @endif
                                                class="flex h-full items-center gap-1 {{ $isActive ? 'text-[#fbb158] font-bold border-b-2 border-[#fbb158]' : 'text-white/80 hover:text-[#fbb158]' }} px-4 text-[12px] font-medium tracking-[0.08em] uppercase transition-colors whitespace-nowrap"
                                                style="font-family: 'Lora', Georgia, serif;">
                                                <span>{{ $item->label }}</span>
                                                <button type="button" @click.stop.prevent="open = !open" class="p-1 focus:outline-none" aria-label="Toggle {{ $item->label }} submenu">
                                                    <x-icon name="chevron-down"
                                                        class="w-[9px] h-[9px] mt-px opacity-50 transition-transform duration-200"
                                                        x-bind:class="open ? 'rotate-180 opacity-100 text-[#fbb158]' : ''" />
                                                </button>
                                            </a>
                                            <!-- Dropdown Menu -->
                                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-2"
                                                class="absolute left-0 top-[100%] min-w-[220px] bg-[#140c05]/97 border border-[#fbb158]/15 border-t-2 border-t-[#fbb158] rounded-b-[4px] py-0 shadow-[0_12px_40px_rgba(0,0,0,0.4)] z-50"
                                                style="display: none;">
                                                <ul class="flex flex-col">
                                                    @foreach($item->children as $child)
                                                        @php
                                                            $isChildActive = $child->isCurrentActive();
                                                        @endphp
                                                        <li>
                                                            <a href="{{ $child->resolved_url }}" @if($child->is_external)
                                                            target="_blank" rel="noopener noreferrer" @endif
                                                                class="block py-[9px] px-[1.4rem] {{ $isChildActive ? 'text-[#fbb158] font-semibold border-l-[#fbb158] bg-[#fbb158]/10' : 'text-white/70 hover:text-[#fbb158] hover:border-l-[#fbb158] hover:bg-[#fbb158]/5' }} text-[12.5px] font-light tracking-[0.03em] border-l-2 border-transparent transition-all duration-150 hover:pl-[1.8rem]"
                                                                style="font-family: 'Lora', Georgia, serif;">
                                                                {{ $child->label }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @else
                                        {{-- ── PLAIN LINK ───────────────────────────────────────────────── --}}
                                        <div class="h-full flex items-center">
                                            <a href="{{ $item->resolved_url }}" @if($item->is_external) target="_blank"
                                            rel="noopener noreferrer" @endif
                                                class="flex h-full items-center {{ $isActive ? 'text-[#fbb158] font-bold border-b-2 border-[#fbb158]' : 'text-white/80 hover:text-[#fbb158]' }} px-4 text-[12px] font-medium tracking-[0.08em] uppercase transition-colors whitespace-nowrap"
                                                style="font-family: 'Lora', Georgia, serif;">
                                                {{ $item->label }}
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="hidden md:flex items-center gap-3 shrink-0">

                        @if(!empty($companySetting?->whatsapp_number))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting->whatsapp_number) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2 bg-[#25D366] text-white hover:bg-[#128C7E] px-[18px] py-[8px] rounded-[4px] text-[12px] font-bold tracking-[0.07em] uppercase transition-colors whitespace-nowrap shadow-sm"
                                style="font-family: 'Lora', Georgia, serif;">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>{{ __('WHATSAPP') }}</a>
                        @endif
                    </div>

                    <!-- Mobile menu toggle button -->
                    <div class="-mr-2 flex md:hidden items-center h-full shrink-0">
                        <button @click="mobileMenuOpen = true" type="button"
                            class="relative inline-flex items-center justify-center rounded-[3px] p-2 text-white/80 hover:text-[#fbb158] focus:outline-none transition-colors">
                            <span class="sr-only">{{ __('Open main menu') }}</span>
                            <x-icon name="bars-3" class="block h-7 w-7" />
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        </div>

        {{-- ══ MOBILE MENU — Full-Screen Overlay ══ --}}
        <div x-show="mobileMenuOpen" class="fixed inset-0 z-[200] flex flex-col" style="display:none;"
            @keydown.escape.window="mobileMenuOpen = false">

            {{-- Backdrop blur layer --}}
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-out duration-400"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="absolute inset-0 bg-[#0e0904]/75 backdrop-blur-sm"
                @click="mobileMenuOpen = false">
            </div>

            {{-- Slide-in Panel --}}
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-350 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="relative flex flex-col h-full w-full max-w-[320px] shadow-2xl overflow-hidden"
                style="background: linear-gradient(165deg, #1a0e06 0%, #2E1F12 45%, #1a0e06 100%);">

                {{-- Gold top accent line --}}
                <div class="h-[3px] w-full flex-shrink-0"
                    style="background: linear-gradient(90deg, transparent, #fbb158 30%, #f0d080 60%, #fbb158 80%, transparent);">
                </div>

                {{-- ── HEADER ── --}}
                <div class="flex items-center justify-between px-6 py-4 flex-shrink-0"
                    style="border-bottom: 1px solid rgba(201,168,76,0.18);">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3">
                        @if(!empty($companySetting?->company_logo))
                            <img src="{{ asset('storage/' . $companySetting->company_logo) }}"
                                alt="{{ $companySetting->company_name ?? config('app.name') }}"
                                class="h-10 w-auto object-contain">
                        @else
                            <span class="text-[#fbb158] font-semibold text-[20px] tracking-[0.01em] leading-tight"
                                style="font-family: 'Playfair Display', Georgia, serif;">
                                {{ $companySetting?->company_name ?? config('app.name', ' ') }}
                            </span>
                        @endif
                    </a>

                    {{-- Close button --}}
                    <button type="button" @click="mobileMenuOpen = false"
                        class="flex items-center justify-center w-9 h-9 rounded-full transition-all duration-200 focus:outline-none"
                        style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                        onmouseover="this.style.background='rgba(201,168,76,0.18)'"
                        onmouseout="this.style.background='rgba(201,168,76,0.08)'">
                        <span class="sr-only">{{ __('Close menu') }}</span>
                        <svg class="w-5 h-5 text-[#fbb158]" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- ── NAV LINKS (scrollable) ── --}}
                <div class="flex-1 overflow-y-auto py-2" style="scrollbar-width: none;">
                    <nav class="px-4 py-4">
                        @if($headerMenu->count() > 0)
                            @foreach($headerMenu as $index => $item)
                                @php
                                    $hasChildren = $item->children->count() > 0;
                                    $hasRealUrl = !empty($item->resolved_url) && $item->resolved_url !== '#' && $item->resolved_url !== 'javascript:void(0);' && $item->resolved_url !== 'javascript:void(0)';
                                    $isActive = $item->isCurrentActive();
                                @endphp
                                @if($hasChildren)
                                    {{-- Parent with children --}}
                                    <div x-data="{ open: false }" class="mb-1">
                                        <div class="flex items-center rounded-[6px] overflow-hidden transition-all duration-200"
                                            style="border: 1px solid rgba(201,168,76,0.0);"
                                            x-bind:style="open || {{ $isActive ? 'true' : 'false' }} ? 'border-color: rgba(201,168,76,0.18); background: rgba(201,168,76,0.05)' : ''">
                                            <a href="{{ $hasRealUrl ? $item->resolved_url : 'javascript:void(0);' }}"
                                                @if($hasRealUrl)
                                                    @if($item->is_external) target="_blank" rel="noopener noreferrer" @endif
                                                    @click="mobileMenuOpen = false"
                                                @else
                                                    @click="open = !open"
                                                @endif
                                                class="flex-1 flex items-center gap-3 px-4 py-[14px] group">
                                                {{-- Gold dot accent --}}
                                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 transition-colors duration-200"
                                                    x-bind:style="open || {{ $isActive ? 'true' : 'false' }} ? 'background:#fbb158' : 'background:rgba(201,168,76,0.4)'"></span>
                                                <span
                                                    class="text-[15px] font-medium tracking-[0.06em] uppercase transition-colors duration-200"
                                                    style="font-family: 'Lora', Georgia, serif;"
                                                    x-bind:style="open || {{ $isActive ? 'true' : 'false' }} ? 'color:#fbb158; font-weight:700;' : 'color:rgba(255,255,255,0.85)'">
                                                    {{ $item->label }}
                                                </span>
                                            </a>
                                            <button @click="open = !open" type="button"
                                                class="flex items-center justify-center w-10 h-[50px] flex-shrink-0 focus:outline-none transition-colors duration-200"
                                                aria-label="Toggle {{ $item->label }} submenu">
                                                <svg class="w-4 h-4 transition-transform duration-300"
                                                    x-bind:style="open ? 'transform:rotate(180deg); color:#fbb158' : 'color:rgba(255,255,255,0.85)'"
                                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- Sub-menu --}}
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="ml-4 mb-1 rounded-b-[6px] overflow-hidden"
                                            style="display:none; border-left: 2px solid rgba(201,168,76,0.3);">
                                            @foreach($item->children as $child)
                                                @php
                                                    $isChildActive = $child->isCurrentActive();
                                                @endphp
                                                <a href="{{ $child->resolved_url }}" @if($child->is_external) target="_blank"
                                                rel="noopener noreferrer" @endif @click="mobileMenuOpen = false"
                                                    class="flex items-center gap-3 pl-4 pr-4 py-3 transition-all duration-150 group"
                                                    style="border-bottom: 1px solid rgba(201,168,76,0.06); background: {{ $isChildActive ? 'rgba(255,255,255,0.08)' : 'transparent' }};"
                                                    onmouseover="this.style.background='rgba(201,168,76,0.06)'; this.querySelector('span.sub-label').style.color='#fbb158'"
                                                    onmouseout="this.style.background='{{ $isChildActive ? 'rgba(255,255,255,0.08)' : 'transparent' }}'; this.querySelector('span.sub-label').style.color='{{ $isChildActive ? '#fbb158' : 'rgba(255,255,255,0.55)' }}'">
                                                    <svg class="w-2.5 h-2.5 flex-shrink-0" style="color:{{ $isChildActive ? '#fbb158' : 'rgba(201,168,76,0.5)' }}"
                                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    <span
                                                        class="sub-label text-[13px] tracking-[0.04em] font-light transition-colors duration-150"
                                                        style="font-family: 'Lora', Georgia, serif; color: {{ $isChildActive ? '#fbb158; font-weight:600;' : 'rgba(255,255,255,0.55)' }};">
                                                        {{ $child->label }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Plain link --}}
                                    <a href="{{ $item->resolved_url }}" @if($item->is_external) target="_blank"
                                    rel="noopener noreferrer" @endif @click="mobileMenuOpen = false"
                                        class="flex items-center gap-3 px-4 py-[14px] mb-1 rounded-[6px] transition-all duration-200"
                                        style="border: 1px solid {{ $isActive ? 'rgba(201,168,76,0.25)' : 'rgba(201,168,76,0.0)' }}; background: {{ $isActive ? 'rgba(201,168,76,0.08)' : 'transparent' }};"
                                        onmouseover="this.style.borderColor='rgba(201,168,76,0.18)'; this.style.background='rgba(201,168,76,0.05)'; this.querySelector('span.nav-label').style.color='#fbb158'; this.querySelector('span.nav-dot').style.background='#fbb158'"
                                        onmouseout="this.style.borderColor='{{ $isActive ? 'rgba(201,168,76,0.25)' : 'rgba(201,168,76,0.0)' }}'; this.style.background='{{ $isActive ? 'rgba(201,168,76,0.08)' : 'transparent' }}'; this.querySelector('span.nav-label').style.color='{{ $isActive ? '#fbb158' : 'rgba(255,255,255,0.85)' }}'; this.querySelector('span.nav-dot').style.background='{{ $isActive ? '#fbb158' : 'rgba(201,168,76,0.4)' }}'">
                                        <span class="nav-dot w-1.5 h-1.5 rounded-full flex-shrink-0 transition-colors duration-200"
                                            style="background: {{ $isActive ? '#fbb158' : 'rgba(201,168,76,0.4)' }};"></span>
                                        <span
                                            class="nav-label text-[15px] font-medium tracking-[0.06em] uppercase transition-colors duration-200"
                                            style="color: {{ $isActive ? '#fbb158; font-weight:700;' : 'rgba(255,255,255,0.85)' }}; font-family: 'Lora', Georgia, serif;">
                                            {{ $item->label }}
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </nav>

                    {{-- Decorative divider --}}
                    <div class="mx-6 my-4 flex items-center gap-3">
                        <div class="flex-1 h-px"
                            style="background: linear-gradient(90deg, rgba(201,168,76,0.0), rgba(201,168,76,0.4));">
                        </div>

                        <div class="flex-1 h-px"
                            style="background: linear-gradient(90deg, rgba(201,168,76,0.4), rgba(201,168,76,0.0));">
                        </div>
                    </div>

                    {{-- WhatsApp CTA --}}
                    @if(!empty($companySetting?->whatsapp_number))
                        <div class="px-6 mb-4">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting->whatsapp_number) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2.5 w-full py-3.5 rounded-[6px] font-bold text-[13px] tracking-[0.08em] uppercase text-white transition-all duration-200"
                                style="background: linear-gradient(135deg, #25D366, #128C7E); box-shadow: 0 4px 16px rgba(37,211,102,0.25);"
                                onmouseover="this.style.boxShadow='0 6px 24px rgba(37,211,102,0.4)'; this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.boxShadow='0 4px 16px rgba(37,211,102,0.25)'; this.style.transform='translateY(0)'">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                                </svg>{{ __('Chat on WhatsApp') }}</a>
                        </div>
                    @endif

                </div>

                {{-- ── FOOTER — Contact & Socials ── --}}
                <div class="flex-shrink-0 px-6 py-5"
                    style="border-top: 1px solid rgba(201,168,76,0.15); background: rgba(0,0,0,0.2);">
                    <p class="text-[11px] font-semibold tracking-[0.14em] uppercase mb-3"
                        style="color: rgba(201,168,76,0.6); font-family: 'Lora', Georgia, serif;">
                        {{ __('Get In Touch') }}
                    </p>
                    <div class="space-y-2.5">
                        @if(!empty($companySetting?->phone))<a href="tel:{{ $companySetting->phone }}"
                                class="flex items-center gap-2.5 group">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                    style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.2);">
                                    <x-icon name="phone" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </div>
                                <span class="text-[13px] font-light transition-colors duration-150"
                                    style="color: rgba(255,255,255,0.65); font-family: 'Lora', Georgia, serif;"
                                    onmouseover="this.style.color='#fbb158'"
                                    onmouseout="this.style.color='rgba(255,255,255,0.65)'">
                                    {{ $companySetting->phone }}
                                </span>
                            </a>
                        @endif
                        @if(!empty($companySetting?->email))<a href="mailto:{{ $companySetting->email }}"
                                class="flex items-center gap-2.5 group">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                                    style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.2);">
                                    <x-icon name="envelope" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </div>
                                <span class="text-[13px] font-light transition-colors duration-150 truncate"
                                    style="color: rgba(255,255,255,0.65); font-family: 'Lora', Georgia, serif;"
                                    onmouseover="this.style.color='#fbb158'"
                                    onmouseout="this.style.color='rgba(255,255,255,0.65)'">
                                    {{ $companySetting->email }}
                                </span>
                            </a>
                        @endif
                    </div>

                    {{-- Social icons row --}}
                    @if($companySetting?->facebook_url || $companySetting?->instagram_url || $companySetting?->youtube_url || $companySetting?->x_url || $companySetting?->tiktok_url)
                        <div class="flex items-center gap-2.5 mt-4">
                            @if($companySetting?->facebook_url)
                                <a href="{{ $companySetting->facebook_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                    style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                                    onmouseover="this.style.background='rgba(201,168,76,0.2)'; this.style.borderColor='rgba(201,168,76,0.6)'"
                                    onmouseout="this.style.background='rgba(201,168,76,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'">
                                    <x-icon name="facebook" type="solid" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </a>
                            @endif
                            @if($companySetting?->instagram_url)
                                <a href="{{ $companySetting->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                    style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                                    onmouseover="this.style.background='rgba(201,168,76,0.2)'; this.style.borderColor='rgba(201,168,76,0.6)'"
                                    onmouseout="this.style.background='rgba(201,168,76,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'">
                                    <x-icon name="instagram" type="solid" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </a>
                            @endif
                            @if($companySetting?->youtube_url)
                                <a href="{{ $companySetting->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                    style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                                    onmouseover="this.style.background='rgba(201,168,76,0.2)'; this.style.borderColor='rgba(201,168,76,0.6)'"
                                    onmouseout="this.style.background='rgba(201,168,76,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'">
                                    <x-icon name="youtube" type="solid" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </a>
                            @endif
                            @if($companySetting?->x_url)
                                <a href="{{ $companySetting->x_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                    style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                                    onmouseover="this.style.background='rgba(201,168,76,0.2)'; this.style.borderColor='rgba(201,168,76,0.6)'"
                                    onmouseout="this.style.background='rgba(201,168,76,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'">
                                    <x-icon name="x" type="solid" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </a>
                            @endif
                            @if($companySetting?->tiktok_url)
                                <a href="{{ $companySetting->tiktok_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200"
                                    style="background: rgba(201,168,76,0.08); border: 1px solid rgba(201,168,76,0.2);"
                                    onmouseover="this.style.background='rgba(201,168,76,0.2)'; this.style.borderColor='rgba(201,168,76,0.6)'"
                                    onmouseout="this.style.background='rgba(201,168,76,0.08)'; this.style.borderColor='rgba(201,168,76,0.2)'">
                                    <x-icon name="tiktok" type="solid" class="w-3.5 h-3.5 text-[#fbb158]" />
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Bottom gold accent line --}}
                <div class="h-[3px] flex-shrink-0"
                    style="background: linear-gradient(90deg, transparent, #fbb158 30%, #f0d080 60%, #fbb158 80%, transparent);">
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-950 text-white py-16 border-t border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <!-- Brand Column -->
                <div class="col-span-1 md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-6">
                        @if(!empty($companySetting?->white_logo))
                            <img src="{{ asset('storage/' . $companySetting->white_logo) }}"
                                alt="{{ $companySetting->company_name ?? config('app.name') }}"
                                class="h-8 w-auto object-contain">
                        @else
                            <div
                                class="w-8 h-8 bg-brand-primary text-white flex items-center justify-center rounded-[3px] font-bold text-lg font-brand">
                                J</div>
                        @endif
                        <span class="font-bold text-lg font-brand tracking-tight text-white">
                            {{ $companySetting?->company_name ?? config('app.name', ' ') }}
                        </span>
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        {{ __('Your premier partner for unforgettable African safaris, Kilimanjaro treks, and Zanzibar escapes.') }}
                    </p>
                </div>

                <!-- Company Links -->
                @if($footerCompany->count() > 0)
                    <div>
                        <h4 class="text-white font-bold mb-6">{{ __('Company') }}</h4>
                        <ul class="space-y-4 text-sm text-gray-400">
                            @foreach($footerCompany as $item)
                                <li>
                                    <a href="{{ $item->resolved_url }}" @if($item->is_external) target="_blank"
                                    rel="noopener noreferrer" @endif
                                        class="hover:text-brand-primary transition-colors">{{ $item->label }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Destinations Links -->
                @if($footerDestinations->count() > 0)
                    <div>
                        <h4 class="text-white font-bold mb-6">{{ __('Destinations') }}</h4>
                        <ul class="space-y-4 text-sm text-gray-400">
                            @foreach($footerDestinations as $item)
                                <li>
                                    <a href="{{ $item->resolved_url }}" @if($item->is_external) target="_blank"
                                    rel="noopener noreferrer" @endif
                                        class="hover:text-brand-primary transition-colors">{{ $item->label }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Contact Info (from CompanySettings) -->
                <div>
                    <h4 class="text-white font-bold mb-6">{{ __('Contact Us') }}</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        @if(!empty($companySetting?->address))
                            <li class="flex items-start gap-3">
                                <x-icon name="map-pin" class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" />
                                <div class="prose prose-sm prose-invert max-w-none text-gray-400 [&>p]:m-0">
                                    {!! $companySetting->address !!}
                                </div>
                            </li>
                        @endif
                        @if(!empty($companySetting?->email))
                            <li class="flex items-center gap-3">
                                <x-icon name="envelope" class="w-5 h-5 text-brand-primary shrink-0" />
                                <a href="mailto:{{ $companySetting->email }}"
                                    class="hover:text-brand-primary transition-colors">{{ $companySetting->email }}</a>
                            </li>
                        @endif
                        @if(!empty($companySetting?->phone))
                            <li class="flex items-center gap-3">
                                <x-icon name="phone" class="w-5 h-5 text-brand-primary shrink-0" />
                                <a href="tel:{{ $companySetting->phone }}"
                                    class="hover:text-brand-primary transition-colors">{{ $companySetting->phone }}</a>
                            </li>
                        @endif
                        @if(!empty($companySetting?->website))
                            <li class="flex items-center gap-3">
                                <x-icon name="globe-network" class="w-5 h-5 text-brand-primary shrink-0" />
                                <a href="{{ $companySetting->website }}" target="_blank" rel="noopener noreferrer"
                                    class="hover:text-brand-primary transition-colors">{{ preg_replace('#^https?://#', '', rtrim($companySetting->website, '/')) }}</a>
                            </li>
                        @endif


                        <li class="flex items-center gap-3">
                            @auth
                                <a href="{{ route('client.dashboard') }}"
                                    class="flex items-center gap-3 hover:text-brand-primary transition-colors"><x-icon
                                        name="user" class="w-5 h-5 text-brand-primary shrink-0" />{{ __('Client
                                                Area') }}</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex items-center gap-3 hover:text-brand-primary transition-colors"><x-icon
                                        name="user" class="w-5 h-5 text-brand-primary shrink-0" />{{ __('login') }}</a>
                            @endauth

                        </li>
                    </ul>

                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="border-t border-gray-900 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-550 text-sm">
                    © {{ date('Y') }} {{ $companySetting?->company_name ?? ' ' }}. All rights reserved.
                </p>
                <div class="flex gap-4">
                    @if($companySetting?->facebook_url)
                        <a href="{{ $companySetting->facebook_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-gray-500 hover:text-white transition-colors">
                            <span class="sr-only">{{ __('Facebook') }}</span>
                            <x-icon name="facebook" type="solid" class="w-5 h-5" />
                        </a>
                    @endif
                    @if($companySetting?->instagram_url)
                        <a href="{{ $companySetting->instagram_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-gray-500 hover:text-white transition-colors">
                            <span class="sr-only">{{ __('Instagram') }}</span>
                            <x-icon name="instagram" type="solid" class="w-5 h-5" />
                        </a>
                    @endif
                    @if($companySetting?->youtube_url)
                        <a href="{{ $companySetting->youtube_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-gray-500 hover:text-white transition-colors">
                            <span class="sr-only">{{ __('YouTube') }}</span>
                            <x-icon name="youtube" type="solid" class="w-5 h-5" />
                        </a>
                    @endif
                    @if($companySetting?->x_url)
                        <a href="{{ $companySetting->x_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-gray-500 hover:text-white transition-colors">
                            <span class="sr-only">X</span>
                            <x-icon name="x" type="solid" class="w-5 h-5" />
                        </a>
                    @endif
                    @if($companySetting?->tiktok_url)
                        <a href="{{ $companySetting->tiktok_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-gray-500 hover:text-white transition-colors">
                            <span class="sr-only">{{ __('TikTok') }}</span>
                            <x-icon name="tiktok" type="solid" class="w-5 h-5" />
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>
    @if(session('newsletter_success'))
        <div id="newsletter-toast"
            class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-4 rounded-[3px] shadow-2xl z-[9999] transition-opacity duration-500 font-medium">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>{{ session('newsletter_success') }}</p>
            </div>
        </div>
        <script>setTimeout(() => { const t = document.getElementById('newsletter-toast'); if (t) { t.style.opacity = '0'; setTimeout(() => t.remove(), 500); } }, 5000);</script>
    @endif

    @if(session('newsletter_error'))
        <div id="newsletter-toast-error"
            class="fixed bottom-6 right-6 bg-red-600 text-white px-6 py-4 rounded-[3px] shadow-2xl z-[9999] transition-opacity duration-500 font-medium max-w-sm">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>{{ session('newsletter_error') }}</p>
            </div>
        </div>
        <script>setTimeout(() => { const t = document.getElementById('newsletter-toast-error'); if (t) { t.style.opacity = '0'; setTimeout(() => t.remove(), 500); } }, 6000);</script>
    @endif

    <x-frontend.start-planning-modal />
    <x-frontend.activity-modal />

    {{-- Hero section video/iframe full-cover enforcement --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const heroSection = document.querySelector('section.hero');
            if (!heroSection) return;

            // Fix all direct child divs that act as the bg container (GrapesJS assigns random IDs)
            // Target the first child div which is always the bg container
            const firstChild = heroSection.children[0];
            if (firstChild && firstChild.tagName === 'DIV') {
                firstChild.style.setProperty('position', 'absolute', 'important');
                firstChild.style.setProperty('top', '0', 'important');
                firstChild.style.setProperty('left', '0', 'important');
                firstChild.style.setProperty('width', '100%', 'important');
                firstChild.style.setProperty('height', '100%', 'important');
                firstChild.style.setProperty('overflow', 'hidden', 'important');
                firstChild.style.setProperty('z-index', '1', 'important');
            }

            // Fix all iframes inside the hero (YouTube video cover fix)
            const iframes = heroSection.querySelectorAll('iframe');
            iframes.forEach(function (iframe) {
                iframe.removeAttribute('width');
                iframe.removeAttribute('height');
                iframe.style.setProperty('position', 'absolute', 'important');
                iframe.style.setProperty('top', '50%', 'important');
                iframe.style.setProperty('left', '50%', 'important');
                iframe.style.setProperty('transform', 'translate(-50%, -50%)', 'important');
                iframe.style.setProperty('pointer-events', 'none', 'important');
                iframe.style.setProperty('border', 'none', 'important');
                iframe.style.setProperty('z-index', '1', 'important');

                // 16:9 cover formula: ensure iframe is always landscape-sized enough to fill portrait screens
                function sizeIframe() {
                    var vw = window.innerWidth;
                    var vh = window.innerHeight;
                    var w = Math.max(vw, vh * (16 / 9));
                    var h = Math.max(vh, vw * (9 / 16));
                    iframe.style.setProperty('width', w + 'px', 'important');
                    iframe.style.setProperty('height', h + 'px', 'important');
                    iframe.style.setProperty('min-width', vw + 'px', 'important');
                    iframe.style.setProperty('min-height', vh + 'px', 'important');
                }
                sizeIframe();
                window.addEventListener('resize', sizeIframe);
            });

            // Fix all videos inside the hero
            const videos = heroSection.querySelectorAll('video');
            videos.forEach(function (video) {
                video.style.setProperty('position', 'absolute', 'important');
                video.style.setProperty('top', '0', 'important');
                video.style.setProperty('left', '0', 'important');
                video.style.setProperty('width', '100%', 'important');
                video.style.setProperty('height', '100%', 'important');
                video.style.setProperty('object-fit', 'cover', 'important');
                video.style.setProperty('pointer-events', 'none', 'important');
            });
        });
    </script>

    {{-- ── Floating WhatsApp Chat Widget ── --}}
    @php
        $waRaw = $companySetting?->whatsapp_number ?: ($companySetting?->phone ?: ' ');
        $waClean = preg_replace('/[^0-9]/', '', $waRaw);
        $companyName = $companySetting?->company_name ?? config('app.name', ' ');
        $waMessage = rawurlencode("Hello! I would like to inquire about a safari with {$companyName}.");
    @endphp

    <aside id="floating-whatsapp" aria-label="{{ __('Chat on WhatsApp') }}"
        class="fixed bottom-6 right-6 sm:bottom-8 sm:right-8 z-50 flex items-center justify-end group">

        <!-- Hover / Active Pill Tooltip -->
        <a href="https://wa.me/{{ $waClean }}?text={{ $waMessage }}" target="_blank" rel="noopener noreferrer"
            class="wa-tooltip hidden sm:inline-flex items-center gap-2 mr-3 px-4 py-2 bg-white dark:bg-[#152018] text-gray-800 dark:text-gray-100 text-xs font-semibold rounded-full shadow-[0_4px_20px_rgba(0,0,0,0.14)] border border-stone-200/80 dark:border-white/10 opacity-0 group-hover:opacity-100 translate-x-3 group-hover:translate-x-0 transition-all duration-300 pointer-events-auto hover:bg-stone-50 dark:hover:bg-[#1a281e]"
            style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            <span class="w-2 h-2 rounded-full bg-[#25D366] animate-pulse"></span>
            <span>{{ __('Chat with us on WhatsApp') }}</span>
        </a>

        <!-- Floating Icon Button -->
        <div class="relative flex items-center justify-center">
            <!-- Gentle Pulse Ring Animation -->
            <span class="absolute inset-0 rounded-full bg-[#25D366] opacity-40 animate-ping pointer-events-none"
                style="animation-duration: 2.5s;"></span>

            <a href="https://wa.me/{{ $waClean }}?text={{ $waMessage }}" target="_blank" rel="noopener noreferrer"
                aria-label="{{ __('Chat with us on WhatsApp') }}" title="{{ __('Chat with us on WhatsApp') }}"
                class="wa-btn w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-tr from-[#128C7E] via-[#25D366] to-[#25D366] text-white flex items-center justify-center shadow-[0_10px_25px_rgba(37,211,102,0.45)] hover:shadow-[0_14px_35px_rgba(37,211,102,0.65)] transform hover:scale-110 active:scale-95 transition-all duration-300 relative z-10">

                <!-- WhatsApp SVG Icon -->
                <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current drop-shadow-sm" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z" />
                </svg>

                <!-- Small Online Status Badge -->
                <span
                    class="absolute top-1 right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-white dark:border-[#121c15] rounded-full"></span>
            </a>
        </div>
    </aside>

    @include('partials.livewire-waf-bypass')
    @livewireScripts
</body>

</html>