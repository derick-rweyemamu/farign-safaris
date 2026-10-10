@extends('layouts.frontend')

@section('title', 'Destinations — ' . config('app.name', ' '))

@section('content')

    {{-- ── HERO BANNER ────────────────────────────────────────────────────────────── --}}
    <section class="relative w-full h-96 md:h-[480px] overflow-hidden bg-gray-900">
        @if(app(\App\Settings\GeneralSetting::class)?->tour_hero_image)
            <img src="{{ Storage::url(app(\App\Settings\GeneralSetting::class)->tour_hero_image) }}"
                alt="Tanzania Safari Destinations" class="absolute inset-0 w-full h-full object-cover opacity-85">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/15 to-black/45"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <h1 class="text-4xl md:text-6xl !font-normal font-normal text-white font-brand leading-tight mb-4" style="font-weight: 400; text-shadow: 0 2px 10px rgba(0,0,0,0.6);">
                {{ __('Explore Our Destinations') }}
            </h1>
            <p class="text-white/90 text-base md:text-lg max-w-2xl" style="text-shadow: 0 1px 6px rgba(0,0,0,0.6);">
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