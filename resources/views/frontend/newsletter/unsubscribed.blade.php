@extends('layouts.frontend')

@section('title', 'Unsubscribed - ' . config('app.name', ' '))

@section('content')
    <div style="min-height: 50vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div
            style="text-align: center; max-width: 500px; background: white; padding: 3rem; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <x-icon name="check-circle" style="width: 64px; height: 64px; margin: 0 auto 1.5rem; color: #10B981;" />
            <h2 style="font-size: 1.5rem; font-weight: bold; color: #111827; margin-bottom: 1rem;">
                {{ __('Successfully Unsubscribed') }}</h2>
            <p style="color: #6B7280; margin-bottom: 2rem;">
                {{ __('You have been successfully removed from our newsletter list. We\'re sorry to see you go!') }}</p>
            <a href="{{ route('home') }}" class="btn-primary"
                style="display: inline-block;">{{ __('Return to Homepage') }}</a>
        </div>
    </div>
@endsection