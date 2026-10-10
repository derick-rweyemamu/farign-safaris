@extends('layouts.frontend')

@section('title', 'All Tours & Safaris |  ')

@section('content')

    {{-- ── HERO BANNER ────────────────────────────────────────────────────────────── --}}
    <section class="relative w-full h-96 md:h-[480px] overflow-hidden bg-gray-900">
        @if($companySetting?->tour_hero_image)
            <img src="{{ Storage::url($companySetting->tour_hero_image) }}" alt="Tanzania Safari Tours"
                class="absolute inset-0 w-full h-full object-cover opacity-85">
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/15 to-black/45"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <h1 class="text-4xl md:text-6xl !font-normal font-normal text-white font-brand leading-tight mb-4" style="font-weight: 400; text-shadow: 0 2px 10px rgba(0,0,0,0.6);">
                {{ __('Explore Our Tours & Safaris') }}
            </h1>
            <p class="text-white/90 text-base md:text-lg max-w-2xl" style="text-shadow: 0 1px 6px rgba(0,0,0,0.6);">
                {{ __('Hand-crafted African adventures — from Serengeti game drives to Kilimanjaro summits and Zanzibar beach escapes.') }}
            </p>
        </div>
    </section>

    @include('frontend.partials.tours-full-page')

@endsection