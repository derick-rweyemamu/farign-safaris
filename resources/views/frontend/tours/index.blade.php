@extends('layouts.frontend')

@section('title', 'All Tours & Safaris |  ')

@section('content')

    {{-- ── HERO BANNER ────────────────────────────────────────────────────────────── --}}
    <section class="relative w-full h-72 md:h-96 overflow-hidden bg-gray-950">
        @if($companySetting?->tour_hero_image)
            <img src="{{ Storage::url($companySetting->tour_hero_image) }}" alt="Tanzania Safari Tours"
                class="absolute inset-0 w-full h-full object-cover opacity-50">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-gray-950/60 via-gray-950/30 to-gray-950/80"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <span
                class="inline-flex items-center px-4 py-1.5 rounded-md bg-brand-primary/20 border border-brand-primary/40 text-brand-primary text-xs font-bold uppercase tracking-widest mb-4">
                {{ __('Discover Africa') }}
            </span>
            <h1 class="text-4xl md:text-6xl font-bold text-white font-brand leading-tight mb-4">
                {{ __('Explore Our Tours & Safaris') }}
            </h1>
            <p class="text-gray-300 text-base md:text-lg max-w-2xl">
                {{ __('Hand-crafted African adventures — from Serengeti game drives to Kilimanjaro summits and Zanzibar beach escapes.') }}
            </p>
        </div>
    </section>

    @include('frontend.partials.tours-full-page')

@endsection