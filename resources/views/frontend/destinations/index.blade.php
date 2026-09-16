@extends('layouts.frontend')

@section('title', 'Destinations — ' . config('app.name', ' '))

@section('content')

    {{-- ── HERO BANNER ────────────────────────────────────────────────────────────── --}}
    <section class="relative w-full h-72 md:h-96 overflow-hidden bg-gray-950">
        @if(app(\App\Settings\GeneralSetting::class)?->tour_hero_image)
            <img src="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->tour_hero_image) }}"
                alt="Tanzania Safari Destinations" class="absolute inset-0 w-full h-full object-cover opacity-50">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/60 via-gray-950/30 to-gray-950/80"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <span
                class="inline-flex items-center px-4 py-1.5 rounded-full bg-brand-primary/20 border border-brand-primary/40 text-brand-primary text-xs font-bold uppercase tracking-widest mb-4">
                {{ __('Discover Africa') }}
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white font-brand leading-tight mb-4">
                {{ __('Explore Our Destinations') }}
            </h1>
            <p class="text-gray-300 text-base md:text-lg max-w-2xl">
                {{ __('From the vast plains of the Serengeti to the misty heights of Kilimanjaro — discover Africa\'s most extraordinary places.') }}
            </p>
        </div>
    </section>

    {{-- Destinations Listing with Filters --}}
    <div class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <livewire:frontend.destinations-index />
        </div>
    </div>

@endsection